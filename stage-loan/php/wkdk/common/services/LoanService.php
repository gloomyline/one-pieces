<?php
namespace common\services;
use common\bases\CommonService;
use common\extend\sms\AlidayuSms;
use common\extend\sms\MsgTemplate;
use common\models\BudgetPlanModel;
use common\models\LoanModel;
use common\models\LoanLogModel;
use common\models\MobileLogModel;
use common\models\ProductModel;
use common\models\ShopModel;
use common\models\ShopProductModel;
use common\models\UrgeModel;
use common\models\UserModel;
use Yii;

class LoanService
{
    const STATE_MAP = [
        'auditing' => 'auditing',
        'audit_failure' => 'audit_failure',
        'reviewing' => 'auditing',
        'review_failure' => 'audit_failure',
        'review_success' => 'audit_success',
        'granting' => 'granting',
        'confirming' => 'confirming',
        'confirm_success' => 'confirm_success',
        'confirm_failure' => 'confirm_failure',
        'repaying' => 'repaying',
        'finished' => 'finished',
        'overdue' => 'overdue',
    ];

    /**
     * 计算借款息费
     * @param int $quota 借款额度
     * @param int $period 借款期限(单位：月)
     * @param double $annualizedInterestRate 年化利率
     * @param double $trialRate 信审费率
     * @param double $serviceRate 服务费率
     * @param double $poundage 手续费率
     * @param string $periodUnit 借款期限单位【指示 按年化利率/月化利率/日化利率 计算】
     * @return double 返回借款息费
     */
    public static function caculateFee($quota, $period, $annualizedInterestRate, $trialRate, $serviceRate, $poundage, $periodUnit = 'day')
    {
        if ($periodUnit == 'day') {
            $interest = $quota * $period * ($annualizedInterestRate/12/30); //利率
        } else if ($periodUnit == 'month') {
            $interest = $quota * $period * ($annualizedInterestRate/12); //利率
        } else {
            $interest = $quota * $period * ($annualizedInterestRate); //利率
        }

        $trial_fee = $quota * $trialRate; //信审费
        $service_fee = $quota * $serviceRate; //服务费
        $service_charge = $quota * $poundage; //手续费

        $interest = round($interest, 2); //保留2位小数位
        $trial_fee = round($trial_fee, 2); //保留2位小数位
        $service_fee = round($service_fee, 2); //保留2位小数位
        $service_charge = round($service_charge, 2); //保留2位小数位

        //借款息费总额
        $total_fee = $interest + $trial_fee + $service_fee + $service_charge;
        return $total_fee;
    }

    /**
     * 计算分期借款息费
     * @param string $lendingAt 放款时间 2017-12-21
     * @param int $quota 借款额度
     * @param int $period 借款期限(单位：月)
     * @param double $annualizedInterestRate 年化利率
     * @param double $trialRate 信审费率
     * @param double $serviceRate 服务费率
     * @param double $poundage 手续费率
     * @return double 借款息费
     */
    public static function caculateInstallmentFee($lendingAt, $quota, $period,  $annualizedInterestRate, $trialRate, $serviceRate, $poundage)
    {
        $repaymentAt = SiteService::getNextMonthDate($lendingAt); // 下月还款日
        $days = SiteService::getDaysDistance($repaymentAt); // 第一期借款时间

        $avgPrincipal = round(($quota / $period), 2); // 第一期本金
        $lastPrincipal = $quota - $avgPrincipal;  // 最后一期本金
        $totalFee = 0; // 借款息费总额 = 第一期总息费 + 剩余总息费
        $firstFee = self::caculateFee($avgPrincipal, $days, $annualizedInterestRate, $trialRate, $serviceRate, $poundage); // 第一期借款息费
        $totalFee += $firstFee; // 加上第一期息费
        for ($i=2; $i<=$period; $i++) {
            // 最后一期
            if ($i == $period) {
                $fee = self::caculateFee($lastPrincipal, 1, $annualizedInterestRate, $trialRate, $serviceRate, $poundage, 'month');
            } else {
                $fee = self::caculateFee($avgPrincipal, 1, $annualizedInterestRate, $trialRate, $serviceRate, $poundage, 'month'); // 剩余借款息费
                $lastPrincipal -= $avgPrincipal; // 减去本次循环借款息费
            }
            $totalFee += $fee; // 总息费叠加
        }
        return $totalFee;
    }

    public static function generateSubmitLog($loanId)
    {
        $title = LoanLogModel::TITLE_SUBMIT_SUCCESS;
        $content = '您已成功提交资料';
        LoanLogModel::addLoanLog($loanId, $title, $content);
    }

    public static function generateAuditingLog($loanId)
    {
        $title = LoanLogModel::TITLE_AUDITING;
        $content = '审核结果将以【消息推送】和【短信】的方式提醒您';
        LoanLogModel::addLoanLog($loanId, $title, $content);
    }

    public static function generateAuditFailureLog($loanId)
    {
        $title = LoanLogModel::TITLE_AUDIT_FAILURE;
        $content = '您的分期申请未通过审核';
        LoanLogModel::addLoanLog($loanId, $title, $content);
    }

    public static function generateAuditSuccessLog($loanId)
    {
        $title = LoanLogModel::TITLE_AUDIT_SUCCESS;
        $content = '您的分期申请已通过审核！';
        LoanLogModel::addLoanLog($loanId, $title, $content);
    }

    // 消费分期 商家确认中
    public static function generateShopConfirmingLog($loanId)
    {
        $title = LoanLogModel::TITLE_CONFIRMING;
        $content = '您的消费申请已通过审核，等待商家确认';
        LoanLogModel::addLoanLog($loanId, $title, $content);
    }

    // 消费分期 商家确认通过
    public static function generateShopConfirmSuccessLog($loanId)
    {
        $title = LoanLogModel::TITLE_CONFIRM_SUCCESS;
        $content = '您的消费申请已通商家确认！';
        LoanLogModel::addLoanLog($loanId, $title, $content);
    }

    // 消费分期 商家确认不通过
    public static function generateShopConfirmFailureLog($loanId)
    {
        $title = LoanLogModel::TITLE_CONFIRM_FAILURE;
        $content = '您的消费申请商家确认未通过！';
        LoanLogModel::addLoanLog($loanId, $title, $content);
    }

    public static function generateGrantingLog($loanId)
    {
        $title = LoanLogModel::TITLE_GRANTING;
        $content = '您的分期申请商家已确认，平台正在放款，请耐心等待！';
        LoanLogModel::addLoanLog($loanId, $title, $content);
    }

    //支付回调失败时调用
    public static function generateGrantFailureLog($loanId)
    {
        $title = LoanLogModel::TITLE_GRANT_FAILURE;
        $content = '放款失败，请您咨询客服';
        LoanLogModel::addLoanLog($loanId, $title, $content);
    }

    /**
     * 放款成功日志：放款成功时调用
     * @param $loanId
     */
    public static function generateGrantSuccessLog($loanId)
    {
        $title = LoanLogModel::TITLE_GRANT_SUCCESS;
        $content = '您的分期申请商家已确认，且放款成功！';
        LoanLogModel::addLoanLog($loanId, $title, $content);
    }

    /**
     * 借款结清日志：还款结束时调用
     * @param $loanId
     */
    public static function generateRepaymentLog($loanId)
    {
//        $title = LoanLogModel::TITLE_REPAYING;
//        $content = '您的分期申请商家已确认，且放款成功！';
//        LoanLogModel::addLoanLog($loanId, $title, $content);
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
     * 计算借款当前还款金额明细
     * @param integer $loanId 借款ID
     * @return array $detail 还款金额明细
     */
    public static function caculateRepaymentAmountDetail($loanId)
    {
        $loan = LoanModel::findLoanById($loanId); // 查询当前借款以及还款计划信息
        $product = ProductModel::getProductByCondition(['type' => $loan['type'], 'active' => ProductModel::STATE_ACTIVE]); // 当前借款类别对应的产品信息

        $currentRepayingAt = SiteService::getRecentRepayingDate(date('Y-m-d')); // 获取最近还款日
        $detail = [
            'overdue_days' => 0, // 逾期天数
            'max_overdue_days' => $product['limit_overdue_days'] ?? 0, // 产品逾期天数上限
            'overdue_principal' => 0, // 逾期本金
            'overdue_fine' => 0, // 逾期罚金
            'monthly' => 0, // 应还月供
            'term_count' => 0, // 还款期数
            'term_amount' => 0, // 本期应还金额

            'prepayment_fee' => 0, // 提前还款手续费
            'repayment_at' => $currentRepayingAt, // 本期还款时间

            'surplus_amount' => 0, // 剩余未还金额
            'surplus_principal' => 0, // 剩余未还本金
            'repayed_amount' => 0, // 已还金额
            'last_repayment_at' => '', // 最后（近）一次实际还款时间

            'plan_detail' => [], // 计划
        ];
        if ($loan['budgetPlan']) {
            foreach ($loan['budgetPlan'] as $kbp => $vbp) {
                // 计划还款时间小于 最近还款日的 计划 为逾期计划
                if (strtotime($vbp['planned_repayment_at']) < strtotime($currentRepayingAt) && ($vbp['state'] == BudgetPlanModel::STATE_REPAYING || $vbp['state'] == BudgetPlanModel::STATE_OVERDUE)) {
                    # 计算逾期
                    $overdueDays =  abs(SiteService::getDaysDistance($vbp['planned_repayment_at'])); // 逾期天数
                    $overdueDays = ($overdueDays - $product['limit_overdue_days']) > 0 ? $product['limit_overdue_days'] : $overdueDays; // 是否达逾期上限天数 ? 产品逾期最大天数 : 计算的逾期天数
                    $overdueFine = round($vbp['principal'] * $loan['overdue_rate'] * $overdueDays, 2); // 逾期罚金 = 逾期本金 * 逾期费率 * 逾期天数

                    $detail['overdue_days'] += $overdueDays; // 计划还款时间 到当前时间相差的天数
                    $detail['overdue_principal'] += $vbp['principal']; // 逾期本金
                    $detail['overdue_fine'] += $overdueFine; // 逾期罚金 = 逾期本金 * 逾期费率 * 逾期天数

                    $detail['monthly'] += $vbp['monthly']; // 应还月供
                    $detail['term_count']++; // 期数自增

                    $detail['surplus_amount'] += $vbp['monthly']; // 剩余未还金额
                    $detail['surplus_principal'] += $vbp['principal']; // 剩余未还本金

                    $detail['plan_detail'][] = [
                        'id' => $vbp['id'], // 还款计划ID
                        'prepayment_fee' => 0, // 提前还款手续费
                        'repayed_amount' => $vbp['monthly'] + $overdueFine, // 实际还款金额 = 月供 + 罚金
                        'repayment_at' => date('Y-m-d H:i:s'), // 实际还款时间
                    ]; // 计划明细
                } else if(strtotime($vbp['planned_repayment_at']) == strtotime($currentRepayingAt) && ($vbp['state'] == BudgetPlanModel::STATE_REPAYING || $vbp['state'] == BudgetPlanModel::STATE_OVERDUE)){
                    $isRepayingAt = (strtotime(date('Y-m-d')) - strtotime($currentRepayingAt)) == 0 ? true : false; // 当天是否为本期放款日
                    // 本期还款日
                    if ($isRepayingAt) {
                        // 若有逾期未还款，且未至还款日，先还逾期；若正好至还款日，逾期与本期正常还款一起还款
                        $detail['monthly'] += $vbp['monthly'];
                        $detail['plan_detail'][] = [
                            'id' => $vbp['id'], // 还款计划ID
                            'prepayment_fee' => 0, // 提前还款手续费
                            'repayed_amount' => $vbp['monthly'], // 实际还款金额 = 月供
                            'repayment_at' => date('Y-m-d H:i:s'), // 实际还款时间
                        ]; // 计划明细
                    } else {
                        // 若有逾期未还款，且未至还款日，先还逾期, 此处不做处理
                        // 若无逾期未还款，且未至还款日，需支付 提前还款手续费
                        if ($detail['overdue_days'] == 0) {
                            $detail['monthly'] += $vbp['monthly']; // 应还月供
                            $detail['plan_detail'][] = [
                                'id' => $vbp['id'], // 还款计划ID
                                'prepayment_fee' => 0, // 提前还款手续费
                                'repayed_amount' => $vbp['monthly'], // 实际还款金额 = 月供
                                'repayment_at' => date('Y-m-d H:i:s'), // 实际还款时间
                            ]; // 计划明细
                        }
                    }
                    $detail['term_count']++; // 期数自增
                    $detail['surplus_principal'] += $vbp['principal']; // 剩余未还本金
                } else if ($vbp['state'] == BudgetPlanModel::STATE_REPAYING || $vbp['state'] == BudgetPlanModel::STATE_OVERDUE){
                    $detail['surplus_amount'] += $vbp['monthly']; // 剩余未还金额
                    $detail['surplus_principal'] += $vbp['principal']; // 剩余未还本金
                }
                $detail['repayed_amount'] += $vbp['repayed_amount']; // 已还金额
                if ($vbp['term'] == $loan['repayed_count']) {
                    $detail['last_repayment_at'] = $vbp['repayment_at']; // 实际还款时间
                }
            }
        }
        // 本期应还金额 = 应还月供和 + 逾期罚金
        $detail['term_amount'] = $detail['monthly'] + $detail['overdue_fine'] + $detail['prepayment_fee'];
        $detail['surplus_amount'] += $detail['term_amount']; // 剩余未还金额
        return $detail;
    }

    /**
     * 计算借款还清金额明细
     * @param integer $loanId 借款ID
     * @return array $detail 还款金额明细
     */
    public static function caculateRepaymentAmountSettleDetail($loanId)
    {
        $loan = LoanModel::findLoanById($loanId); // 查询当前借款以及还款计划信息
        $product = ProductModel::getProductByCondition(['type' => $loan['type'], 'active' => ProductModel::STATE_ACTIVE]); // 当前借款类别对应的产品信息

        $currentRepayingAt = SiteService::getRecentRepayingDate(date('Y-m-d')); // 获取最近还款日
        $detail = [
            'term_count' => 0, // 还款期数
            'term_amount' => 0, // 应还金额
            'term_interest' => 0, // 借款息费

            'prepayment_fee' => 0, // 提前还款手续费
            'repayment_at' => $currentRepayingAt, // 本期还款时间

            'surplus_principal' => 0, // 剩余本金（不包含当期本金）
            'loan_principal' => 0, // 剩余借款本金（包含当期本金）

            'plan_detail' => [], // 计划
        ];
        if ($loan['budgetPlan']) {
            foreach ($loan['budgetPlan'] as $kbp => $vbp) {
                // 本期还款计划
                if (strtotime($vbp['planned_repayment_at']) == strtotime($currentRepayingAt) && $vbp['state'] == BudgetPlanModel::STATE_REPAYING) {
                    $loanDays = abs(SiteService::getDaysDistance($vbp['begin_repayment_at'])); // 本月借款天数
                    $interest = self::caculateFee($vbp['principal'], $loanDays, $loan['annualized_interest_rate'], $loan['trial_rate'], $loan['service_rate'], $loan['poundage'], 'day');
                    $detail['term_count']++; // 还款期数
                    $detail['term_interest'] += $interest; // 本期应还息费 = 本期本金 * 日化利率 * 借款天数
                    $detail['term_amount'] = $detail['term_amount'] + $vbp['principal'] + $detail['term_interest']; // 应还金额
                    $detail['loan_principal'] += $vbp['principal']; // 剩余借款本金

                    $detail['plan_detail'][] = [
                        'id' => $vbp['id'], // 还款计划ID
                        'repayed_amount' => $vbp['principal'] + $detail['term_interest'], // 实际还款金额
                        'repayment_at' => date('Y-m-d H:i:s'), // 实际还款时间
                    ]; // 计划明细
                } else if (strtotime($vbp['planned_repayment_at']) > strtotime($currentRepayingAt) && $vbp['state'] == BudgetPlanModel::STATE_REPAYING){
                    // 应还金额 = 本期月供 + 剩余未还金额 + 提前还款手续费
                    $detail['term_count']++; // 还款期数
                    $detail['surplus_principal'] += $vbp['principal']; // 剩余本金
                    $detail['loan_principal'] += $vbp['principal']; // 剩余借款本金
                    $detail['term_amount'] += $vbp['principal']; // 应还金额

                    $detail['plan_detail'][] = [
                        'id' => $vbp['id'], // 还款计划ID
                        'repayed_amount' => $vbp['principal'], // 实际还款金额 = 本月本金
                        'repayment_at' => date('Y-m-d H:i:s'), // 实际还款时间
                    ]; // 计划明细
                }
            }
        }
        $prepaymentFee = round($detail['surplus_principal'] * $product['prepayment_poundage'], 2); // 提前还款手续费 = 剩余本金 * 提前还款手续费率
        $detail['prepayment_fee'] = ($prepaymentFee >= $product['prepayment_poundage_max']) ? $product['prepayment_poundage_max'] : $prepaymentFee;
        // 本期应还金额 = 本期月供 + 剩余本金 + 提前还款手续费
        $detail['term_amount'] += $detail['prepayment_fee'];
        return $detail;
    }

    /**
     * 放款成功后 相关的关联数据变更
     */
    public static function updateCorrelationAfterGrantSuccess($loanId, $quota, $userId)
    {
        $user = UserModel::findUserById($userId); // 获取用户信息
        $loan = LoanModel::findLoanById($loanId); // 获取借款订单信息

        $interest = LoanService::caculateInstallmentFee(date('Y-m-d'), $quota, $loan->period, $loan->annualized_interest_rate, $loan->trial_rate, $loan->service_rate, $loan->poundage); // 计算息费
        LoanModel::setRepayingStateById($loanId, $quota, $interest); // 变更到账金额、状态、放款时间、借款息费
        LoanService::generateBudgetPlan($loanId, true); // 生成还款计划，并存入分期计划表
        self::generateGrantSuccessLog($loanId);   // 添加日志为放款成功
        UserModel::updateAfterGrantSuccess($userId, $quota); // 更新user表 ：成功借款次数、成功借款金额

        // 消费分期放款成功更新商户商品的销量
        if ($loan->type == LoanModel::TYPE_CONSUMPTION) {
            if ($loan->orderDetail) {
                foreach ($loan->orderDetail as $row) {
                    ShopProductModel::updateProductSale($row->shop_product_id, $row->quantity);
                }
            }
            // 发送短信给商户、用户
            $service = new CommonService();
            $type = MobileLogModel::TYPE_LOAN_NOTICE; // 放款成功通知
            $userParams = [
                'date' => date('Y-m-d', strtotime($loan->created_at)),
                'periods' => $loan->period,
                'cash' => $quota,
                'account' => $quota
            ]; // 尊敬的用户：您于${date}申请的${periods}期，金额为${cash}元，已给商户转账，转账金额${account}元，敬请知悉！
            $service->sendSmsCode(AlidayuSms::Sign, MsgTemplate::LOAN_NOTIFY_USER, $user->mobile, $userParams, $type); // 消费分期成功放款通知 - 用户
            $shopParams = [
                'name' => $user->real_name,
                'date' => date('Y-m-d', strtotime($loan->created_at)),
                'account' => $quota
            ];// ${name}于${date}购买商品的购买金额为${account}元已经发放到您的账户，请注意查收。
            $service->sendSmsCode(AlidayuSms::Sign, MsgTemplate::LOAN_NOTIFY_SHOP, $loan->shop->actual_controller_mobile, $shopParams, $type); // 消费分期成功放款通知 - 商家

        } else {
            // 发送短信通知用户 放款成功
            $service = new CommonService();
            $type = MobileLogModel::TYPE_LOAN_NOTICE; // 放款成功通知
            $params = [
                'date' => date('Y-m-d', strtotime($loan->created_at)),
                'periods' => $loan->period,
                'cash' => $quota,
                'account' => $quota
            ]; // 尊敬的用户：您于${date}申请的总期数${periods}金额${cash}元,已成功到账，到账金额${account}元，请您注意查收！
            $service->sendSmsCode(AlidayuSms::Sign, MsgTemplate::LOAN_NOTIFY, $user->mobile, $params, $type); // 成功放款通知
        }

    }

    /**
     * 还款成功后 相关的关联数据变更
     * @param integer $loanId 借款ID
     * @param double $moneyOrder 本次还款交易金额
     * @param double $repayedAmount 已还款总金额
     * @param integer $userId 用户ID
     * @param string $planId 还款计划ID,多个ID 使用,隔开
     * @param string $planDetail 还款计划详情
     */
    public static function updateCorrelationAfterRepaySuccess($loanId, $moneyOrder, $repayedAmount, $userId, $planId, $planDetail)
    {
        LoanModel::setRepaymentRelatedById($loanId, $repayedAmount, $planId); // 变更已还款金额、状态、已还款期数
        $planDetail = json_decode($planDetail); // 还款计划详情
        foreach($planDetail->plan_detail as $k => $v) {
            // 变更还款计划信息
            if ($planDetail->repayed_type == BudgetPlanModel::TYPE_NORMAL) {
                BudgetPlanModel::updateBudgetPlan($v->id, $v->prepayment_fee, $v->repayed_amount, $v->repayment_at, $planDetail->repayed_type, BudgetPlanModel::STATE_FINISHED);
            } else {
                // 提前还清
                if ($k == 0) {
                    BudgetPlanModel::updateBudgetPlan($v->id, $planDetail->prepayment_fee, $moneyOrder, $v->repayment_at, $planDetail->repayed_type, BudgetPlanModel::STATE_FINISHED);
                } else {
                    BudgetPlanModel::updateBudgetPlan($v->id, 0, 0, $v->repayment_at, $planDetail->repayed_type, BudgetPlanModel::STATE_FINISHED);
                }
            }
        }

        $user = UserModel::findUserById($userId); // 获取用户信息
        $service = new CommonService();
        $type = MobileLogModel::TYPE_REPAYMENT_SUCCESS_NOTICE; // 还款成功通知
        $params = [
            'yearmonth' => date('Y-m-d H:i:s'),
            'account' => $moneyOrder
        ];
        $service->sendSmsCode(AlidayuSms::Sign, MsgTemplate::REPAYMENT_SUCCESS, $user->mobile, $params, $type); // 成功还款通知

        $loan = LoanModel::findLoanById($loanId); // 获取借款订单信息
        $plans = BudgetPlanModel::getBudgetPlanByIds(explode(',', $planId)); // 查询已还计划列表
        foreach ($plans as $v) {
            UserModel::thawUserQuota($userId, $v['principal'], true); // 解冻用户额度

            // 消费分期
            if ($loan['type'] == LoanModel::TYPE_CONSUMPTION) {
                ShopModel::thawShopQuota($loan['shop_id'], $v['principal']); // 解冻商家额度
            }

            // 逾期借款需更新逾期催收记录的状态,即还款时间大于计划还款时间
            if ((strtotime($v['repayment_at']) - strtotime($v['planned_repayment_at'])) > 0) {
                UrgeModel::urgeFinished($loanId, $v['id']);
            }
        }
    }

    /**
     * 生成分期计划
     * @param integer $loanId 借款ID
     * @param boolean $storage 是否存入数据库
     * @return array
     */
    public static function generateBudgetPlan($loanId, $storage = false)
    {
        $data = [];
        $loan = LoanModel::findLoanById($loanId);
        $forecastInterest = $loan->interest > 0 ? $loan->interest : LoanService::caculateInstallmentFee(date('Y-m-d'), $loan->quota, $loan->period, $loan->annualized_interest_rate, $loan->trial_rate, $loan->service_rate, $loan->poundage); // 临时计算息费
        if ($loan) {
            $amount = (float)($loan->quota + $forecastInterest); // 借款应还总额
            // 等本等息
            if ($loan->repayment_way == LoanModel::WAY_PRINCIPAL_INTEREST_DUE) {
                $tempPrincipal = 0; // 临时变量，计算已还本金
                $tempInterest = 0; // 临时变量，计算已还息费
                $tempMonthly = 0; // 临时变量，计算已还月供

                $beginAt = date('Y-m-d', strtotime(' +1 day ')) ; // 开始计息日，默认值为第二天
                $repayAt = SiteService::getNextMonthDate(date('Y-m-d')); // 还款日，默认值为当天

                for($i = 1; $i <= $loan->period; $i++) {
                    // 第一期
                    if ($i == 1) {
                        $days = SiteService::getDaysDistance($repayAt); // 借款天数
                        $principal = round(($loan->quota / $loan->period), 2); // 本期本金 = 借款总额/借款期数
                        $interest = self::caculateFee($principal, $days, $loan->annualized_interest_rate, $loan->trial_rate, $loan->service_rate, $loan->poundage);
                        $monthly = $principal + $interest;
                    } else {
                        $beginAt = date('Y-m-d', strtotime($repayAt .' +1 day ')); // 开始计息日
                        $repayAt = SiteService::getNextMonthDate($beginAt); // 还款到期日

                        // 最后一期
                        if ($i == $loan->period) {
                            $principal = $loan->quota - $tempPrincipal;
                            $interest = (float)$forecastInterest - $tempInterest;
                            $monthly = $amount - $tempMonthly;
                        } else {
                            $principal = round(($loan->quota / $loan->period), 2); // 本期本金 = 借款总额/借款期数
                            $interest = self::caculateFee($principal, 1, $loan->annualized_interest_rate, $loan->trial_rate, $loan->service_rate, $loan->poundage, 'month');
                            $monthly = $principal + $interest; // 剩余期数 月供
                        }
                    }
                    $trialFee = round(($principal * $loan->trial_rate), 2); // 信审费用
                    $serviceFee = round(($principal * $loan->service_rate), 2); // 服务费用
                    $poundageFee = round(($principal * $loan->poundage), 2); // 手续费用
                    $interestFee = round(($interest - $trialFee - $serviceFee - $poundageFee), 2); // 利息
                    $data[] = [
                        'user_id' => $loan->user_id,
                        'loan_id' => $loanId,
                        'term' => $i,
                        'monthly' => $monthly, // 月供
                        'principal' => $principal, // 本月应还本金
                        'interest' => $interest, // 本期借款息费
                        'state' => BudgetPlanModel::STATE_REPAYING, // 状态
                        'begin_repayment_at' => $beginAt, // 开始还款时间
                        'planned_repayment_at' => $repayAt, // 计划还款时间
                        'trial_fee' => $trialFee, // 信审费用
                        'service_fee' => $serviceFee, // 服务费用
                        'poundage_fee' => $poundageFee, // 手续费用
                        'interest_fee' => $interestFee, // 利息
                    ];
                    // 是否存入分期计划表
                    if ($storage) {
                        BudgetPlanModel::addBudgetPlan($data[$i-1]); // 保存分期计划
                    }
                    $tempPrincipal += $principal;
                    $tempInterest += $interest;
                    $tempMonthly += $monthly;
                }
                unset($tempPrincipal);unset($tempInterest);unset($tempMonthly);
            }
        }
        return $data;
    }

    /**
     * 判断分期计划中是否存在未归还逾期项
     * @param integer $loanId 借款ID
     * @return bool true - 存在 false - 不存在
     */
    public static function isOverdue($loanId)
    {
        $currentTermRepaymentAt = SiteService::getRecentRepayingDate(date('Y-m-d')); // 获取本期还款日
        $budgetPlan = BudgetPlanModel::getBudgetPlanByLoanId($loanId); // 获取对应的分期计划
        foreach ($budgetPlan as $v) {
            if ($v['state'] == BudgetPlanModel::STATE_OVERDUE || (strtotime($v['planned_repayment_at']) < strtotime($currentTermRepaymentAt) && $v['state'] == BudgetPlanModel::STATE_REPAYING)) {
                return true;
            }
        }
        return false;
    }
}