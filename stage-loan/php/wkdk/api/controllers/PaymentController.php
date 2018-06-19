<?php
namespace api\controllers;

use api\bases\ApiController;
use common\bases\CommonService;
use common\extend\payment\lianlianpay\LianlianpayNotify;
use common\extend\sms\AlidayuSms;
use common\extend\sms\MsgTemplate;
use common\models\LoanLogModel;
use common\models\LoanModel;
use common\models\MobileLogModel;
use common\models\PayLogModel;
use common\models\UserModel;
use common\services\LoanService;
use yii\db\Exception;
use yii\helpers\Json;
use Yii;

class PaymentController extends ApiController
{
    /**
     * 绑定访问控制过滤器
     *
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        // 访问控制
        $behaviors['access']['rules'] = [
            // 允许访客用户访问的Action
            [
                'actions' => ['lianlianpay-notify', 'lianlian-auth-pay-notify', 'lianlian-bankcard-repayment-notify'],
                'allow' => true,
                'roles' => ['?'],
            ],
            // 其它的Action必须要授权用户才可访问
            [
                'allow' => true,
                'roles' => ['@'],
            ],
        ];
        return $behaviors;
    }
    
    /**
     * 后台放款异步通知
     */
    public function actionLianlianpayNotify()
    {
        $lianlianpayNotify = new LianlianpayNotify();
        $result = file_get_contents("php://input");
        Yii::info('连连支付-实时支付异步通知回调：' . $result, 'lianlianpay'); // 添加接口访问日志

        // 接收到数据
        if ($result) {
            $data = Json::decode($result);
            $verifyResult = $lianlianpayNotify->verifyCallback($data); // 验签
            if (!$verifyResult) {
                Yii::info('连连支付-实时支付通知回调验签失败:' . $result, 'lianlianpay');
                $im = Yii::$app->companyim;
                $im->sendNotice('sys_error', $im::TECH_DEPT_CHAT_ID, '连连支付-实时支付通知回调验签失败');
                exit();
            }

            $resultPay = strtolower($data['result_pay']); // 将返回的支付结果，转化为小写
            if ($resultPay == PayLogModel::STATE_SUCCESS) { // 付款成功
                // 重复通知的处理，若状态已经付款成功了，此处不作任何处理
                $payLog = PayLogModel::findPayLogByNoOrder($data['no_order']);
                if (!$payLog) {
                    Yii::error('连连支付-实时支付通知回调错误:商户订单号' . $data['no_order'] . '不存在', 'lianlianpay');
                    exit();
                }

                // 订单的支付状态非已成功状态
                if ($payLog->state != PayLogModel::STATE_SUCCESS) {
                    $log['state'] = $resultPay;
                    $log['oid_paybill'] = $data['oid_paybill']; // 连连支付支付单号
                    $log['settle_date'] = date('Y-m-d H:i:s', strtotime($data['settle_date'])); // 账务日期
                    PayLogModel::updatePayLogByNoOrder($data['no_order'], $log); // 返回当前
                    LoanService::updateCorrelationAfterGrantSuccess($payLog->loan_id, $data['money_order'], $payLog->user_id); // 放款成功后 相关的关联数据变更
                }

                $im = Yii::$app->companyim;
                $im->sendTextToChatGroup($im::SENDER_XIAOBAI, $im::COMPANY_CHAT_ID, sprintf('商户放款成功！用户ID(%s),金额(%s)！', $payLog->user_id, $data['money_order']));
            }

            // 付款退款
            if ($resultPay == PayLogModel::STATE_CANCEL) {
                // 重复通知的处理，若状态已经付款成功了，此处不作任何处理
                $payLog = PayLogModel::findPayLogByNoOrder($data['no_order']);
                if (!$payLog) {
                    Yii::error('连连支付-实时支付通知回调错误:商户订单号' . $data['no_order'] . '不存在', 'lianlianpay');
                    exit();
                }

                // 订单的支付状态非已退款状态
                if ($payLog->state != PayLogModel::STATE_CANCEL) {
                    $log['state'] = $resultPay;
                    $loanId = PayLogModel::updatePayLogByNoOrder($data['no_order'], $log); // 返回当前
                    // 发生退款时，需将借款记录的金额变更回去，状态变更回 复审成功
                    if ($loanId) {
                        $loan['arrival_amount'] = 0.00; // 需变更的到账金额
                        $loan['state'] = LoanModel::STATE_REVIEW_SUCCESS;
                        LoanModel::updateActiveLoanById($loanId, $loan);
                        LoanLogModel::delAppointedLoanLog($loanId);// 删除还款中日志
                    }
                }

                $im = Yii::$app->companyim;
                $im->sendTextToChatGroup($im::SENDER_XIAOBAI, $im::COMPANY_CHAT_ID, sprintf('商户放款失败，交易状态为退款！用户ID(%s),金额(%s)！', $payLog->user_id, $data['money_order']));
            }

            // 付款失败
            if ($resultPay == PayLogModel::STATE_FAILURE || $resultPay == PayLogModel::STATE_CLOSED) {
                $log['state'] = $resultPay;
                if ($data['info_order']) {
                    $log['info_order'] = $data['info_order']; // 订单描述_订单失败原因
                }
                PayLogModel::updatePayLogByNoOrder($data['no_order'], $log); // 返回当前


                $payLog = PayLogModel::findPayLogByNoOrder($data['no_order']); // 查询当前的支付订单信息
                $user = UserModel::findUserById($payLog->user_id); // 获取用户信息
                $service = new CommonService();
                $type = MobileLogModel::TYPE_LOAN_NOTICE; // 放款通知
                $service->sendSmsCode(AlidayuSms::Sign, MsgTemplate::LOAN_FAILURE, $user->mobile, [], $type); // 放款失败通知

                $im = Yii::$app->companyim;
                $im->sendTextToChatGroup($im::SENDER_XIAOBAI, $im::COMPANY_CHAT_ID, sprintf('商户放款失败，交易状态为失败！用户ID(%s),金额(%s)！', $payLog->user_id, $data['money_order']));
            }

            return Json::encode([
                'ret_code' => '0000',
                'ret_msg' => '交易成功'
            ]);
        }
        Yii::info('连连支付-实时支付通知回调未成功接收数据', 'lianlianpay');
    }

    /**
     * 还款支付成功通知处理（成功才通知）
     */
    public function actionLianlianAuthPayNotify()
    {
        $lianlianpayNotify = new LianlianpayNotify();
        $result = file_get_contents("php://input");
        Yii::info('连连支付-认证支付异步通知回调：' . $result, 'lianlianpay'); // 添加接口访问日志

        if ($result) { // 成功状态
            $data = Json::decode($result);

            $verifyResult = $lianlianpayNotify->verifyCallback($data); // 验签
            if (!$verifyResult) {
                Yii::info('连连支付-用户认证支付通知回调验签失败:' . $result, 'lianlianpay');
                $im = Yii::$app->companyim;
                $im->sendNotice('sys_error', $im::TECH_DEPT_CHAT_ID, '连连支付-用户认证支付通知回调验签失败');
                exit();
            }
            
            //验证该订单情况是否存在，存在不做任何处理，不存在，存入支付日志，并更改loan表对应的记录信息
            $payLog = PayLogModel::findPayLogByNoOrder($data['no_order']);
            if (!$payLog) {
                Yii::error('连连支付-用户认证支付回调失败: 查无此用户订单记录' . $result, 'lianlianpay');
                exit();
            }

            // 订单未成功，需处理数据；订单已成功，不做处理
            if ($payLog->state != PayLogModel::STATE_SUCCESS) {
                try {
                    // 更改交易记录的状态
                    $log = [
                        'state' => strtolower($data['result_pay']), // 变更状态
                        'oid_paybill' => $data['oid_paybill'], // 连连支付支付单号
                        'info_order' => $data['info_order'] ?? '', // 订单描述
                        'settle_date' => $data['settle_date'] ?? '', // 清算日期
                        'bank_code' => $data['bank_code'] ?? '', // 银行编号
                        'card_no' => $data['card_no'] ?? '', // 银行卡号
                        'no_agree' => $data['no_agree'] ?? '', // 银通签约的协议编号
                    ];

                    PayLogModel::updatePayLogById($payLog->id, $log);

                    // 更改借款信息中的 已还金额,若已还款金额 = 申请额度 + 借款息费 ，变更借款状态为 已还完
                    $loan = LoanModel::findUserLoan($payLog->user_id, $payLog->loan_id); // 获取当前用户有效订单信息
                    $repayedAmount = (float)($loan->repayed_amount + $payLog->money_order); // 变更已还款金额
                    LoanService::updateCorrelationAfterRepaySuccess($loan->id, $payLog->money_order, $repayedAmount, $payLog->user_id, $payLog->plan_id, $payLog->plan_detail);

                    $im = Yii::$app->companyim;
                    $im->sendTextToChatGroup($im::SENDER_XIAOBAI, $im::COMPANY_CHAT_ID, sprintf('用户还款成功！用户ID(%s),金额(%s)！', $payLog->user_id, $data['money_order']));

                }  catch (Exception $e) {
                    Yii::error($e);
                    exit();
                }
            }

            return Json::encode([
                'ret_code' => '0000',
                'ret_msg' => '交易成功'
            ]);
        }
    }

    /**
     * 银行卡还款扣款异步通知
     */
    public function actionLianlianBankcardRepaymentNotify()
    {
        $lianlianpayNotify = new LianlianpayNotify();
        $result = file_get_contents("php://input");
        Yii::info('连连支付-银行卡还款扣款异步通知回调：' . $result, 'lianlianpay'); // 添加接口访问日志

        if ($result) { // 成功状态
            $data = Json::decode($result);

            $verifyResult = $lianlianpayNotify->verifyCallback($data); // 验签
            if (!$verifyResult) {
                Yii::info('连连支付-银行卡还款扣款通知回调验签失败:' . $result, 'lianlianpay');
                $im = Yii::$app->companyim;
                $im->sendNotice('sys_error', $im::TECH_DEPT_CHAT_ID, '连连支付-银行卡还款扣款通知回调验签失败');
                exit();
            }

            //验证该订单情况是否存在，存在不做任何处理，不存在，存入支付日志，并更改loan表对应的记录信息
            $payLog = PayLogModel::findPayLogByNoOrder($data['no_order']);
            if (!$payLog) {
                Yii::error('连连支付-银行卡还款扣款回调失败: 查无此用户订单记录' . $result, 'lianlianpay');
                exit();
            }

            // 订单未成功，需处理数据；订单已成功，不做处理
            if ($payLog->state != PayLogModel::STATE_SUCCESS) {
                try {
                    // 更改交易记录的状态
                    $log = [
                        'state' => strtolower($data['result_pay']), // 变更状态
                        'oid_paybill' => $data['oid_paybill'], // 连连支付支付单号
                        'info_order' => $data['info_order'] ?? '', // 订单描述
                        'settle_date' => $data['settle_date'] ?? '', // 清算日期
                    ];

                    PayLogModel::updatePayLogById($payLog->id, $log);

                    // 更改借款信息中的 已还金额,若已还款金额 = 申请额度 + 借款息费 ，变更借款状态为 已还完
                    $loan = LoanModel::findUserLoan($payLog->user_id, $payLog->loan_id); // 获取当前用户有效订单信息
                    $repayedAmount = (float)($loan->repayed_amount + $payLog->money_order); // 变更已还款金额
                    LoanService::updateCorrelationAfterRepaySuccess($loan->id, $payLog->money_order, $repayedAmount, $payLog->user_id, $payLog->plan_id, $payLog->plan_detail);

                    $im = Yii::$app->companyim;
                    $im->sendTextToChatGroup($im::SENDER_XIAOBAI, $im::COMPANY_CHAT_ID, sprintf('银行还款扣款成功！用户ID(%s),金额(%s)！', $payLog->user_id, $repayedAmount));

                }  catch (Exception $e) {
                    Yii::error($e);
                    exit();
                }
            }

            return Json::encode([
                'ret_code' => '0000',
                'ret_msg' => '交易成功'
            ]);
        }
    }
}