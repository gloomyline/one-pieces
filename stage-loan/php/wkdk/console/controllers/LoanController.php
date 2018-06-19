<?php
namespace console\controllers;

use common\bases\CommonService;
use common\extend\sms\AlidayuSms;
use common\extend\sms\MsgTemplate;
use common\models\BudgetPlanModel;
use common\models\LoanModel;
use common\models\MobileLogModel;
use common\models\OverdueLogModel;
use common\models\ProductModel;
use Yii;
use yii\console\Controller;

class LoanController extends Controller
{
    /**
     * 设置借款逾期
     */
    public function actionSetOverdue()
    {
        Yii::info('系统执行逾期检查@' . date('Y-m-d H:i:s'), 'overdue');
        // 判断借款计划，待还款状态的记录是否 超过计划还款时间
        $where = [
            'state' => [ BudgetPlanModel::STATE_REPAYING ], // 状态为还款中
        ];
        $budgetPlans = BudgetPlanModel::getBudgetPlanByCondition($where);
        $overDuePlanCount = 0; // 逾期记录数
        $overDueDetail = []; // 逾期详情
        foreach ($budgetPlans as $v) {
            // 计划还款时间 小于 当前时间，判定该借款计划逾期
            $days = (strtotime(date('Y-m-d', time())) - strtotime($v['planned_repayment_at'])) / 24 / 3600;
            if ($days > 0) {
                // 设置该计划逾期
                BudgetPlanModel::setOverdueStateById($v['id']);
                // 设置该计划对应的借款逾期
                LoanModel::setOverDueStateById($v['loan_id']);
                // 计算逾期记录数
                $overDuePlanCount++;
                // 逾期详情
                $overDueDetail[] = [
                    'loan_id' => $v['loan_id'],
                    'user_id' => $v['user_id'],
                    'term' => $v['term'],
                    'planned_repayment_at' => $v['planned_repayment_at'],
                ];

            } else if ($days == 0) {
                // 还款日，发送还款通知
                $service = new CommonService();
                $type = MobileLogModel::TYPE_REPAYMENT_SUCCESS_NOTICE; // 还款通知
                $params = [
                    'yearmonth' => $v['term'],
                    'account' => $v['monthly'],
                    'date' => $v['planned_repayment_at'],
                ]; // 尊敬的用户：您的第${yearmonth}期账单金额为${account}元，请于${date}前付款。如已付款，无需理会。
                $service->sendSmsCode(AlidayuSms::Sign, MsgTemplate::REPAYMENT_NOTIFY, $v['user']['mobile'], $params, $type); // 还款通知
            }
        }
        $tips = sprintf('[悟空分期]系统执行逾期检查结果：%s 项还款计划逾期并且被标记。' . "\n", $overDuePlanCount);
        foreach ($overDueDetail as $k => $v) {
            $tips .= sprintf('%s、用户ID(%s) 借款ID(%s) 期数(%s) 应还款时间(%s)' . "\n", ($k + 1), $v['user_id'], $v['loan_id'], $v['term'], $v['planned_repayment_at']);
        }
        if ($overDuePlanCount > 0) {
            $im = Yii::$app->companyim;
            $im->sendTextToChatGroup($im::SENDER_XIAOBAI, $im::COMPANY_CHAT_ID, $tips);
        }
        Yii::info('Tips: Overdue loans have been successfully marked.', 'overdue');
        Yii::info($tips, 'overdue');
    }

    /**
     * 记录逾期日志以及发送逾期通知
     */
    public function actionOverdueNotify()
    {
        Yii::info('系统执行逾期提醒检查@' . date('Y-m-d H:i:s'), 'overdue');
        // 判断借款计划，待还款状态的记录是否 超过计划还款时间
        $where = [
            'state' => [ BudgetPlanModel::STATE_OVERDUE ], // 状态为逾期中
        ];
        $budgetPlans = BudgetPlanModel::getBudgetPlanByCondition($where);
        $overDuePlanCount = 0; // 逾期记录数
        $overDueDetail = []; // 逾期详情
        foreach ($budgetPlans as $v) {
            // 计划还款时间 小于 当前时间，判定该借款计划逾期
            $days = (strtotime(date('Y-m-d', time())) - strtotime($v['planned_repayment_at'])) / 24 / 3600;
            if ($days > 0) {
                // 计算逾期记录数
                $overDuePlanCount++;
                //  获取产品配置
                $product = ProductModel::getProductByCondition(['type' => $v['loan']['type'], 'active' => ProductModel::STATE_ACTIVE]);
                $days = $days > $product->limit_overdue_days ? $product->limit_overdue_days : $days; // 若天数达逾期上限，为上限天数
                // 计算逾期罚金
                $overdueFees = round($days * $v['principal'] * $v['loan']['overdue_rate'], 2); // 逾期罚金 = 逾期天数 * 逾期本金 * 逾期费率; // 借款逾期罚金
                // 若存在计划逾期记录
                $overdueLog = OverdueLogModel::findOneByPlanId($v['id']);
                if ($overdueLog) {
                    // 更新借款逾期日志
                    OverdueLogModel::update($v['id'], $overdueFees, $days);
                } else {
                    // 计划逾期信息
                    $overdueLog = [
                        'loan_id' => $v['loan_id'],
                        'plan_id' => $v['id'],
                        'overdue_days' => $days,
                        'overdue_fees' => $overdueFees,
                    ];
                    // 保存借款逾期日志
                    OverdueLogModel::add($overdueLog);
                }

                // 发送逾期短信
                $service = new CommonService();
                $type = MobileLogModel::TYPE_OVERDUE_NOTICE; // 逾期通知
                $params = [
                    'yearmonth' => $v['planned_repayment_at'],
                    'account' => round(($v['monthly'] + $overdueFees), 2),
                    'day' => $days,
                ]; // 尊敬的用户：您${yearmonth}账单金额为${account}元，已过期${day}天，请尽快通过平台进行操作处理。如已付款，无需理会。
                $service->sendSmsCode(AlidayuSms::Sign, MsgTemplate::OVERDUE_NOTIFY, $v['user']['mobile'], $params, $type); // 逾期通知

                // 逾期详情
                $overDueDetail[] = [
                    'loan_id' => $v['loan_id'],
                    'user_id' => $v['user_id'],
                    'term' => $v['term'],
                    'planned_repayment_at' => $v['planned_repayment_at'],
                    'days' => $days,
                    'overdue_fees' => $overdueFees,
                ];
            }
        }
        $tips = sprintf('[悟空分期]系统执行逾期提醒检查结果：%s 项逾期日志已更新。' . "\n", $overDuePlanCount);
        foreach ($overDueDetail as $k => $v) {
            $tips .= sprintf('%s、用户ID(%s) 借款ID(%s) 期数(%s) 应还款时间(%s) 逾期天数(%s) 逾期罚金(%s)' . "\n", ($k + 1), $v['user_id'], $v['loan_id'], $v['term'], $v['planned_repayment_at'], $v['days'], $v['overdue_fees']);
        }
        if ($overDuePlanCount > 0) {
            $im = Yii::$app->companyim;
            $im->sendTextToChatGroup($im::SENDER_XIAOBAI, $im::COMPANY_CHAT_ID, $tips);
        }
        Yii::info($tips, 'overdue');
    }
}