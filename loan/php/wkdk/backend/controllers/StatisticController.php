<?php

namespace backend\controllers;

use backend\models\AdminModel;
use common\models\LoanModel;
use common\models\QuotaApplyModel;
use common\models\StatisticsModel;
use common\models\Urge;
use common\models\UrgeModel;
use common\models\UserModel;
use common\services\SystemService;
use Yii;
use backend\bases\BackendController;
use yii\helpers\Json;

class StatisticController extends BackendController
{
    /**
     * 今日统计
     * @return string
     */
    public function actionIndex()
    {

        $today = date('Y-m-d', time()); // 获取今天的日期
        $todayUsersCount = UserModel::statRegisterUserCountByDate($today); // 今日新用户

        $todayQuota = LoanModel::statQuotaTotalByDate($today); // 今日申请总额

        $todayLoanTotal = LoanModel::statLoanTotalByDate($today);   // 今日放款总额

        $todayRepayment = LoanModel::statRepaymentByDate($today);  // 今日还款总额

        $todayRefuse = LoanModel::todayRefuse(); // 今日拒绝次数

        $todayOverdue = LoanModel::statOverdueByDate($today); // 今日逾期未还总额

        $todayPlanRepayment = LoanModel::todayPlanRepayment();  // 今日到期待还款总额

        $QuotaApplyStateAuditingCount = QuotaApplyModel::getQuotaApplyStateAuditingCount(); // 额度待审核

        $auditingCount = LoanModel::getCountByState(LoanModel::STATE_AUDITING);  // 待初审

        $reviewingCount = LoanModel::getCountByState(LoanModel::STATE_REVIEWING); // 待复审

        $quotaCount = LoanModel::getCountByState(LoanModel::STATE_REVIEW_SUCCESS); // 待放款

        $overdueCount = LoanModel::getCountByState(LoanModel::STATE_OVERDUE); // 逾期未还

        // 逾期贷款记录最新逾期记录
        $overdueList = [];
        $lastOverdueList = LoanModel::getLastOverdue();
        if ($lastOverdueList) {
            foreach ($lastOverdueList as $list) {
                $overdueList[] = [
                    'id' => $list->id,
                    'encoding' => $list->encoding ?? '', // 借款编号
                    'created_at' => $list->created_at ?? '', // 借款日期
                    'period' => $list->period ?? 0, // 借款期限
                    'quota' => $list->quota ?? 0, // 本金
                    'user_name' => $list->user->real_name ?? '', //用户名
                    'mobile' => $list->user->mobile,
                ];
            }
        }

        $week = self::getWeek(); // 得到一周的日期
        $weekLoan = LoanModel::dailyLoan();
        $weekRepayment = LoanModel::dailyRepayment();
        $weekApply = LoanModel::dailyApply();
        $weekCheckRefuse = LoanModel::dailyCheckRefuse(); // 初审拒绝
        $weekReviewRefuse = LoanModel::dailyReviewRefuse();// 复审拒绝

        $result = [];
        $result = self::dataFromatter($week, $weekLoan, $weekRepayment, $weekApply, $weekCheckRefuse, $weekReviewRefuse);
        $data = [
            'new_users' => $todayUsersCount,
            'today_quota' => $todayQuota['today_quota_total'] ?? 0, // 今日申请
            'today_quota_count' => $todayQuota['count'],
            'today_loan_total' => $todayLoanTotal['today_loan_total'] ?? 0, // 今日放款
            'today_loan_total_count' =>$todayLoanTotal['count'],
            'today_repayment' => $todayRepayment['today_repayment'] ?? 0, // 今日还款
            'today_repayment_count' => $todayRepayment['count'],
            'today_refuse' => $todayRefuse['today_refuse'] ??  0, // 今日拒绝
            'today_refuse_count' => $todayRefuse['count'],
            'today_overdue' => $todayOverdue['today_overdue'] ?? 0, // 今日逾期
            'today_overdue_count' => $todayOverdue['count'],
            'today_plan_repayment' => $todayPlanRepayment['today_plan_repayment'] ?? 0, // 今日到期
            'today_plan_repayment_count' => $todayPlanRepayment['count'],
            'auditingCount' => $auditingCount, // 待初审
            'reviewingCount' => $reviewingCount, // 待复审
            'quota_apply_state_auditing_count' => $QuotaApplyStateAuditingCount, // 额度待审核
            'quotaCount' => $quotaCount, // 待放款
            'overdueCount' => $overdueCount, // 逾期未还
            'labels' => $week,
            'loan_amount' => $result['loan'] ?? [],
            'repayed_amount' => $result['repayment'] ?? [],
            'apply_count' => $result['apply'] ?? [], // 今日申请
            'refuse_count' => $result['refuse'] ?? [],
            'overdue_list' => $overdueList
        ];
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'results' => $data
        ]);
    }

    /**
     * 累计统计
     * @return string
     */
    public function actionAccumulateStatistic()
    {
        $totalUserCount = UserModel::RegisterUserCount(); // 累计注册用户

        $quota = LoanModel::quotaTotal(); // 累计申请总额

        $loanTotal = LoanModel::loanTotal();  // 累计放款总额

        $repayment = LoanModel::repaymentTotal(); // 还款总额

        $refuse = LoanModel::refuseTotal(); // 拒绝次数

        $overdue = LoanModel::overdueTotal(); // 逾期未还总额

        $planRepayment = LoanModel::planRepayment(); // 待还总额

        $months = self::getMonth();

        $monthLoan = LoanModel::monthLoan();

        $monthRepayment = LoanModel::monthRepayment();

        $monthApply = LoanModel::monthApply();

        $monthCheckRefuse = LoanModel::monthCheckRefuse(); //初审拒绝

        $monthReviewRefuse = LoanModel::monthReviewRefuse();// 复审拒绝

        $result = [];
        $result = self::dataFromatter($months, $monthLoan, $monthRepayment, $monthApply, $monthCheckRefuse, $monthReviewRefuse);

        $data = [
            'users' => $totalUserCount,
            'quota' => $quota['quota_total'] ?? 0, // 申请总额
            'quota_count' => $quota['count'],
            'loan_total' => $loanTotal['loan_total'] ?? 0, // 放款总额
            'loan_total_count' =>$loanTotal['count'],
            'repayment' => $repayment['repayment'] ?? 0, // 今日还款
            'repayment_count' => $repayment['count'],
            'refuse' => $refuse['refuse'] ??  0, // 今日拒绝
            'refuse_count' => $refuse['count'],
            'overdue' => $overdue['overdue'] ?? 0, // 累计逾期
            'overdue_count' => $overdue['count'],
            'plan_repayment' => $planRepayment['plan_repayment'] ?? 0, // 今日到期
            'plan_repayment_count' => $planRepayment['count'],
            'labels' => $months,
            'loan_amount' => $result['loan'] ?? [],
            'repayed_amount' => $result['repayment'] ?? [],
            'apply_count' => $result['apply'] ?? [], // 累计申请
            'month_refuse_count' => $result['refuse'] ?? []
        ];
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'results' => $data
        ]);
    }

    /**
     * 得到最近一周的日期
     * @return array
     */
    private static function getWeek()
    {
        $date = [];
        for ($i = 6 ; $i >= 0; $i--) {
            $date[] = date('Y-m-d', strtotime('-'.$i.' days'));
        }
        return $date;
    }

    /**
     * 获得最近半年的日期
     * @return array
     */
    private static function getMonth()
    {
        $date = [];
        for ($i = 5 ; $i >= 0; $i--) {
            $date[] = date('Y-m', strtotime('-'.$i.' month'));
        }
        return $date;
    }

    /**
     * 格式化图标的数据
     * @param array $length x轴的日期
     * @param array $loan 放款的数据
     * @param array $repayment 还款的数据
     * @param array $apply 申请的次数
     * @param array $checkRefuse 初审失败次数
     * @param array $reviewRefuse 复审失败次数
     * @return array 返回格式化的数组
     */
    private static function dataFromatter($length = [], $loan = [], $repayment = [], $apply = [], $checkRefuse = [], $reviewRefuse = [])
    {
        $loans = [];
        $repayments = [];
        $applys = [];
        $refuses = [];
        if ($length) {
            $i = 0;
            foreach ($length as $lth) {
                $a = $b = $c = $d = 0;
                if ($loan) {
                    foreach ($loan as $list) {

                        if ($list['dates'] == $lth) {
                            $loans[] = $list['loan_amount'];
                            $a = 1;
                        }
                    }
                }

                if ($a == 0) {
                    $loans[] = 0;
                }

                if ($repayment) {
                    foreach ($repayment as $list) {
                        if ($list['dates'] == $lth) {
                            $repayments[] = $list['repayed_amount'];
                            $b = 1;
                        }
                    }
                }

                if ($b == 0) {
                    $repayments[] = 0;
                }

                if ($apply) {
                    foreach ($apply as $list) {
                        if ($list['dates'] == $lth) {
                            $applys[] = $list['count'];
                            $c = 1;
                        }
                    }
                }

                if ($c == 0) {
                    $applys[] = 0;
                }

                if ($checkRefuse) {
                    foreach ($checkRefuse as $list) {
                        if ($list['dates'] == $lth) {
                            $refuses[] = $list['count'];
                            $d = 1;
                        }
                    }
                }

                if ($d == 0) {
                    $refuses[] = 0;
                }

                if ($reviewRefuse) {
                    foreach ($reviewRefuse as $list) {
                        if ($list['dates'] == $lth) {
                            $refuses[$i] += $list['count'];
                        }
                    }
                }

                $i++;
            }
        }
        $data = [
          'loan' => $loans,
          'repayment' => $repayments,
          'apply' => $applys,
          'refuse' => $refuses
        ];
        return $data;
    }

    /**
     * 每日统计
     * @return string
     */
    public function actionDailyStatistics()
    {
        $results = $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $start = $request->get('start', ''); // 申请起始时间
        $end = $request->get('end', ''); // 申请截止时间
        $results = StatisticsModel::getDailyStatisticsList($offset, $limit, $start, $end);
        foreach ($results['result'] as $row) {
            $data[] = [
                'id' => $row->id,
                'created_at' => $row->created_at, // 日期
                'user_count' => $row->user_count, // 新增用户
                'loan_count' => $row->loan_count, // 借款笔数
                'lend_count' => $row->lend_count, // 放款笔数
                'overdue_count' => $row->overdue_count, // 逾期笔数
                //'urge_loan_count' => $row->urge_loan_count, // 催收笔数
                'urge_count' => $row->urge_count, // 催收次数
                'bad_count' => $row->bad_count, // 坏账笔数
                'lend_amount' => $row->lend_amount, // 放款金额
                'repayment_amount' => $row->repayment_amount, // 还款金额
                'loan_interest' => $row->loan_interest, // 借款息费
                'overdue_amount' => $row->overdue_amount, // 逾期金额
                'overdue_penalty' => $row->overdue_penalty, // 逾期罚息
                'bad_amount' => $row->bad_amount, // 坏账金额
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $results['count'],
            'results' => $data
        ]);
    }

    /**
     *
     * 借款统计
     * @return string
     */
    public function actionLoanStatistics()
    {
        $results = $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $start = $request->get('start', ''); // 申请起始时间
        $end = $request->get('end', ''); // 申请截止时间
        $results = StatisticsModel::getDailyStatisticsList($offset, $limit, $start, $end);
        foreach ($results['result'] as $row) {
            $data[] = [
                'id' => $row->id,
                'created_at' => $row->created_at, // 日期
                'loan_user' => $row->loan_user,// 借款人数
                'lend_user' => $row->lend_user,// 放款人数
                'loan_count' => $row->loan_count, // 借款笔数
                'lend_count' => $row->lend_count, // 放款笔数
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $results['count'],
            'results' => $data
        ]);
    }

    /**
     * 还款统计
     * @return string
     */
    public function actionRepaymentStatistics()
    {
        // 还款笔数 逾期还款笔数 逾期还款笔数占比 还款金额 逾期还款金额 逾期还款金额占比
        $results = $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $start = $request->get('start', ''); // 申请起始时间
        $end = $request->get('end', ''); // 申请截止时间
        $results = StatisticsModel::getDailyStatisticsList($offset, $limit, $start, $end);
        foreach ($results['result'] as $row) {
            $data[] = [
                'id' => $row->id,
                'created_at' => $row->created_at, // 日期
                'repayment_count' => $row->repayment_count, // 还款笔数
                'repayment_overdue_count' => $row->repayment_overdue_count, // 逾期还款笔数
                'repayment_amount' => $row->repayment_amount, // 还款金额
                'repayment_overdue_amount' => $row->repayment_overdue_amount, // 逾期还款金额
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $results['count'],
            'results' => $data
        ]);
    }

    /**
     * 逾期统计
     * @return string
     */
    public function actionOverdueStatistics()
    {
        // ID , 日期，逾期笔数，逾期金额，逾期罚息催收笔数，催收次数，催收成功，催收成功率，坏账数
        $results = $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $start = $request->get('start', ''); // 申请起始时间
        $end = $request->get('end', ''); // 申请截止时间
        $results = StatisticsModel::getDailyStatisticsList($offset, $limit, $start, $end);
        foreach ($results['result'] as $row) {
            $data[] = [
                'id' => $row->id,
                'created_at' => $row->created_at, // 日期
                'overdue_count' => $row->overdue_count, // 逾期笔数
                'overdue_amount' => $row->overdue_amount, // 逾期金额
                'overdue_penalty' => $row->overdue_penalty, // 逾期罚息
                'urge_loan_count' => $row->urge_loan_count, // 催收笔数
                'urge_count' => $row->urge_count, // 催收次数
                'urge_success_count' => $row->urge_success_count, // 催收成功次数
                'bad_count' => $row->bad_count, // 坏账数
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $results['count'],
            'results' => $data
        ]);
    }

    /**
     * 催收统计
     * @return string
     */
    public function actionUrgeStatistics()
    {
        $results = $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $userName = $request->get('username', '');
        $realName = $request->get('real_name', '');
        $results = AdminModel::getUrgeStatics($offset, $limit, $userName, $realName); // 获取催收员列表
        // 循环催收员列表更具admin_id 进行统计
        foreach ($results['result'] as $k => $row) {
            $data[$k] = [
                'id' => $row->id, // 催收员id
                'username' => $row->username, //用户名
                'real_name' => $row->real_name, // 真实姓名
                'role_name' => $row->role->item_name, // 角色
            ];
            // 已分配笔数，等待催收笔数，催收未还笔数，（催收已还笔数，已催收回总额），剩余未催回总额=等待催收+催收未还
            $data[$k]['assigned'] = Urge::find()->where(['admin_id' => $row->id])->count(); // 已分配笔数

            $waitingUrge = UrgeModel::getUrgeStatisticsByCatcherId($row->id, ['urge.state' => UrgeModel::STATE_DEFAULT]); //催收记录状态为： 1
            //print_r( $waitingUrge);
            $data[$k]['waiting_urge_count'] = $waitingUrge['urge_count']; // 等待催收笔数
            $data[$k]['waiting_urge_amount'] = $waitingUrge['urge_amount'] ?? 0; //等待催收金额
            //催收未还笔数
            $repayingUrge = UrgeModel::getUrgeStatisticsByCatcherId($row->id, ['urge.state' => UrgeModel::STATE_REPAYING]); // 催收记录状态为： 2
            $data[$k]['repaying_urge_count'] = $repayingUrge['urge_count']; // 催收未还笔数
            $data[$k]['repaying_urge_amount'] = $repayingUrge['urge_amount'] ?? 0; // 催收未还金额

            // 催收已还
            $finishedUrge = UrgeModel::getUrgeStatisticsByCatcherId($row->id, ['urge.state' => UrgeModel::STATE_FINISHED]); // 催收已还： 3
            $data[$k]['finished_urge_count'] = $finishedUrge['urge_count']; // 催收已还笔数
            $data[$k]['finished_urge_amount'] = $finishedUrge['urge_amount'] ?? 0; // 已催回总额
            // 坏账
            $badUrge = UrgeModel::getUrgeStatisticsByCatcherId($row->id, ['urge.state' => UrgeModel::STATE_BAD]); // 坏账： 4
            $data[$k]['bad_urge_count'] = $badUrge['urge_count']; // 坏账笔数
            $data[$k]['bad_urge_amount'] = $badUrge['urge_amount'] ?? 0; // 坏账总额
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $results['count'],
            'results' => $data
        ]);
    }

    /**
     * 导出表格数据生成.csv文件
     * @return string
     */
    public function actionExportLoanExcel()
    {
        $results = $data = [];
        $request = Yii::$app->request;
        $act = $request->get('act', '');
        $page = $request->get('page','');
        if ($act == '') {
            return '导出失败Excel,请重试';
        }
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        if ($act == 'loan') { // 借款统计
            $start = $request->get('start', ''); // 申请起始时间
            $end = $request->get('end', ''); // 申请截止时间
            $results = StatisticsModel::getDailyStatisticsList($offset, $limit, $start, $end);
            foreach ($results['result'] as $row) {
                $data[] = [
                    'id' => $row->id,
                    'created_at' => $row->created_at, // 日期
                    'loan_user' => $row->loan_user,// 借款人数
                    'lend_user' => $row->lend_user,// 放款人数
                    'loan_lend_user_rate' => $row->loan_user > 0 ? number_format($row->lend_user / $row->loan_user * 100, 2) : '--', // 借款人数通过率
                    'loan_count' => $row->loan_count, // 借款笔数
                    'lend_count' => $row->lend_count, // 放款笔数
                    'loan_lend_count_rate' => $row->loan_count > 0 ? number_format($row->lend_count / $row->loan_count * 100, 2) : '--', // 借款笔数通过率

                ];
            }
            $filename = '借款统计';
            $header = ['ID', '日期', '借款人数', '放款人数', '借款人数通过率(%)', '借款人数', '放款人数', '借款笔数通过率(%)'];
            // $index = ['id', 'created_at','loan_user','lend_user','loan_lend_user_rate','loan_count','lend_count','loan_lend_count_rate'];
        } elseif ($act == 'repayment') { // 还款统计
            $start = $request->get('start', ''); // 申请起始时间
            $end = $request->get('end', ''); // 申请截止时间
            $results = StatisticsModel::getDailyStatisticsList($offset, $limit, $start, $end);
            foreach ($results['result'] as $row) {
                $data[] = [
                    'id' => $row->id,
                    'created_at' => $row->created_at, // 日期
                    'repayment_count' => $row->repayment_count, // 还款笔数
                    'repayment_overdue_count' => $row->repayment_overdue_count, // 逾期还款笔数
                    'repayment_overdue_rate' =>  $row->repayment_count > 0 ? number_format($row->repayment_overdue_count /  $row->repayment_count * 100, 2) : '--', // 逾期还款占比
                    'repayment_amount' => $row->repayment_amount, // 还款金额
                    'repayment_overdue_amount' => $row->repayment_overdue_amount, // 逾期还款金额
                    'repayment_overdue_amount_rate' =>  $row->repayment_amount > 0 ? number_format($row->repayment_overdue_amount /  $row->repayment_amount * 100, 2) : '--', // 逾期还款金额占比
                ];
            }
            $filename = '还款统计';
            $header = ['ID', '日期', '还款笔数', '逾期还款笔数', '逾期还款笔数占比(%)', '还款金额(元)', '逾期还款金额(元)', '逾期还款金额占比(%)'];
            // $index = ['id', 'created_at', 'repayment_count', 'repayment_overdue_count', 'repayment_overdue_rate', 'repayment_amount', 'repayment_overdue_amount', 'repayment_overdue_amount_rate'];
        } elseif ($act == 'overdue') { // 逾期统计
            $start = $request->get('start', ''); // 申请起始时间
            $end = $request->get('end', ''); // 申请截止时间
            $results = StatisticsModel::getDailyStatisticsList($offset, $limit, $start, $end);
            foreach ($results['result'] as $row) {
                $data[] = [
                    'id' => $row->id,
                    'created_at' => $row->created_at, // 日期
                    'overdue_count' => $row->overdue_count, // 逾期笔数
                    'overdue_amount' => $row->overdue_amount, // 逾期金额
                    'overdue_penalty' => $row->overdue_penalty, // 逾期罚息
                    'urge_loan_count' => $row->urge_loan_count, // 催收笔数
                    'urge_count' => $row->urge_count, // 催收次数
                    'urge_success_count' => $row->urge_success_count, // 催收成功次数
                    'urge_success_rate' => $row->urge_count > 0 ? number_format($row->urge_success_count /  $row->urge_count * 100, 2) : '--',// 催收成功率
                    'bad_count' => $row->bad_count, // 坏账数
                ];
            }
            $filename = '逾期统计';
            $header = ['ID', '日期', '逾期笔数', '逾期金额(元)', '逾期罚息(元)', '催收笔数', '催收次数', '催收成功次数', '催收成功率(%)', '坏账数'];
            // $index = ['id', 'created_at', 'overdue_count', 'overdue_amount', 'overdue_penalty', 'urge_loan_count', 'urge_count', 'urge_success_count', 'urge_success_rate', 'bad_count'];
        } elseif ($act == 'daily') { // 每日统计
            $start = $request->get('start', ''); // 申请起始时间
            $end = $request->get('end', ''); // 申请截止时间
            $results = StatisticsModel::getDailyStatisticsList($offset, $limit, $start, $end);
            foreach ($results['result'] as $row) {
                $data[] = [
                    'id' => $row->id,
                    'created_at' => $row->created_at, // 日期
                    'user_count' => $row->user_count, // 新增用户
                    'loan_count' => $row->loan_count, // 借款笔数
                    'lend_count' => $row->lend_count, // 放款笔数
                    'pass_rate' => $row->loan_count > 0 ? number_format($row->lend_count/$row->loan_count * 100, 2) : '--', // 通过率
                    'overdue_count' => $row->overdue_count, // 逾期笔数
                    //'urge_loan_count' => $row->urge_loan_count, // 催收笔数
                    'urge_count' => $row->urge_count, // 催收次数
                    'bad_count' => $row->bad_count, // 坏账笔数
                    'lend_amount' => $row->lend_amount, // 放款金额
                    'repayment_amount' => $row->repayment_amount, // 还款金额
                    'loan_interest' => $row->loan_interest, // 借款息费
                    'overdue_amount' => $row->overdue_amount, // 逾期金额
                    'overdue_penalty' => $row->overdue_penalty, // 逾期罚息
                    'bad_amount' => $row->bad_amount, // 坏账金额
                ];
            }
            $filename = '每日统计';
            $header = ['ID', '日期', '新增用户数', '借款笔数', '放款笔数', '通过率(%)', '逾期笔数', '催收次数', '坏账笔数', '放款金额(元)', '还款金额(元)', '借款息费金额(元)', '逾期金额(元)', '逾期罚息(元)', '坏账金额(元)'];
            // $index = ['id', 'created_at', 'user_count', 'loan_count', 'lend_count', 'pass_rate', 'overdue_count', 'urge_count', 'bad_count', 'lend_amount', 'repayment_amount', 'loan_interest', 'overdue_amount', 'overdue_penalty', 'bad_amount'];
        } elseif ($act == 'urge') {
            $userName = $request->get('username', '');
            $realName = $request->get('real_name', '');
            $results = AdminModel::getUrgeStatics($offset, $limit, $userName, $realName); // 获取催收员列表
            // 循环催收员列表更具admin_id 进行统计
            foreach ($results['result'] as $k => $row) {
                $data[$k] = [
                    'id' => $row->id, // 催收员id
                    'username' => $row->username, //用户名
                    'real_name' => $row->real_name, // 真实姓名
                    'role_name' => $row->role->item_name, // 角色
                ];
                // 已分配笔数，等待催收笔数，催收未还笔数，（催收已还笔数，已催收回总额），剩余未催回总额=等待催收+催收未还
                $data[$k]['assigned'] = Urge::find()->where(['admin_id' => $row->id])->count(); // 已分配笔数

                $waitingUrge = UrgeModel::getUrgeStatisticsByCatcherId($row->id, ['urge.state' => UrgeModel::STATE_DEFAULT]); //催收记录状态为： 1

                $data[$k]['waiting_urge_count'] = $waitingUrge['urge_count']; // 等待催收笔数
                //$data[$k]['waiting_urge_amount'] = $waitingUrge['urge_amount'] ?? 0; //等待催收金额
                //催收未还笔数
                $repayingUrge = UrgeModel::getUrgeStatisticsByCatcherId($row->id, ['urge.state' => UrgeModel::STATE_REPAYING]); // 催收记录状态为： 2
                $data[$k]['repaying_urge_count'] = $repayingUrge['urge_count']; // 催收未还笔数
                //$data[$k]['repaying_urge_amount'] = $repayingUrge['urge_amount'] ?? 0; // 催收未还金额

                // 催收已还
                $finishedUrge = UrgeModel::getUrgeStatisticsByCatcherId($row->id, ['urge.state' => UrgeModel::STATE_FINISHED]); // 催收已还： 3
                $data[$k]['finished_urge_count'] = $finishedUrge['urge_count']; // 催收已还笔数
                $data[$k]['finished_urge_amount'] = $finishedUrge['urge_amount'] ?? 0; // 已催回总额
                $data[$k]['un_urge_amount'] = ($waitingUrge['urge_amount'] ?? 0) + ($repayingUrge['urge_amount'] ?? 0); //剩余未催回总额
                // 坏账
                $badUrge = UrgeModel::getUrgeStatisticsByCatcherId($row->id, ['urge.state' => UrgeModel::STATE_BAD]); // 坏账： 4
                $data[$k]['bad_urge_count'] = $badUrge['urge_count']; // 坏账笔数
                $data[$k]['bad_urge_amount'] = $badUrge['urge_amount'] ?? 0; // 坏账总额

            }
            $filename = '催收统计';
            $header = ['ID', '登录名', '真实姓名', '角色', '已分配笔数', '等待催收笔数', '催收未还笔数', '已还款笔数', '已催收总额(元)', '剩余未催回总额(元)', '坏账数', '坏账总额'];
            // $index = ['id', 'username', 'real_name', 'role_name', 'assigned', 'waiting_urge_count', 'repaying_urge_count', 'finished_urge_count', 'finished_urge_amount', 'un_urge_amount', 'bad_urge_count', 'bad_urge_amount'];
        }
        exit(SystemService::exportCsv($filename.'第'.$page.'页', $header, $data));
    }
}