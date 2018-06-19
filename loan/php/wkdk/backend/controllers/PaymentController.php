<?php
namespace backend\controllers;

use backend\bases\BackendController;
use common\extend\payment\lianlianpay\LianlianpayApi;
use common\extend\payment\lianlianpay\LianlianpayConfig;
use common\extend\payment\lianlianpay\LianlianpayNotify;
use common\extend\payment\lianlianpay\LianlianpayUtils;
use common\models\LoanLogModel;
use common\models\LoanModel;
use common\models\PayLog;
use common\models\PayLogModel;
use common\services\CurlService;
use common\services\LoanService;
use Yii;
use yii\helpers\Json;

class PaymentController extends BackendController
{
    private function lianlianpayInit()
    {
        $lianlianConfig = LianlianpayConfig::getConfig(); // 获取连连支付基础参数配置信息
        //构造要请求的参数数组
        $params = [
            "oid_partner" => $lianlianConfig['oid_partner'],
            "api_version" => '1.0', // 版本号，输入当前版本号1.0
            "sign_type" => $lianlianConfig['sign_type'],
        ]; // 最基本的系统参数，如商户编号、版本号、支付签名方式等
        return $params;
    }
    /**
     * 实时支付API（用户放款）
     */
    public function actionLianlianPayment()
    {
        $request = Yii::$app->request;
        $loanId = $request->post('loan_id'); // 借款ID

        $loan = LoanModel::findLoanById($loanId); // 当前借款的相关明细
        if (!$loan) {
            return Json::encode([ 'status' => self::STATUS_FAILURE, 'error_message' => '参数错误' ]);
        }
        if (!$loan->userBank || !$loan->userBank->agreeno) {
            return Json::encode([ 'status' => self::STATUS_FAILURE, 'error_message' => '用户尚未完成银行卡签约，请先认证后继续' ]);
        }
        $lianlianConfig = LianlianpayConfig::getConfig(); // 获取连连支付基础参数配置信息

        $watingState = [ PayLogModel::STATE_APPLY, PayLogModel::STATE_CHECK, PayLogModel::STATE_PROCESSING ]; // 需等待处理完成的订单, 中间态订单
        $isReuseFlag = false; // 标识是否存在可重复提交的支付记录  true-表示存在可重复提交记录 false-表示不存在（即需要重新添加支付记录） 默认值【false】
        $paylog = PayLogModel::findActivePayLog($loan->user_id, $loanId, PayLogModel::TYPE_REALTIMEPAY); // 查询该用户的平台放款有效支付记录
        if ($paylog && $paylog->state == PayLogModel::STATE_PENDING) { // 存在记录，且状态为待处理状态
            $isReuseFlag = true; // 该支付单号可重复使用
        } else if ($paylog && in_array($paylog->state, $watingState)) {
            return Json::encode([
                "status" => self::STATUS_FAILURE,
                "error_message" => '已存在正在处理的订单，请等待完成后继续',
            ]);
        }

        $params = $this->lianlianpayInit();
        $orderNo = $isReuseFlag ? $paylog->no_order : LoanService::buildOrderCode($loan->user_id); // 商户系统唯一标识该付款的流水号
        $params['no_order'] = $orderNo; // 商户系统唯一标识该付款的流水号
        $params['dt_order'] = date('YmdHis'); // 商户付款时间
        $params['money_order'] = (float)$loan->quota; // 付款金额，单位元，精确到小数点后两位
        $params['card_no'] = $loan->userBank->bank_no; // 收款银行帐号
        $params['acct_name'] = $loan->userBank->bank_user; // 收款人姓名
        $params['bank_name'] = $loan->userBank->bank_name; // 收款银行名称
        $params['info_order'] = LianlianpayApi::APP_ORDER_DES; // 订单描述（付款用途）
        $params['flag_card'] = LianlianpayApi::FLAG_CARD_TO_PRIVATE; // 对公对私标识 0-对私 1-对公
        $params['notify_url'] = Yii::$app->params['llpay_payment_notify'];; // 服务器异步通知地址
        $params['memo'] = LianlianpayApi::PATMENT_MEMO; // 收款备注，传递至银行

        $lianlianpayApi = new LianlianpayApi($lianlianConfig);
        $requestPara = $lianlianpayApi->buildRequestPara($params); // 返回所有的参数

        $lianlianPayUtil = new LianlianpayUtils();
        $payLoad = $lianlianPayUtil->ll_encrypt(json_encode($requestPara), $lianlianConfig['lianlian_public_key_path']);

        $data = [
            'pay_load' => $payLoad, // 请求参数加密
            'oid_partner' => trim($params['oid_partner'])
        ];
        if (!$isReuseFlag) { // 订单不存在 或 不可重复利用时
            // 将订单请求保存至数据库
            $payLogParams['no_order'] = $orderNo;
            $payLogParams['info_order'] = LianlianpayApi::APP_ORDER_DES;
            $payLogParams['user_id'] = $loan->user_id; // 用户Id
            $payLogParams['loan_id'] = (integer)$loanId; // 借款Id
            $payLogParams['pay_type'] = LianlianpayApi::PAY_TYPE_PAYMENT; // 支付类型 2-平台放款
            $payLogParams['dt_order'] = date('Y-m-d H:i:s', strtotime($params['dt_order'])); // 订单时间
            $payLogParams['money_order'] = (float)$params['money_order']; // 付款金额
            PayLogModel::addPayLog($payLogParams); // 添加订单
        }

        $respose= $lianlianpayApi->buildRequestJSON($data, LianlianpayApi::PATMENT_REQUEST_URL); // 调用接口
        $result = Json::decode($respose);
        if ($respose && ($result['ret_code']) == LianlianpayApi::REQUEST_SUCCESS) { // 有返回值，且返回code为0000
            $lianlianpayNotify = new LianlianpayNotify();
            $queryVerifyResult = $lianlianpayNotify->verifyCallback($result); // 验签
            if (!$queryVerifyResult) {
                return Json::encode([
                    "status" => self::STATUS_FAILURE,
                    "error_message" => '数据错误',
                ]);
            }
            // 更改数据库状态
            $noOrder = $result['no_order']; // 商户付款流水号
            $updateParam['oid_paybill'] = $result['oid_paybill']; // 连连支付订单号
            $updateParam['confirm_code'] = isset($result['confirm_code']) ? $result['confirm_code'] : ""; // 验证码
            $updateParam['state']  = PayLogModel::STATE_APPLY; // 连连支付状态
            $loanId = PayLogModel::updatePayLogByNoOrder($noOrder, $updateParam); // 返回当前
            LoanModel::updateLendingStateById($loanId);  // 变更用户当前有效订单 状态为放款中
            LoanService::generateGrantingLog($loanId, $params['card_no']);   // 添加放款日志

            return Json::encode([
                'status' => self::STATUS_SUCCESS,
                'error_message' => '放款申请成功'
            ]);
        } else if(in_array($result['ret_code'], LianlianpayApi::NEED_QUERY_CODE_ARR)) { // 其他状态，查询订单状态
            $lianlianApi = new  LianlianpayApi($lianlianConfig);
            $queryParams = $this->lianlianpayInit();
            $queryParams['no_order'] = $params['no_order']; //商户付款流水号
            $queryRespose = $lianlianApi->queryPayment($queryParams);

            if ($queryRespose) { // 查询的结果
                $queryResult = Json::decode($queryRespose);

                if ($queryResult['ret_code'] == LianlianpayApi::REQUEST_SUCCESS) { // 成功
                    $lianlianpayNotify = new LianlianpayNotify();
                    $queryVerifyResult = $lianlianpayNotify->verifyCallback($queryResult); // 验签
                    if (!$queryVerifyResult) {
                        Yii::error('连连支付-实时支付查询订单验签失败：' . $queryRespose, 'lianlianpay');

                        $im = Yii::$app->companyim;
                        $im->sendNotice('sys_error', $im::TECH_DEPT_CHAT_ID, '放款查询订单验签失败, 订单号: ' . $queryParams['no_order']);

                        return Json::encode([
                            "status" => self::STATUS_FAILURE,
                            "error_message" => '数据错误',
                        ]);
                    }

                    // 查询到了支付订单状态
                    $resultPay = strtolower($queryResult['result_pay']);
                    if ($resultPay == PayLogModel::STATE_APPLY || $resultPay == PayLogModel::STATE_CHECK || $resultPay == PayLogModel::STATE_PROCESSING) { // 付款申请、复核申请、付款处理中
                        $param['state'] = $resultPay;
                        $loanId = PayLogModel::updatePayLogByNoOrder($queryResult['no_order'], $param); // 返回当前
                        LoanModel::updateLendingStateById($loanId);  // 变更用户当前有效订单 状态为放款中
                        LoanService::generateGrantingLog($loanId, $params['card_no']);   // 添加放款日志

                        return Json::encode([
                            'status' => self::STATUS_SUCCESS,
                            'error_message' => '交易正在处理中'
                        ]);
                    }
                    if ($resultPay == PayLogModel::STATE_SUCCESS) { // 付款成功
                        // 重复通知的处理，若状态已经付款成功了，此处不作任何处理
                        $payLog = PayLogModel::findPayLogByNoOrder($queryResult['no_order']);
                        if (!$payLog) {
                            Yii::error('连连支付-实时支付查询订单错误:商户订单号' . $data['no_order'] . '不存在', 'lianlianpay');
                            exit();
                        }

                        if ($payLog->state != PayLogModel::STATE_SUCCESS) {
                            $param['state'] = $resultPay;
                            $param['oid_paybill'] = $queryResult['oid_paybill']; // 连连支付支付单号
                            $param['settle_date'] = date('Y-m-d H:i:s', strtotime($queryResult['settle_date'])); // 账务日期
                            $loanId = PayLogModel::updatePayLogByNoOrder($queryResult['no_order'], $param); // 返回当前

                            LoanService::updateCorrelationAfterGrantSuccess($loanId, $params['money_order'], $payLog->user_id); // 放款成功后 相关的关联数据变更
                        } // 订单的支付状态非已成功状态

                        return Json::encode([
                            'status' => self::STATUS_SUCCESS,
                            'error_message' => '付款成功'
                        ]);
                   } // end-if 付款成功
                   if ($resultPay == PayLogModel::STATE_CANCEL) { // 付款退款
                       // 重复通知的处理，若状态已经付款成功了，此处不作任何处理
                        $payLog = PayLogModel::findPayLogByNoOrder($queryResult['no_order']);
                        if (!$payLog) {
                            Yii::error('连连支付-实时支付查询订单错误:商户订单号' . $data['no_order'] . '不存在', 'lianlianpay');
                            exit();
                        }

                        if ($payLog->state != PayLogModel::STATE_CANCEL) {
                           $param['state'] = $resultPay;
                           $loanId = PayLogModel::updatePayLogByNoOrder($queryResult['no_order'], $param); // 返回当前
                           // 发生退款时，需将借款记录的金额变更回去，状态变更回 复审成功
                           $cancel_params['arrival_amount'] = 0.00; // 需变更的到账金额
                           $cancel_params['state'] = LoanModel::STATE_REVIEW_SUCCESS;
                           LoanModel::updateActiveLoanById($loanId, $cancel_params);
                           // 删除还款中日志
                           $re = LoanLogModel::delAppointedLoanLog($loanId);
                        } // 订单的支付状态非已退款状态

                       $im = Yii::$app->companyim;
                       $im->sendTextToChatGroup($im::SENDER_XIAOBAI, $im::COMPANY_CHAT_ID, '放款提醒：订单 '. $queryResult['no_order'] . ' 已退款' );

                       return Json::encode([
                           'status' => self::STATUS_FAILURE,
                           'error_message' => '该笔支付已退款'
                       ]);
                   } // end-if 付款退款

                   if ($resultPay == PayLogModel::STATE_FAILURE || $resultPay == PayLogModel::STATE_CLOSED) { // 付款失败
                       $param['state'] = $resultPay;
                       if ($queryResult['info_order']) {
                           $param['info_order'] = $queryResult['info_order']; // 订单描述
                       }
                       PayLogModel::updatePayLogByNoOrder($queryResult['no_order'], $param); // 返回当前

                       return Json::encode([
                           'status' => self::STATUS_FAILURE,
                           'error_message' => '支付失败或交易已关闭'. (isset(explode('_', $param['info_order'])[1]) ? '。  失败原因：'. explode('_', $param['info_order'])[1] : '')
                       ]);
                   } // end-if 付款失败

                   Yii::error('连连支付-实时支付查询订单未能查询到订单支付状态：' . $queryRespose, 'lianlianpay');
                   return Json::encode([
                       'status' => self::STATUS_FAILURE,
                       'error_message' => '未查询到订单支付状态'
                   ]);
               } else { // 查询订单时的结果非 成功0000 状态
                   Yii::error('连连支付-实时支付查询订单失败：' . $queryRespose, 'lianlianpay');
                   return Json::encode([
                       'status' => self::STATUS_FAILURE,
                       'error_message' => $queryResult['ret_msg']
                   ]);
               } // end-else $queryResult['ret_code'] != '0000' 查询请求返回值
            } // end-if $queryRespose  查询的结果

            Yii::error('连连支付-实时支付查询订单未成功接收数据', 'lianlianpay');
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                'error_message' => '无返回值'
            ]);
        } else if(in_array($result['ret_code'], LianlianpayApi::NEED_CONFIRM_CODE_ARR)){ // 需要确认订单的返回码
            $checkParams['state'] = PayLogModel::STATE_CHECK; // 审核状态
            $checkParams['confirm_code'] = $result['confirm_code'] ?? ''; // 验证码
            $loanId = PayLogModel::updatePayLogByNoOrder($params['no_order'], $checkParams); // 返回当前
            LoanModel::updateLendingStateById($loanId);  // 变更用户当前有效订单 状态为放款中
            LoanService::generateGrantingLog($loanId, $params['card_no']);   // 添加放款日志

            Yii::info('连连支付-实时支付需要人工审核确认：' . $respose, 'lianlianpay');

            $im = Yii::$app->companyim;
            $im->sendTextToChatGroup($im::SENDER_XIAOBAI, $im::COMPANY_CHAT_ID, '放款提醒：放款申请订单 '. $params['no_order'] . ' 为疑似重复订单，请登录后台人工审核确认' );

            return Json::encode([
                'status' => self::STATUS_FAILURE,
                'error_message' => $result['ret_msg']
            ]);
        } else {
          Yii::info('连连支付付款失败-code:' . $result['ret_code'] . 'msg:' . $result['ret_msg'] . 'params:' . Json::encode($requestPara), 'lianlianpay');
          return Json::encode([
              'status' => self::STATUS_FAILURE,
              'error_message' => $result['ret_msg']
          ]);
        }// end-else $result == NULL 支付请求无返回值
    }

    /**
     * 连连付款申请待确认列表
     */
    public function actionLianlianConfirmList()
    {
        $request = Yii::$app->request;
        $limit = $request->get('limit', 20);
        $offset = $request->get('offset', 0);

        // 查询所有待确认的放款支付记录
        $result = PayLogModel::findAllNeedConfirmPayLog($offset, $limit);

        $count = PayLog::find()->with('user')->with('userBank')
            ->where(['pay_type' => PayLogModel::TYPE_REALTIMEPAY])
            ->andWhere(['pay_log.state' => PayLogModel::STATE_CHECK])
            ->count();
        $data = [];
        foreach($result as $v) {
            if (!$v->userBank || !$v->userBank->agreeno) {
                return Json::encode([ 'status' => self::STATUS_FAILURE, 'error_message' => '用户尚未完成银行卡签约，请先认证后继续' ]);
            }
            $data[] = [
                'id' => $v->id,
                'user_name' => $v->user->real_name, // 用户姓名
                'no_order' => $v->no_order, // 商户唯一订单流水号
                'money_order' => $v->money_order, // 交易金额
                'info_order' => $v->info_order, // 订单描述
                'created_at' => $v->created_at, // 下单时间
                'bank_name' => $v->userBank->bank_name, //交易银行名称
                'bank_no' => $v->userBank->bank_no, // 银行卡号
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'results' => $data,
            'count' => (int)$count
        ]);

    }

    /**
     * 连连实时支付借款确认
     */
    public function actionLianlianPaymentConfirm()
    {
        $request = Yii::$app->request;
        $payLogId = $request->post('payLog_id', 0); // 支付记录Id
        if (!$payLogId) {
            return Json::encode([ 'status' => self::STATUS_FAILURE, 'error_message' => '参数错误' ]);
        }
        $payLog = PayLogModel::findPayLogById($payLogId); // 按ID 查询到支付记录
        if (!$payLog) {
            return Json::encode([ 'status' => self::STATUS_FAILURE, 'error_message' => '未查询到相关支付信息，请检查参数是否正确' ]);
        }

        $requestParams = $this->lianlianpayInit();
        $requestParams['no_order'] = $payLog->no_order; // 商户唯一流水订单号
        $requestParams['confirm_code'] = $payLog->confirm_code; // 确认付款验证码
        $requestParams['notify_url'] = Yii::$app->params['llpay_payment_notify']; // 服务器一部通知地址

        $lianlianConfig = LianlianpayConfig::getConfig(); // 获取连连支付基础参数配置信息
        $lianlianpayApi = new LianlianpayApi($lianlianConfig);
        $requestPara = $lianlianpayApi->buildRequestPara($requestParams); // 返回所有的参数

        $lianlianPayUtil = new LianlianpayUtils();
        $payLoad = $lianlianPayUtil->ll_encrypt(json_encode($requestPara), $lianlianConfig['lianlian_public_key_path']);

        $data = [
            'pay_load' => $payLoad, // 请求参数加密
            'oid_partner' => trim($requestParams['oid_partner'])
        ];
        $respose= $lianlianpayApi->buildRequestJSON($data, LianlianpayApi::PATMENT_CONFIRM_URL); // 调用接口

        $result = Json::decode($respose);
        if ($result) {
            if ($result['ret_code'] == LianlianpayApi::REQUEST_SUCCESS) { // 表示付款申请确认成功
                $lianlianpayNotify = new LianlianpayNotify();
                $queryVerifyResult = $lianlianpayNotify->verifyCallback($result); // 验签
                if (!$queryVerifyResult) {
                    return Json::encode([
                        "status" => self::STATUS_FAILURE,
                        "error_message" => '数据错误',
                    ]);
                }
                return Json::encode([
                    'status' => self::STATUS_SUCCESS,
                    'error_message' => '放款申请成功'
                ]);
            } else {
                Yii::info('连连支付确认付款失败-code:' . $result['ret_code'] . 'msg:' . $result['ret_msg'] . 'params:' . Json::encode($requestPara), 'lianlianpay');
                return Json::encode([
                    'status' => self::STATUS_FAILURE,
                    'error_message' => $result['ret_msg']
                ]);
            } // if ret_code != 0000  付款确认申请不成功
        } else {
            Yii::info('连连支付确认付款未成功接收返回数据', 'lianlianpay');
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                'error_message' => '无返回值'
            ]);
        }
    }

}