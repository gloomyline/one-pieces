<?php
namespace backend\controllers;

use backend\bases\BackendController;
use backend\models\AdminModel;
use common\bases\CommonService;
use common\extend\sms\AlidayuSms;
use common\extend\sms\MsgTemplate;
use common\models\Loan;
use common\models\LoanModel;
use common\models\MobileLogModel;
use common\models\ProductModel;
use common\models\Urge;
use common\models\UrgeLog;
use common\models\UrgeLogModel;
use common\models\UrgeModel;
use common\models\UserMobileReportModel;
use yii\helpers\Json;
use Yii;

class OverdueController extends BackendController
{
    /**
     * 获取逾期列表
     * @return string
     */
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
        $id = $request->get('id', ''); // 逾期记录id
        $results = LoanModel::getOverdueAll($offset, $limit, $realName, $mobile, $beginAt, $endAt, $id);

        $product = ProductModel::getActiveProduct();

        foreach ($results['result'] as $row) {
            $overdueAmount = $shouldRepaymentAmount = $overdueDays = 0;
            $overdueDays = (strtotime(date('Y-m-d',time())) - strtotime($row->planned_repayment_at)) / (3600 * 24);
            $overdueAmount = $row->arrival_amount * $row->overdue_rate * ($overdueDays < $product->limit_overdue_days ? $overdueDays : $product->limit_overdue_days); // 逾期金额
            $shouldRepaymentAmount = $row->interest + $row->arrival_amount; // 应还款金额
            $data[] = [
                'id' => $row->id,
                'encoding' => $row->encoding ?? '',
                'real_name' => $row->user->real_name ?? '',
                'mobile' => $row->user->mobile ?? '',
                'arrival_amount' => $row->arrival_amount,
                'period' => $row->period,
                'lending_at' => $row->lending_at,
                'planned_repayment_at' => $row->planned_repayment_at, // 计划还款时间
                'overdue_amount' => number_format($overdueAmount,2),
                'should_repayment_amount' => number_format($shouldRepaymentAmount,2),
                'overdue_days' => $overdueDays,
                'total_repayment_amount' => number_format($shouldRepaymentAmount + $overdueAmount,2),
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
        if (empty($loanId)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请选择要操作的逾期记录！']);
        }
        if (empty($adminId)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请选择催收人员']);
        }
        if ($model = UrgeModel::getUrgeByLoanId($loanId)) {
            $model->admin_id = $adminId;
            if (!$model->save()) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '保存失败']);
            }
        } else{
            $urge = new Urge();
            $urge->loan_id = $loanId;
            $urge->admin_id = $adminId;
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

        $product = ProductModel::getActiveProduct();

        foreach ($results['result'] as $row) {
            $overdueAmount = $overdueDays = 0;
            // 若催收已还款，逾期时间=还款时间-计划还款时间
            if ($row->loan->state == LoanModel::STATE_FINISHED) {
                $overdueDays = (strtotime(date('Y-m-d', strtotime($row->loan->repayment_at))) - strtotime($row->loan->planned_repayment_at)) / (3600 * 24);
            } else {
                $overdueDays = (strtotime(date('Y-m-d',time())) - strtotime($row->loan->planned_repayment_at)) / (3600 * 24);
            }
            $overdueAmount = $row->loan->arrival_amount * $row->loan->overdue_rate * ($overdueDays < $product->limit_overdue_days ? $overdueDays : $product->limit_overdue_days); // 逾期费

            $data[] = [
                'id' => $row->id,
                'encoding' => $row->loan->encoding ?? '', // 借款编号
                'real_name' => $row->loan->user->real_name ?? '', //真实姓名
                'mobile' => $row->loan->user->mobile ?? '', // 手机号
                'arrival_amount' => number_format($row->loan->arrival_amount,2), // 借款金额
                'period' => $row->loan->period, // 借款期限
                'lending_at' => $row->loan->lending_at, // 放款时间
                'planned_repayment_at' => $row->loan->planned_repayment_at, // 计划还款时间
                'overdue_amount' => number_format($overdueAmount,2), // 逾期费用
                'overdue_days' => $overdueDays, // 逾期天数
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

        $product = ProductModel::getActiveProduct(); // 当前线上的产品

        foreach ($results['result'] as $row) {
            $overdueAmount = $overdueDays = 0;
            // 若催收已还款，逾期时间=还款时间-计划还款时间
            if ($row->loan->state == LoanModel::STATE_FINISHED) {
                $overdueDays = (strtotime(date('Y-m-d', strtotime($row->loan->repayment_at))) - strtotime($row->loan->planned_repayment_at)) / (3600 * 24);
            } else {
                $overdueDays = (strtotime(date('Y-m-d', time())) - strtotime($row->loan->planned_repayment_at)) / (3600 * 24);
            }
            $overdueAmount = $row->loan->arrival_amount * $row->loan->overdue_rate * ($overdueDays < $product->limit_overdue_days ? $overdueDays : $product->limit_overdue_days); // 逾期费
            $data[] = [
                'id' => $row->id,
                'encoding' => $row->loan->encoding ?? '', // 借款编号
                'loan_id' => $row->loan_id, // 借款id
                'real_name' => $row->loan->user->real_name ?? '', //真实姓名
                'mobile' => $row->loan->user->mobile ?? '', // 手机号
                'arrival_amount' => number_format($row->loan->arrival_amount, 2), // 借款金额
                'period' => $row->loan->period, // 借款期限
                'lending_at' => $row->loan->lending_at, // 放款时间
                'planned_repayment_at' => $row->loan->planned_repayment_at, // 计划还款时间
                'overdue_amount' => number_format($overdueAmount, 2), // 逾期费用
                'overdue_days' => $overdueDays, // 逾期天数
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
        $loanId = Yii::$app->request->get('loan_id', '');
        $loan = Loan::findOne(['id' => $loanId]);
        if (!$loan) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误，获取前十通话记录失败！']);
        }
        $userMobile = UserMobileReportModel::findLastSuccessMobileReportByUserId($loan->user_id);

        $data = [];
        $data = Json::decode($userMobile->content)['stati'] ?? [];

        if (isset($data) && is_array($data)) {
            foreach ($data as &$v) {
                $mobileLog = MobileLogModel::findOverdueMassByLoanIdAndMobile($loan->id, $v['mobileNo']);
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
     * 群发逾期短信通知
     * @return string
     */
    public function actionSendOverdueGroupMessages()
    {
        $request = Yii::$app->request;
        $mobiles = $request->post('mobiles', '');
        $loanId = $request->post('loan_id', '');
        if (is_array($mobiles) && !empty($mobiles)) {
            $mobiles = implode(',' , $mobiles);
        }
        // 获取借款订单信息
        $loan = LoanModel::findLoanByLoanId($loanId);
        if (!$loan) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误!']);
        }

        $service = new CommonService();
        $realName = $loan->user->real_name; // 真实姓名
        $lendAt = $loan->lending_at; // 借款日期
        $quota = $loan->quota; // 申请金额
        $overdueDays = (strtotime(date('Y-m-d')) - strtotime($loan->planned_repayment_at)) / (3600 * 24); // 逾期天数

        $params = ['name' => $realName, 'date' => $lendAt, 'account' => $quota, 'day' =>$overdueDays]; // ${name}于${date}申请的${account}元，已过期${day}天，请转告其尽快通过平台进行操作处理。
        $result = $service->sendSmsCode(AlidayuSms::Sign, MsgTemplate::OVERDUE_MASS, $mobiles, $params, MobileLogModel::TYPE_OVERDUE_MASS ,$loanId);
        if ($result && $result['code'] == 2000) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => $result['message']]);
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'results' => [],
        ]);
    }
}