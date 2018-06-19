<?php
namespace console\controllers;

use common\extend\payment\lianlianpay\LianlianpayApi;
use common\extend\payment\lianlianpay\LianlianpayConfig;
use common\models\Loan;
use common\models\LoanModel;
use common\models\PayLogModel;
use common\services\LoanService;
use yii\console\Controller;
use Yii;
use yii\helpers\Json;

class LianlianController extends Controller
{
    private $lianlianpayConfig; // 连连支付配置
    const API_PAY_TYPE_AUTH = 'D'; // 支付方式 D-认证支付（借记卡）
    const API_CONTRACT_TYPE = '悟空小贷'; // 商户名称
    const API_CONTACT_WAY = '4001789698'; // 支付方式 D-认证支付（借记卡）

    private function lianlianpayInit()
    {
        $this->lianlianpayConfig = LianlianpayConfig::getConfig();
        //构造要请求的参数数组，无需改动
        $parameter = [
            "oid_partner" => $this->lianlianpayConfig['oid_partner'],
            "api_version" => '1.0',
            "sign_type" => $this->lianlianpayConfig['sign_type'],
        ];

        return $parameter;
    }

    /**
     * 借款到期检查
     */
    public function actionLianlianInstallment()
    {
        Yii::info('系统执行借款到期检查@' . date('Y-m-d H:i:s'), 'lianlianpay');
        $today = date('Y-m-d');
        $comeDueLoans = Loan::find()->with('user')
                                    ->with('userBank')
                                    ->with('userIdentityCard')
                                    ->where(['state' => LoanModel::STATE_REPAYING])
                                    ->andWhere(['=', 'planned_repayment_at', $today])->all();
        if ($comeDueLoans) {
            foreach ($comeDueLoans as $k=>$v) {
                $repayDetail = LoanService::caculateRepaymentAmountDetail($v->id);
                $bankcardRepaymentParams = $agreeeAuthParams = $this->lianlianpayInit(); // 初始化基本请求参数
                $lianlianpayApi = new LianlianpayApi($this->lianlianpayConfig);

                // 1、将对应的借款用户授权
                $agreeeAuthParams['user_id'] = $v->user_id; // 用户ID
                $agreeeAuthParams['repayment_plan'] = json_encode([
                    'repaymentPlan' =>[
                        ['date' => $v->planned_repayment_at, 'amount' => $repayDetail['repaymentAmount'] ],
                    ]
                ]); // 还款计划
                $agreeeAuthParams['repayment_no'] = LoanService::buildOrderCode($v->user_id); // 还款计划编号
                $agreeeAuthParams['sms_param'] = json_encode([
                    'contract_type' =>self::API_CONTRACT_TYPE, // 商户名称
                    'contact_way' =>self::API_CONTACT_WAY, // 商户联系方式
                ]); // 短信参数
                $agreeeAuthParams['pay_type'] = self::API_PAY_TYPE_AUTH; // 支付方式
                $agreeeAuthParams['no_agree'] = $v->userBank->agreeno; // 签约协议号
                Yii::info('参数信息' .json::encode($agreeeAuthParams), 'lianlianpay');

                $requestPara = $lianlianpayApi->buildRequestPara($agreeeAuthParams);
                $agreenoAuthResult = $lianlianpayApi->agreenoAuthApply($requestPara);

                Yii::info('用户分期授权返回信息：' . $agreenoAuthResult, 'lianlian');
                $agreenoAuthResultArray = Json::decode($agreenoAuthResult);
                if ($agreenoAuthResultArray['ret_code'] != LianlianpayApi::REQUEST_SUCCESS) {
                    Yii::error('连连支付-用户分期授权申请失败 ret_code:' . $agreenoAuthResultArray['ret_code'] . ' ret_msg:' . $agreenoAuthResultArray['ret_msg'], 'lianlianpay');

                    $im = Yii::$app->companyim;
                    $im->sendNotice('sys_error', $im::TECH_DEPT_CHAT_ID, '连连支付-用户分期授权申请失败 用户' . $agreeeAuthParams['user_id'] . 'ret_code:' . $agreenoAuthResultArray['ret_code'] . ' ret_msg:' . $agreenoAuthResultArray['ret_msg']);

                    exit();
                }
                // 2、银行卡还款扣款
                Yii::info('签约成功执行扣款', 'lianlianpay');

                // 查询订单是否可复用
                $isReuseFlag = false; // 标识是否存在可重复提交的支付记录  true-表示存在可重复提交记录 false-表示不存在（即需要重新添加支付记录） 默认值【false】
                $activePayLog = PayLogModel::findActivePayLog($v->user_id, $v->id, PayLogModel::TYPE_INSTALLMENTPAY); // 查询用户的有效订单
                $time = date('Y-m-d H:i:s', time()- 60 *60 *2); // 前两个小时时间
                if ($activePayLog && $activePayLog->state == PayLogModel::STATE_PENDING && $activePayLog->created_at > $time) { // 状态为初始状态 且 订单时间在两个小时以内 的订单可以被重复使用
                    $isReuseFlag = true;
                }

                $bankcardRepaymentParams['user_id'] = $v->user_id;
                $bankcardRepaymentParams['busi_partner'] = LianlianpayApi::AUTHPAY_BUSI_PARTNER_VIRTUAL;
                $orderNo = $isReuseFlag ? $activePayLog->no_order : LoanService::buildOrderCode($v->user_id); // 商户系统唯一标识该付款的流水号
                $bankcardRepaymentParams['no_order'] = $orderNo; // 商户系统唯一订单号,此处使用了借款订单编号的生成规则
                $bankcardRepaymentParams['dt_order'] = date('YmdHis');
                $bankcardRepaymentParams['name_goods'] = LianlianpayApi::AUTHPAY_NAME_GOODS_BANKCARD_REPAYMENT;
                $bankcardRepaymentParams['info_order'] = LianlianpayApi::AUTHPAY_NAME_GOODS_BANKCARD_REPAYMENT;
                $bankcardRepaymentParams['money_order'] = $repayDetail['repaymentAmount'];
                $bankcardRepaymentParams['notify_url'] = Yii::$app->params['llpay_bankcard_repayment_notify'];
                $bankcardRepaymentParams['risk_item'] = '{"frms_ware_category":"2010","user_info_mercht_userno":"'. $v->user_id . '","user_info_bind_phone":"' . $v->user->mobile . '","user_info_dt_register":"' . date('YmdHis',strtotime($v->user->created_at)) . '","user_info_identify_type":"1","user_info_identify_state":"1","user_info_full_name":"' . $v->userIdentityCard->real_name . '","user_info_id_no":"' . $v->userIdentityCard->identity_no . '"}';// 风险控制参数
                $bankcardRepaymentParams['schedule_repayment_date'] = $v->planned_repayment_at;
                $bankcardRepaymentParams['repayment_no'] = $agreeeAuthParams['repayment_no'];
                $bankcardRepaymentParams['pay_type'] = self::API_PAY_TYPE_AUTH; // 支付方式
                $bankcardRepaymentParams['no_agree'] = $v->userBank->agreeno; // 签约协议号

                Yii::info('银行卡还款扣款参数信息' .json::encode($agreeeAuthParams), 'lianlianpay');

                $requestPara = $lianlianpayApi->buildRequestPara($bankcardRepaymentParams);
                $bankcardRepaymentResult = $lianlianpayApi->bankcardRepayment($requestPara);

                if (!$isReuseFlag) { // 不可重复利用时，添加新支付记录
                    // 将构造完成的订单存入数据库
                    PayLogModel::addPayLog(array(
                        'loan_id' => $v->id, // 借款ID
                        'user_id' => $v->user_id, // 用户ID
                        'pay_type' => PayLogModel::TYPE_INSTALLMENTPAY, // 支付类型：分期支付
                        'no_order' => $bankcardRepaymentParams['no_order'], // 商户系统唯一订单号
                        'info_order' => $bankcardRepaymentParams['info_order'], // 商品名称
                        'money_order' => $bankcardRepaymentParams['money_order'], // 交易金额
                        'dt_order' => date('Y-m-d H:i:s', strtotime($bankcardRepaymentParams['dt_order'])), // 订单时间
                    ));
                } else { // 订单可复用时
                    PayLogModel::updatePayLogByNoOrder($activePayLog->no_order, ['money_order' => $bankcardRepaymentParams['money_order']]); // 更新下当前交易金额 原因：逾期时，逾期罚金每日金额不同
                }

                Yii::info('用户分期银行卡还款扣款返回信息：' . $bankcardRepaymentResult, 'lianlian');
                $bankcardRepaymentResultArray = Json::decode($bankcardRepaymentResult);
                if ($bankcardRepaymentResultArray['ret_code'] != LianlianpayApi::REQUEST_SUCCESS) {
                    Yii::error('连连支付-用户分期授权申请失败 ret_code:' . $bankcardRepaymentResultArray['ret_code'] . ' ret_msg:' . $bankcardRepaymentResultArray['ret_msg'], 'lianlianpay');

                    $im = Yii::$app->companyim;
                    $im->sendNotice('sys_error', $im::TECH_DEPT_CHAT_ID, '连连支付-用户分期银行卡还款扣款失败 用户' . $bankcardRepaymentParams['user_id'] . 'ret_code:' . $bankcardRepaymentResultArray['ret_code'] . ' ret_msg:' . $bankcardRepaymentResultArray['ret_msg'] .' card_no: ' . $v->userBank->bank_no);

                    exit();
                }
            }
        }
    }
}