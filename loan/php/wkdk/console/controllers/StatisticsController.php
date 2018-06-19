<?php
/**
 * Created by PhpStorm.
 * User: lzh
 * Date: 2017/11/23
 * Time: 15:02
 */

namespace console\controllers;

use common\models\LoanModel;
use common\models\StatisticsModel;
use common\models\UrgeLogModel;
use common\models\UrgeModel;
use common\models\UserModel;
use Yii;
use yii\console\Controller;

class StatisticsController extends Controller
{
    /**
     *每日统计
     */
    public function actionDailyStatistics()
    {
        $yesterday = date("Y-m-d",strtotime("-1 day")); // 统计昨天的数据
        $result = [];
        $result = StatisticsModel::findOneByCreatedAt($yesterday); //查找是否有当天的统计记录若有不进行统计
        if ($result) {
            exit('Statistics already exist, do not repeat statistics');
        }
        // 获取昨天新增用户
        $userCount = UserModel::statRegisterUserCountByDate($yesterday);

        //借款
        $loan = LoanModel::statQuotaTotalByDate($yesterday); // 借款笔数
        //借款人数
        $loanUserCount = LoanModel::statQuotaUserCountByDate($yesterday);

        // 放款
        $lend = LoanModel::statLoanTotalByDate($yesterday); // 放款金额，笔数,借款息费
        $lendUserCount = LoanModel::statLoanUserCountByDate($yesterday);  // 放款人数

        //还款
        $repayment = LoanModel::statRepaymentByDate($yesterday); // 还款金额、笔数

        // 逾期还款接，逾期笔数
        $repaymentOverdue = LoanModel::statRepaymentOverdueByDate($yesterday);

        //逾期
        $overdue = LoanModel::statOverdueByDate($yesterday); // 逾期金额，笔数，罚息

        //催收
        $urgeTimes = UrgeLogModel::statUrgeTimesCountByDate($yesterday); // 催收次数
        $urgeLoanCount = UrgeLogModel::statUrgeCountByDate($yesterday); // 催收笔数
        $urgeSuccessCount = UrgeModel::statUrgeSuccessCountByDate($yesterday); // 催收成功次数

        // 坏账 ，坏账金额---暂无后续需补充添加
        $data = [];
        $data = [
            'created_at' => $yesterday,
            'user_count' => $userCount, // 今日新增
            'loan_count' => $loan['count'], // 借款笔数
            'loan_interest' => $lend['today_interest_total'] ?? 0, // 借款息费金额
            'loan_user' => $loanUserCount, // 借款人数
            'lend_count' => $lend['count'], //放款笔数;
            'lend_amount' => $lend['today_loan_total'] ?? 0,
            'lend_user' => $lendUserCount, // 放款人数
            'repayment_amount' => $repayment['today_repayment'] ?? 0,
            'repayment_count' => $repayment['count'],
            'repayment_overdue_count' => $repaymentOverdue['count'],
            'repayment_overdue_amount' => $repaymentOverdue['today_repayment_overdue'] ?? 0,
            'overdue_count' => $overdue['count'], // 逾期笔数
            'overdue_amount' =>  $overdue['today_overdue'] ?? 0,// 逾期金额
            'overdue_penalty' => $overdue['overdue_penalty'] ?? 0, // 逾期罚息
            'urge_count' => $urgeTimes, // 催收次数
            'urge_loan_count' => $urgeLoanCount, // 催收笔数
            'urge_success_count' => $urgeSuccessCount,
            'bad_count' => 0,
            'bad_amount' => 0
        ];
        StatisticsModel::add($data);
    }
}