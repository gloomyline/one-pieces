<?php
namespace backend\controllers;

use backend\bases\BackendController;
use backend\models\AdminModel;
use common\bases\CommonService;
use common\extend\sms\AlidayuSms;
use common\extend\sms\MsgTemplate;
use common\models\BudgetPlan;
use common\models\BudgetPlanModel;
use common\models\LoanModel;
use common\models\MobileLogModel;
use common\models\ProductModel;
use common\models\Urge;
use common\models\UrgeLog;
use common\models\UrgeLogModel;
use common\models\UrgeModel;
use common\models\UserMobileReportModel;
use common\services\SiteService;
use yii\helpers\Json;
use Yii;

class OverdueController extends BackendController
{


    public function actionIndex()
    {
        $results = $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $mobile = $request->get('mobile','');
        $realName = $request->get('real_name','');
        $beginAt = $request->get('start_at', ''); // 计划还款起始时间
        $endAt = $request->get('end_at', ''); // 计划还款截止时间
        $budgetPlanId = $request->get('id', ''); // 逾期计划id
        $results = BudgetPlanModel::getOverdueAll($offset, $limit, $realName, $mobile, $beginAt, $endAt, $budgetPlanId);

        // 获得现金分期产品
        $productCash = ProductModel::getProductByCondition(['type' => ProductModel::TYPE_CASH, 'active' => ProductModel::STATE_ACTIVE]);
        // 获得消费分期产品
        $productConsumption = ProductModel::getProductByCondition(['type' => ProductModel::TYPE_CONSUMPTION, 'active' => ProductModel::STATE_ACTIVE]);
        foreach ($results['result'] as $row) {
            $overdueAmount = $shouldRepaymentAmount = $overdueDays = 0;
            $overdueDays = abs(SiteService::getDaysDistance($row->planned_repayment_at)); //逾期天数
            // 逾期罚金 = 逾期本金 * 逾期费率 * 逾期天数
            // 逾期金额计算与消费类型有关
            if ($row->loan->type == ProductModel::TYPE_CASH) { // 现金分期
                $overdueAmount = $row->principal * $row->loan->overdue_rate * ($overdueDays < $productCash->limit_overdue_days ? $overdueDays : $productCash->limit_overdue_days); // 逾期金额
            } elseif ($row->loan->type == ProductModel::TYPE_CONSUMPTION) { // 消费分期
                $overdueAmount = $row->principal * $row->loan->overdue_rate * ($overdueDays < $productConsumption->limit_overdue_days ? $overdueDays : $productConsumption->limit_overdue_days); // 逾期金额
            }
            $productName = '--';
            if ($row->loan->orderDetail) {

                $productTitle = [];
                foreach ($row->loan->orderDetail as $lt) {
                    $productTitle[] = $lt->title ;
                }
                $productName = implode(',', $productTitle);
            }

            $data[] = [
                'id' => $row->id, // 分期计划id
                'loan_id' => $row->loan->id, // 借款id
                'encoding' => $row->loan->encoding ?? '', // 借款编号
                'type' => $row->loan->type ?? '', // 借款类型
                'shop_name' => $row->loan->shop->shop_name ?? '--',
                'product_name' => $productName ?? '--', // 商品名称
                'real_name' => $row->user->real_name ?? '', // 真实姓名
                'mobile' => $row->user->mobile ?? '', // 手机号码
                'arrival_amount' => $row->loan->arrival_amount ?? 0, // 借款金额
                'period' => $row->term . '/' . $row->loan->period, // 当前期数/总期数
                'lending_at' => $row->loan->lending_at ?? '', // 放款时间
                'planned_repayment_at' => $row->planned_repayment_at, // 计划还款时间
                'overdue_amount' => number_format($overdueAmount,2), // 逾期费用
                'overdue_days' => $overdueDays, // 逾期天数
                'monthly' => $row->monthly, // 本期应还金额 -> 月供
                'total_repayment_amount' => number_format($row->monthly + $overdueAmount,2),
                'urge_id' => $row->urge->id ?? '',
                'admin_id' => $row->urge->admin_id ?? '',
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $results['count'],
            'results' => $data
        ]);
    }

    /**
     * 获取催收员列表 -- 更改为获取所有管理员
     * @return string
     */
    public function actionStaff()
    {
        $result = $data = [];
        $result = AdminModel::getAdmins();
        $roles = Yii::$app->authManager->getRoles();
        foreach ($roles as $key => $role) {
            foreach ($result as $staff) {
                $data[$key]['label'] = $role->name;
                if ($role->name == $staff->role->item_name) {
                    $data[$key]['options'][] = [
                        'admin_id' => (int)$staff->id,
                        'admin_name' => $staff->real_name,
                    ];
                }
            }
        }
        return Json_encode([
            'status' => self::STATUS_SUCCESS,
            'results' => $data,
        ]);
    }

    /**
     * 逾期记录催收分配操作
     * @return string
     */
    public function actionAssign()
    {
        $request = Yii::$app->request;
        $loanId = $request->post('loan_id', '');
        $adminId = $request->post('admin_id', '');
        $budgetPlanId = $request->post('budget_plan_id', '');
        if (empty($loanId)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请选择要操作的逾期记录！']);
        }
        if (empty($adminId)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请选择催收人员']);
        }
        if (empty($budgetPlanId)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        if ($model = UrgeModel::getUrgeByBudgetPlanId($budgetPlanId)) {
            $model->admin_id = $adminId;
            if (!$model->save()) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '保存失败']);
            }
        } else{
            $urge = new Urge();
            $urge->loan_id = $loanId;
            $urge->admin_id = $adminId;
            $urge->budget_plan_id = $budgetPlanId;
            if (!$urge->save()) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '保存失败']);
            }
        }
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    /**
     * 已分配的催收列表
     * @return string
     */
    public function actionUrgeList()
    {
        $results = $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $mobile = $request->get('mobile','');
        $realName = $request->get('real_name','');
        $beginAt = $request->get('start_at', ''); // 计划还款起始时间
        $endAt = $request->get('end_at', ''); // 计划还款截止时间
        $adminId = $request->get('staff','');
        $results = UrgeModel::getUrgeList($offset, $limit, $realName, $mobile, $adminId, '', $beginAt, $endAt);

        //print_r($results);exit();
        // 获得现金分期产品
        $productCash = ProductModel::getProductByCondition(['type' => ProductModel::TYPE_CASH, 'active' => ProductModel::STATE_ACTIVE]);
        // 获得消费分期产品
        $productConsumption = ProductModel::getProductByCondition(['type' => ProductModel::TYPE_CONSUMPTION, 'active' => ProductModel::STATE_ACTIVE]);

        foreach ($results['result'] as $row) {
            $overdueAmount = $overdueDays = 0;
            $overdueDays = abs(SiteService::getDaysDistance($row->budgetPlan->planned_repayment_at)); //逾期天数
            // 逾期罚金 = 逾期本金 * 逾期费率 * 逾期天数
            // 逾期金额计算与消费类型有关
            if ($row->loan->type == ProductModel::TYPE_CASH) { // 现金分期
                $overdueAmount = $row->budgetPlan->principal * $row->loan->overdue_rate * ($overdueDays < $productCash->limit_overdue_days ? $overdueDays : $productCash->limit_overdue_days); // 逾期金额
            } elseif ($row->loan->type == ProductModel::TYPE_CONSUMPTION) { // 消费分期
                $overdueAmount = $row->budgetPlan->principal * $row->loan->overdue_rate * ($overdueDays < $productConsumption->limit_overdue_days ? $overdueDays : $productConsumption->limit_overdue_days); // 逾期金额
            }
            $productName = '--';
            if ($row->loan->orderDetail) {
                $productName = '';
                $productTitle = [];
                foreach ($row->loan->orderDetail as $lt) {
                    $productTitle[] = $lt->title ;
                }
                $productName = implode(',', $productTitle);
            }
            $data[] = [
                'id' => $row->id,
                'encoding' => $row->loan->encoding ?? '', // 借款编号
                'type' => $row->loan->type ?? '', // 借款类型
                'shop_name' => $row->loan->shop->shop_name ?? '--', // 商户名称
                'product_name' => $productName ?? '--', // 商品名称
                'real_name' => $row->budgetPlan->user->real_name ?? '', //真实姓名
                'mobile' => $row->budgetPlan->user->mobile ?? '', // 手机号
                'principal' => $row->budgetPlan->principal, // 本期逾期本金
                'arrival_amount' => $row->loan->arrival_amount, // 借款金额
                'period' => $row->budgetPlan->term . '/' . $row->loan->period, // 当前期数/总期数
                'lending_at' => $row->loan->lending_at, // 放款时间
                'planned_repayment_at' => $row->budgetPlan->planned_repayment_at, // 计划还款时间
                'overdue_amount' => round($overdueAmount,2), // 逾期费用
                'overdue_days' => $overdueDays, // 逾期天数
                'monthly' => $row->budgetPlan->monthly, // 本期应还金额 -> 月供
                'total_repayment_amount' => round($row->budgetPlan->monthly + $overdueAmount, 2), // 本期应还总额
                'admin_name' => $row->admin->real_name ?? '', // 催收人员
                'created_at' => $row->created_at ?? '', // 分配时间
                'state' => $row->state ?? '', // 催收结果
                'urge_count' => $row->urge_count ?? 0, //催收次数
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $results['count'],
            'results' => $data
        ]);
    }

    /**
     * 查看催收日记的详情记录
     * @return string
     */
    public function actionUrgeLog()
    {
        $results = $data = [];
        $request = Yii::$app->request;
        $urgeId = $request->get('id', '');
        $results = UrgeLogModel::getUrgeLogByUrgeIdList($urgeId);
        $count = count($results); // 记录的总条数
        if (!$results) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '暂无催款记录']);
        }
        foreach ($results as $row) {
            $data[] = [
                'id' => $row->id ?? '',
                'created_at' => $row->created_at ?? '', // 催收时间
                'urge_way' => $row->urge_way ?? '', // 催收方式
                'urge_result' => $row->urge_result ?? '', // 催收结果
                'planned_repayment_at' => $row->planned_repayment_at ?? '', // 预计还款日期
                'content' => $row->content ?? '', // 催款情况说明
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $count,
            'results' => $data
        ]);
    }

    /**
     * 我的催收列表
     * @return string
     */
    public function actionUrgency()
    {
        $results = $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $mobile = $request->get('mobile','');
        $realName = $request->get('real_name','');
        $beginAt = $request->get('start_at', ''); // 计划还款起始时间
        $endAt = $request->get('end_at', ''); // 计划还款截止时间
        $state = $request->get('state',''); // 催收结果
        $adminId = Yii::$app->user->id;
        $results = UrgeModel::getUrgeList($offset, $limit, $realName, $mobile, $adminId, $state, $beginAt, $endAt);

        // 获得现金分期产品
        $productCash = ProductModel::getProductByCondition(['type' => ProductModel::TYPE_CASH, 'active' => ProductModel::STATE_ACTIVE]);
        // 获得消费分期产品
        $productConsumption = ProductModel::getProductByCondition(['type' => ProductModel::TYPE_CONSUMPTION, 'active' => ProductModel::STATE_ACTIVE]);

        foreach ($results['result'] as $row) {
            $overdueAmount = $overdueDays = $productOverdueLimit = 0;
            // 若催收已还款，逾期时间=还款时间-计划还款时间
            if ($row->budgetPlan->state == BudgetPlanModel::STATE_FINISHED) {
                $overdueDays = (strtotime(date('Y-m-d', strtotime($row->budgetPlan->repayment_at))) - strtotime($row->budgetPlan->planned_repayment_at)) / (3600 * 24);
            } else {
                $overdueDays = (strtotime(date('Y-m-d', time())) - strtotime($row->budgetPlan->planned_repayment_at)) / (3600 * 24);
            }
            if ($row->loan->type == LoanModel::TYPE_CASH) {
                $productOverdueLimit  = $productCash->limit_overdue_days;
            } elseif ($row->loan->type == LoanModel::TYPE_CONSUMPTION) {
                $productOverdueLimit = $productConsumption->limit_overdue_days;
            }
            $overdueAmount = $row->budgetPlan->principal * $row->loan->overdue_rate * ($overdueDays < $productOverdueLimit ? $overdueDays : $productOverdueLimit); // 逾期费

            $productName = '--';
            if ($row->loan->orderDetail) {

                $productTitle = [];
                foreach ($row->loan->orderDetail as $lt) {
                    $productTitle[] = $lt->title ;
                }
                $productName = implode(',', $productTitle);
            }

            $data[] = [
                'id' => $row->id,
                'encoding' => $row->loan->encoding ?? '', // 借款编号
                'loan_id' => $row->loan_id, // 借款id
                'budget_plan_id' => $row->budgetPlan->id, // 分期计划id
                'type' => $row->loan->type ?? '', // 借款类型
                'shop_name' => $row->loan->shop->shop_name ?? '--',// 商户名称
                'product_name' => $productName ?? '--',// 商品名称
                'real_name' => $row->loan->user->real_name ?? '', //真实姓名
                'mobile' => $row->loan->user->mobile ?? '', // 手机号
                'principal' => $row->budgetPlan->principal, // 本期逾期本金
                'arrival_amount' => $row->loan->arrival_amount , // 借款金额
                'period' => $row->budgetPlan->term . '/' . $row->loan->period, // 借款期数
                'lending_at' => $row->loan->lending_at, // 放款时间
                'planned_repayment_at' => $row->budgetPlan->planned_repayment_at, // 计划还款时间
                'overdue_amount' => round($overdueAmount, 2), // 逾期费用
                'overdue_days' => $overdueDays, // 逾期天数
                'monthly' => $row->budgetPlan->monthly, // 本期应还金额 -> 月供
                'total_repayment_amount' => round($row->budgetPlan->monthly + $overdueAmount, 2), // 本期应还总额
                //'admin_name' => $row['admin']['real_name'] ?? '', // 催收人员
                'created_at' => $row->created_at ?? '', // 分配时间
                'state' => $row->state ?? '', // 催收结果
                'urge_count' => $row->urge_count ?? 0, //催收次数
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $results['count'],
            'results' => $data
        ]);
    }

    /**
     * 催收操作
     * @return string
     */
    public function actionUrge()
    {
        $request = Yii::$app->request;
        $urgeId = $request->post('urge_id' , '');
        $urgeWay = $request->post('urge_way', '');
        $urgeResult = $request->post('urge_result', '');
        $plannedRepaymentAt = $request->post('planned_repayment_at', '');
        $content = $request->post('content', '');
        $urge = UrgeModel::getUrgeById($urgeId);
        if (!$urge) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        if (empty($urgeWay)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请填写催收方式']);
        }
        if (empty($urgeResult)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请填写催收结果']);
        }
        $urgeLog = new UrgeLog();
        $urgeLog->urge_id = $urgeId;
        $urgeLog->urge_way = $urgeWay;
        $urgeLog->urge_result = $urgeResult;
        if (!empty($plannedRepaymentAt) && isset($plannedRepaymentAt)) {
            $urgeLog->planned_repayment_at = date('Y-m-d', ($plannedRepaymentAt + 3600 * 24));
        }
        $urgeLog->content = $content;
        $urgeLog->admin_id = Yii::$app->user->id;
        $transaction = Yii::$app->db->beginTransaction();
        if (!$urgeLog->save()) { // 保存催收日记
            $transaction->rollBack();
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '提交失败']);
        }
        if (!UrgeModel::updateUrge($urgeId, intval($urgeResult))) { // 更新催收记录状态信息以及催收次数加一操作
            $transaction->rollBack();
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '提交失败']);
        }
        $transaction->commit();
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    /**
     * 获取该借款用户最新前十通话记录
     * @return string
     */
    public function actionUserCallRecords()
    {
        $budgetPlanId = Yii::$app->request->get('budget_plan_id', '');
        $budgetPlan = BudgetPlan::findOne(['id' => $budgetPlanId]);
        if (!$budgetPlan) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误，获取前十通话记录失败！']);
        }
        $userMobile = UserMobileReportModel::findLastSuccessMobileReportByUserId($budgetPlan->user_id);

        $data = [];
        $data = Json::decode($userMobile->content)['stati'] ?? [];

        if (isset($data) && is_array($data)) {
            foreach ($data as &$v) {
                $mobileLog = MobileLogModel::findOverdueMassByBudgetPlanIdAndMobile($budgetPlan->id, $v['mobileNo']);
                if ($mobileLog) {// 若找到记录 发送成功：1 发送失败：2 未发送：0
                    $v['send_state'] =  json_decode($mobileLog->return_message)->Message == 'OK' ? 1 : 2;
                } else {
                    $v['send_state'] = 0;
                }
            }

        }
        if (!$userMobile) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误，获取前十通话记录失败！']);
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'results' => $data,
        ]);
    }

    /**
     * 群发逾期短信通知-- 需修改模板按期数发送
     * @return string
     */
    public function actionSendOverdueGroupMessages()
    {
        $request = Yii::$app->request;
        $mobiles = $request->post('mobiles', '');
        $budgetPlanId = $request->post('budget_plan_id', '');
        if (is_array($mobiles) && !empty($mobiles)) {
            $mobiles = implode(',' , $mobiles);
        }
        //获取分期计划信息&& 订单信息
        $budgetPlan = BudgetPlanModel::findBudgetWithLoanAndUserById($budgetPlanId);
        if (!$budgetPlan || !$budgetPlan->loan) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误!']);
        }
        if ($budgetPlan->state !== BudgetPlanModel::STATE_OVERDUE) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '您所操作的记录不处于逾期状态']);
        }

        $service = new CommonService();
        $realName = $budgetPlan->user->real_name; // 真实姓名

        $plannedRepaymentAtYearMonth = date('Y-m', strtotime($budgetPlan->planned_repayment_at)); // 预计还款年月
        $monthly = $budgetPlan->monthly; // 本期月供
        $overdueDays = (strtotime(date('Y-m-d')) - strtotime($budgetPlan->planned_repayment_at)) / (3600 * 24); // 逾期天数

        $params = ['name' => $realName, 'yearmonth' => $plannedRepaymentAtYearMonth, 'account' => $monthly, 'day' =>$overdueDays]; //${name}的${yearmonth}应付金额为${account}，已过期${day}天，请转告其尽快进行操作处理。
        $result = $service->sendSmsCode(AlidayuSms::Sign, MsgTemplate::OVERDUE_MASS, $mobiles, $params, MobileLogModel::TYPE_OVERDUE_MASS , $budgetPlan->id);

        if ($result && $result['code'] == 2000) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => $result['message']]);
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'results' => [],
        ]);
    }
}