<?php
namespace common\services;
use common\bases\CommonService;
use common\extend\sms\AlidayuSms;
use common\extend\sms\MsgTemplate;
use common\models\LoanModel;
use common\models\LoanLogModel;
use common\models\MobileLogModel;
use common\models\ProductModel;
use common\models\UrgeModel;
use common\models\UserModel;
use Yii;
use yii\helpers\Json;

class LoanService
{
    const STATE_MAP = [
        'auditing' => 'auditing',
        'audit_failure' => 'audit_failure',
        'reviewing' => 'auditing',
        'review_failure' => 'audit_failure',
        'review_success' => 'audit_success',
        'granting' => 'granting',
        'repaying' => 'repaying',
        'finished' => 'finished',
        'overdue' => 'overdue',
    ];
    /**
     * 计算借款息费
     * @param int $quota 借款额度
     * @param int $period 借款期限
     * @param object $product 产品配置
     * @return boolean
     */
    public static function caculateFee($quota, $period, $product)
    {
        $interest = $quota * $period * ($product->annualized_interest_rate/12/30); //利率
        $trial_fee = $quota * $product->trial_rate; //信审费
        $service_fee = $quota * $product->service_rate; //服务费
        $service_charge = $quota * $product->poundage; //手续费

        $interest = round($interest, 2); //保留2位小数位
        $trial_fee = round($trial_fee, 2); //保留2位小数位
        $service_fee = round($service_fee, 2); //保留2位小数位
        $service_charge = round($service_charge, 2); //保留2位小数位

        //借款息费总额
        $total_fee = $interest + $trial_fee + $service_fee + $service_charge;
        return $total_fee;
    }
    /**
     * 计算借款息费明细
     * @param int $quota 借款额度
     * @param int $period 借款期限
     * @param object $product 产品配置
     * @return array 借款息费明细
     */
    public static function caculateFeeDetail($quota, $period, $product)
    {
        $interest = $quota * $period * ($product->annualized_interest_rate/12/30); //利率
        $trialFee = $quota * $product->trial_rate; //信审费
        $serviceFee = $quota * $product->service_rate; //服务费
        $serviceCharge = $quota * $product->poundage; //手续费

        $interest = round($interest, 2); //保留2位小数位
        $trialFee = round($trialFee, 2); //保留2位小数位
        $serviceFee = round($serviceFee, 2); //保留2位小数位
        $serviceCharge = round($serviceCharge, 2); //保留2位小数位

        //借款息费总额
        $totalFee = $interest + $trialFee + $serviceFee + $serviceCharge;

        $feeDetail['annualizedInterestRate'] = $product->annualized_interest_rate; // 年化利率
        $feeDetail['accrual'] = $interest; // 利息
        $feeDetail['trialFee'] = $trialFee; // 信审费
        $feeDetail['serviceFee'] = $serviceFee; // 服务费
        $feeDetail['poundage'] = $serviceCharge; // 手续费
        $feeDetail['totalInterest'] = $totalFee; // 借款息费
        $feeDetail['otherFee'] = $trialFee + $serviceFee + $serviceCharge; // 其他费用
        return $feeDetail;
    }

    /**
     * 判断用户的最新借款是否处于借款冻结时间
     * @param integer $userId 用户ID
     * @return bool true 是 ， false 否
     */
    public static function isFreezeTime($userId)
    {
        $loanModel = new LoanModel();

        $loanFreezeTime = Yii::$app->params['loanFreezeTime']; // 获取借款冻结时间

        $latestLoan = $loanModel->getUserLatestLoan($userId); // 获取用户最新的借款订单

        if ($latestLoan->state == LoanModel::STATE_AUDIT_FAILURE) { // 初审失败
           if ((strtotime($latestLoan->check_at) + $loanFreezeTime) < time()) { // 初审时间 + 冻结时间 是否 小于 当前时间,小于 非冻结时间；大于 处于冻结时间
              return false;
           }
           return true;
        }
        if ($latestLoan->state == LoanModel::STATE_REVIEW_FAILURE) { // 复审失败
            if ((strtotime($latestLoan->review_at) + $loanFreezeTime) < time()) { // 复审时间 + 冻结时间 是否 小于 当前时间,小于 非冻结时间；大于 处于冻结时间
                return false;
            }
            return true;
        }
        return false;
    }

    public static function generateSubmitLog($loanId, $quota, $period, $interest)
    {
        $title = 'submit_success';
        $content = Json::encode([
            'quota' => $quota ?? 0, // 申请额度
            'period' => $period ?? 0, // 申请期限
            'poundage' => $interest ?? 0.00, // 手续费
        ]);
        LoanLogModel::addLoanLog($loanId, $title, $content);
    }

    public static function generateAuditingLog($loanId, $quota, $period, $interest)
    {
        $title = 'auditing';
        $content = Json::encode([
            'quota' => $quota ?? 0,
            'period' => $period ?? 0,
            'poundage' => $interest ?? 0.00,
        ]);
        LoanLogModel::addLoanLog($loanId, $title, $content);
    }

    public static function generateAuditFailureLog($loanId, $failureAt)
    {
        $title = 'audit_failure';
        $content = Json::encode([
            'failure_at' => $failureAt // 失败时间
        ]);
        LoanLogModel::addLoanLog($loanId, $title, $content);
    }

    public static function generateAuditSuccessLog($loanId)
    {   $loan = LoanModel::findLoanById($loanId);
        $title = 'audit_success';
        $content = Json::encode([
            'quota' => $loan->quota ?? 0, // 申请借款金额
            'period' => $loan->period ?? 0, // 申请期限
            'poundage' => self::caculateFee($loan->quota, $loan->period, ProductModel::getActiveProduct()) ?? 0.00, // 借款息费（WAP页面的手续费用）

            'bank_name' => $loan->userBank->bank_name ?? '', // 银行名称
            'end_bank_no' => isset($loan->userBank->bank_no) ? substr($loan->userBank->bank_no, -4) : '' // 银行卡尾号
        ]);
        LoanLogModel::addLoanLog($loanId, $title, $content);
    }

    public static function generateGrantingLog($loanId, $cardNo)
    {
        $title = 'granting';
        $content = Json::encode([
            'end_bank_no' => isset($cardNo) ? substr($cardNo, -4) : '' // 银行卡尾号
        ]);
        LoanLogModel::addLoanLog($loanId, $title, $content);
    }

    //支付回调失败时调用
    public static function generateGrantFailureLog($loanId)
    {
        $title = 'grant_failure';
        $content = '放款失败，请您咨询客服';
        LoanLogModel::addLoanLog($loanId, $title, $content);
    }

    /**
     * 放款成功时调用
     * @param $loanId
     * @param float $repayAmount 还款金额
     * @param integer $plannedRepaymentAt 计划还款时间（时间戳）
     */
    public static function generateRepaymentLog($loanId, $repayAmount, $plannedRepaymentAt)
    {
        $title = 'repaying';
        $content = Json::encode([
            'planned_repayment_at' => $plannedRepaymentAt ?? 0, // 计划还款时间
            'repay_amount' => $repayAmount ?? 0.00// 还款金额
        ]);
        LoanLogModel::addLoanLog($loanId, $title, $content);
    }
    // 支付中
    public static function generatePayingLog($loanId, $quota, $repayAmount, $overdueAmount, $interest)
    {
        $title = 'paying';
        $content = Json::encode([
            'quota' => $quota ?? 0, // 本金
            'repay_amount' => $repayAmount ?? 0.00, // 还款金额
            'overdue_amount' => $overdueAmount ?? 0.00, // 违约罚金
            'interest' => $interest ?? 0.00, // 借款息费

        ]);
        LoanLogModel::addLoanLog($loanId, $title, $content);
    }
    // 还款成功
    public static function generateRepaymentSuccessLog($loanId)
    {
        $title = 'repay_success';
        $content = '';
        LoanLogModel::addLoanLog($loanId, $title, $content);
    }
    // 还款失败
    public static function generateRepaymentFailureLog($loanId, $reason)
    {
        $title = 'repay_failure';
        $content = Json::encode([
            'reason' => $reason ?? ''// 失败原因
        ]);
        LoanLogModel::addLoanLog($loanId, $title, $content);
    }

    /**
     * 生成借款订单编号
     * @param int $userId 用户ID
     * @return boolean
     */
    public static function buildOrderCode($userId)
    {
        return date('Ymd') . str_pad($userId, 10, 0, STR_PAD_LEFT) . date('His');
    }

    /**
     * 计算还款金额明细
     * @param integer $loanId 借款ID
     * @return array $detail 还款金额明细
     */
    public static function caculateRepaymentAmountDetail($loanId)
    {
        $loan = LoanModel::findLoanById($loanId); // 查询用户的当前有效订单
        $product = ProductModel::getActiveProduct();

        $days = (strtotime(date('Y-m-d')) - strtotime($loan->planned_repayment_at)) / 3600 / 24; // 当前时间与计划还款时间相差的天数
        // 若逾期最大上限时间 小于 逾期天数
        if ($product->limit_overdue_days < $days) {
            $overdueDay = $product->limit_overdue_days; // 设置逾期天数为逾期最大上限时间
        } else {
            $overdueDay = $days > 0 ? $days : 0;
        }
        $detail['overdueAmount'] = round((float)($loan->overdue_rate * $loan->quota * $overdueDay), 2); // 逾期金额/违约金 =  逾期费率 * 借款本金 * 逾期天数（当前时间-计划还款时间《放款成功时间+申请期限（天）》）
        $detail['quota'] = $loan->quota;
        $detail['repaymentAmount'] = (float)($loan->quota + $loan->interest + $detail['overdueAmount']); // 用户应还金额总额 = 借款额度 + 借款息费 + 逾期罚金
        $detail['overduePrincipal'] = (float)($loan->quota + $loan->interest); // 非逾期时的应还金额
        $detail['plannedRepaymentAt'] = strtotime($loan->planned_repayment_at) ?? 0; // 计划还款时间
        $detail['overdueRate'] = $loan->overdue_rate ?? 0.00; // 逾期费率
        $detail['interest'] = $loan->interest ?? 0.00; // 借款息费
        $detail['overDay'] = $days > 0 ? $days : 0; // 逾期天数
        return $detail;
    }

    /**
     * 放款成功后 相关的关联数据变更
     */
    public static function updateCorrelationAfterGrantSuccess($loanId, $quota, $userId)
    {
        LoanModel::setRepayingStateById($loanId, $quota); // 变更到账金额、状态、放款时间
        $repayDeatil = self::caculateRepaymentAmountDetail($loanId); // 计算 用户还款本金、还款金额、违约金
        self::generateRepaymentLog($loanId, $repayDeatil['repaymentAmount'], $repayDeatil['plannedRepaymentAt']);   // 添加日志为还款中
        UserModel::updateAfterGrantSuccess($userId, $quota); // 更新user表 ：成功借款次数、成功借款金额

        $user = UserModel::findUserById($userId); // 获取用户信息
        $loan = LoanModel::findLoanById($loanId); // 获取借款订单信息
        $service = new CommonService();
        $type = MobileLogModel::TYPE_LOAN_NOTICE; // 放款成功通知
        $params = [
            'date' => date('Y-m-d', strtotime($loan->created_at)),
            'cash_account' => $quota,
            'account' => $quota
        ];
        $service->sendSmsCode(AlidayuSms::Sign, MsgTemplate::LOAN_NOTIFY, $user->mobile, $params, $type); // 成功放款通知
    }

    /**
     * 还款成功后 相关的关联数据变更
     */
    public static function updateCorrelationAfterRepaySuccess($loanId, $quota, $repayedAmount, $userId, $loanState)
    {
        LoanModel::setFinishedStateById($loanId, $repayedAmount); // 变更到账金额、状态、放款时间
        if ($loanState == LoanModel::STATE_OVERDUE) {
            UrgeModel::urgeFinished($loanId); // 逾期借款需更新逾期催收记录的状态
        }

        self::generateRepaymentSuccessLog($loanId);   // 添加日志为还款成功

        $user = UserModel::findUserById($userId); // 获取用户信息
        $service = new CommonService();
        $type = MobileLogModel::TYPE_REPAYMENT_SUCCESS_NOTICE; // 还款成功通知
        $service->sendSmsCode(AlidayuSms::Sign, MsgTemplate::REPAYMENT_SUCCESS, $user->mobile, ['time' => date('Y-m-d H:i:s'), 'account' => $quota], $type); // 成功还款通知

        UserModel::thawUserQuota($userId, $quota, true); // 解冻
    }
}