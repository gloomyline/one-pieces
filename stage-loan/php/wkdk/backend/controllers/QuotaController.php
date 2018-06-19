<?php
namespace backend\controllers;

use common\models\QuotaApplyModel;
use common\models\ShopModel;
use common\models\ShopQuotaApplyModel;
use common\models\UserModel;
use backend\bases\BackendController;
use yii\helpers\Json;
use Yii;

class QuotaController extends BackendController
{
    /**
     * 用户额度列表
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
        $data = UserModel::getUserList($offset, $limit, $realName, $mobile, '', '', ''); //获取用户信息列表集
        foreach ($data['result'] as $row) {
            $results[] = [
                'id' => $row->id,
                'mobile' => $row->mobile,
                'real_name' => $row->real_name,
                'available_quota' => $row->available_quota, // 可用额度，
                'fronzen_quota' => $row->fronzen_quota, // 冻结额度
                'total_quota' => $row->total_quota, // 总额度
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $data['count'],
            'results' => $results,
            'user_basic_quota' => Yii::$app->params['user_basic_quota'] ?? 0
        ]);
    }

    /**
     * 添加额度
     * @return string
     */
    public function actionQuotaApply()
    {
        $request = Yii::$app->request;
        $userId = intval($request->post('user_id', '')); // 用户id
        $applyQuota = intval($request->post('apply_quota', '')); // 申请的额度

        $user = UserModel::findUserById($userId);

        if(!$user) {
            return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => '您所操作的用户不存在！请重新添加']);
        }

        if (empty($applyQuota)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '金额仅能输入一个大于零的整数且不为空']);
        }

        $quotaApply = QuotaApplyModel::findQuotaApplyByUserId($user->id); // 查询指定用户的最新 提升额度申请信息

        if ($quotaApply && $quotaApply->state == QuotaApplyModel::STATE_AUDITING) {
            return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => '您所操作的用户提额申请正在审核中，请不要重复操作']);
        }

        $applyDetail['user_id'] = $user->id; // 用户ID
        $applyDetail['available_quota'] = $user->available_quota; // 可用额度
        $applyDetail['total_quota'] = $user->total_quota; // 总额度
        $applyDetail['state'] = QuotaApplyModel::STATE_AUDITING; // 审核状态：待审核
        $applyDetail['apply_quota'] = $applyQuota; // 申请的额度
        $applyDetail['apply_type'] = QuotaApplyModel::TYPE_BACKEND_ADD; // 额度类型：后台添加

        $res = QuotaApplyModel::addQuotaApply($applyDetail); // 添加额度申请

        if ($res) {
            return Json::encode([ "status" => self::STATUS_SUCCESS, "error_message" => '添加额度保存成功，请等待系统审核']);
        }
        return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => '保存失败，请重试']);
    }

    /**
     * 额度审核列表
     * @return string
     */
    public function actionAuditList()
    {
        $results = $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $mobile = $request->get('mobile','');
        $realName = $request->get('real_name','');
        $state = QuotaApplyModel::STATE_AUDITING; // 待审核状态
        $results = QuotaApplyModel::quotaApplyList($offset, $limit, $realName, $mobile, $state, '', ['quota_apply.id' => SORT_DESC]); // 获取额度审核列表对象数据
        foreach ($results['result'] as $row) {
            $data[] = [
                'id' => $row->id,
                'mobile' => $row->user->mobile, // 用户名
                'real_name' => $row->user->real_name, // 用户真实姓名
                'available_quota' => $row->available_quota, // 可用额度，
                'fronzen_quota' => $row->total_quota - $row->available_quota, // 冻结额度
                'total_quota' => $row->total_quota, // 总额度
                'apply_quota' => $row->apply_quota, // 申请的额度
                'apply_type' => $row->apply_type, // 申请的类型
                'state' => $row->state,
                'created_at' => $row->created_at,
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $results['count'],
            'results' => $data
        ]);
    }

    /**
     * 审核额度申请操作用户
     * @return string
     */
    public function actionAuditQuota()
    {
        $adminId = Yii::$app->user->id;
        $request = Yii::$app->request;
        $quotaApplyId = $request->post( 'quota_apply_id', 0);
        $state = $request->post('state', '');

        $content = trim($request->post('content', ''));

        $quotaApply = QuotaApplyModel::findQuotaApplyAuditById(intval($quotaApplyId));

        if(!$quotaApply) {
            return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => '您所操作的记录不存在！请操作']);
        }

        if (intval($state) == QuotaApplyModel::STATE_AUDIT_SUCCESS) { // 审核成功
            $allowQuota = $request->post('allow_quota', '');
            if ($allowQuota == '') {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '金额仅能输入一个整数且不为空']);
            }

            if ($quotaApply->apply_type != QuotaApplyModel::TYPE_USER_APPLY) { // 非用户申请执行的操作 非用户申请通过的金额
                if ($quotaApply->apply_quota > 0) {
                    $allowQuota = $allowQuota;
                } else {
                    $allowQuota = -$allowQuota;
                }
                // 通过额度绝对值不能大于申请额度
                if(abs($quotaApply->apply_quota) < intval($allowQuota)) {
                    return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => '通过金额填写不能大于'.abs($quotaApply->apply_quota).'元']);
                }
            }
        }
        if ($content == '') {
            return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => '备注填写不能为空']);
        }

        // 1.审核通过，审核人员id state=1 通过金额 = 传入的通过金额，更新user表总额度 备注
        // 2.审核失败，审核人员id state=2 备注
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $quotaApply->admin_id = $adminId;
            $quotaApply->memo = $content;
            // 审核通过-失败
            if (intval($state) == QuotaApplyModel::STATE_AUDIT_SUCCESS) {
                $quotaApply->state  = QuotaApplyModel::STATE_AUDIT_SUCCESS;
                $quotaApply->allow_quota = $allowQuota;
                // 同步更新用户表总额度，可用额度
                if (!UserModel::updateUserQuota($quotaApply->user_id, $allowQuota)) {
                    return Json::encode([
                        "status" => self::STATUS_FAILURE,
                        "error_message" => "该操作将使用户的总额度或可用额度小于零，请检查提交的数据，重新提交",
                    ]);
                }
            } elseif (intval($state) == QuotaApplyModel::STATE_AUDIT_FAILURE) {
                $quotaApply->state = QuotaApplyModel::STATE_AUDIT_FAILURE;
            }
            if (!$quotaApply->save()) {
                return Json::encode([
                    "status" => self::STATUS_FAILURE,
                    "error_message" => "额度审核操作失败!请重试",
                ]);
            }
            $transaction->commit();
            return Json::encode([ "status" => self::STATUS_SUCCESS, "error_message" => '操作成功']);
        } catch (yii\db\IntegrityException $e) {
            Yii::error($e);
            $transaction->rollBack();
            return Json::encode([
                "status" => self::STATUS_FAILURE,
                "error_message" => "系统故障了, 请重试",
            ]);
        }
    }

    /**
     * 额度记录列表
     * @return string
     */
    public function actionAuditQuotaLog()
    {
        $results = $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $mobile = $request->get('mobile','');
        $realName = $request->get('real_name','');
        $type = $request->get('type', '');
        $state = [QuotaApplyModel::STATE_AUDIT_SUCCESS, QuotaApplyModel::STATE_AUDIT_FAILURE]; // 审核结束
        $results = QuotaApplyModel::quotaApplyList($offset, $limit, $realName, $mobile, $state, $type);//获取额度审核列表，跟新时间排序
        foreach ($results['result'] as $row) {
            $data[] = [
                'id' => $row->id,
                'mobile' => $row->user->mobile, // 用户名
                'real_name' => $row->user->real_name, // 用户真实姓名
                'admin_name' => $row->admin->username, // 审核人员名字
                'apply_quota' => $row->apply_quota, // 申请的额度
                'allow_quota' => $row->allow_quota, // 通过额度
                'apply_type' => $row->apply_type, // 申请的类型
                'state' => $row->state,
                'updated_at' => $row->updated_at,
                'memo' => $row->memo, // 备注信息
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $results['count'],
            'results' => $data
        ]);
    }

    /**
     * 商户额度列表
     * @return string
     */
    public function actionShopQuota()
    {
        $results = $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $shopNo = $request->get('shop_no', '');
        $shopName = $request->get('shop_name', '');
        $state = ShopModel::STATE_AUDIT_PASS; // 通过审核的商户
        $results = ShopModel::getShopList($offset, $limit, $shopName, $shopNo, $state);

        foreach ($results['result'] as $row) {
            $data[] = [
                'id' => $row->id,
                'shop_no' => $row->shop_no,
                'shop_name' => $row->shop_name,
                'total_quota' => $row->total_quota, // 总额度
                'daily_limit_quota' => $row->daily_limit_quota, // 单日限额
                'single_limit_quota' => $row->single_limit_quota, // 单笔限额
                'available_quota' => $row->available_quota, // 商户可用额度
                'init_total_quota' => $row->init_total_quota, // 已提升额度
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $results['count'],
            'results' => $data
        ]);
    }

    /**
     * 添加商户额度
     * @return string
     */
    public function actionShopQuotaApply()
    {
        $request = Yii::$app->request;
        $shopId = intval($request->get('shop_id', '')); // 商户id
        $shop = ShopModel::findShopByShopId($shopId);
        if(!$shop) {
            return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => '您所操作的商户不存在！请重新添加']);
        }

        $shopQuotaApply = ShopQuotaApplyModel::findAuditingOneByShopId($shop->id);
        if ($shopQuotaApply) {
            return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => '您所操作的商户提额申请正在审核中，请不要重复操作']);
        }

        $applyTotal = intval($request->post('apply_total', ''));
        if (!isset($applyTotal)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '总额度金额仅能输入一个不小于零的整数且不为空']);
        }
        $applyDailyLimit= intval($request->post('apply_daily_limit', ''));
        if (!isset($applyDailyLimit)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '单日限额金额仅能输入一个不小于零的整数且不为空']);
        }
        $applySingleLimit= intval($request->post('apply_single_limit', ''));
        if (!isset($applySingleLimit)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '单笔限额金额仅能输入一个不小于零的整数且不为空']);
        }
        // 总额度》单日限额》单笔限额
        if (($shop->single_limit_quota + $applySingleLimit) > ($shop->daily_limit_quota + $applyDailyLimit)) { // 如果单笔限额大于单日限额时
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '您所提交的表单将造成单笔限额大于单日限额的情况，请调整输入的金额']);
        }
        if (($shop->daily_limit_quota + $applyDailyLimit) > ($shop->total_quota + $applyTotal)) { // 如果单日限额大于总额时
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '您所提交的表单将造成单日限额大于总额度的情况，请调整输入的金额']);
        }
        $applyDetail['shop_id'] = $shop->id; // 商户ID
        $applyDetail['available_quota'] = $shop->available_quota; // 可用额度
        $applyDetail['total_quota'] = $shop->total_quota; // 总额度
        $applyDetail['daily_limit_quota'] = $shop->daily_limit_quota; // 单日限额
        $applyDetail['single_limit_quota'] = $shop->single_limit_quota; // 单日限额
        $applyDetail['state'] = ShopQuotaApplyModel::STATE_AUDITING; // 审核状态：待审核
        $applyDetail['apply_total'] = $applyTotal; // 申请的总额度
        $applyDetail['apply_daily_limit'] = $applyDailyLimit; // 申请的单日限额
        $applyDetail['apply_single_limit'] = $applySingleLimit; // 申请的单笔限额
        $applyDetail['apply_type'] = ShopQuotaApplyModel::TYPE_BACKEND_ADD; // 额度类型：后台添加

        $res = ShopQuotaApplyModel::add($applyDetail); // 添加商户额度申请

        if ($res) {
            return Json::encode([ "status" => self::STATUS_SUCCESS, "error_message" => '添加商户额度保存成功，请等待系统审核']);
        }
        return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => '保存失败，请重试']);
    }

    /**
     * 商户审核额度列表
     * @return string
     */
    public function actionShopQuotaAuditList()
    {
        $results = $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $shopName = $request->get('shop_name','');
        $shopNo = $request->get('shop_no','');
        $state = ShopQuotaApplyModel::STATE_AUDITING; // 待审核状态
        $results = ShopQuotaApplyModel::shopQuotaApplyList($offset, $limit, $shopName, $shopNo, $state, ['shop_quota_apply.id' => SORT_DESC]);
        foreach ($results['result'] as $row) {
            $data[] = [
                'id' => $row->id,
                'shop_id' => $row->shop_id,
                'shop_name' => $row->shop->shop_name, // 商户名称
                'shop_no' => $row->shop->shop_no, // 商户号
                'available_quota' => $row->available_quota, // 可用额度，
                // 'fronzen_quota' => $row->total_quota - $row->available_quota, // 冻结额度
                'total_quota' => $row->total_quota, // 总额度
                'daily_limit_quota' => $row->daily_limit_quota,
                'single_limit_quota' => $row->single_limit_quota,
                'apply_total' => $row->apply_total, // 申请的总
                'apply_daily_limit' => $row->apply_daily_limit, // 申请的单日额度
                'apply_single_limit' => $row->apply_single_limit, // 申请的单笔限额
                'state' => $row->state,
                'created_at' => $row->created_at,
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $results['count'],
            'results' => $data
        ]);
    }
    /**
     * 商户额度审核操作
     * @return string
     */
    public function actionShopQuotaAudit()
    {
        $request = Yii::$app->request;
        $shopQuotaApplyId = intval($request->get('id', ''));
        $shopQuotaApply = ShopQuotaApplyModel::findOneById($shopQuotaApplyId);
        if (!$shopQuotaApply || ($shopQuotaApply->state == ShopQuotaApplyModel::STATE_AUDITED)) {
            return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => '参数错误']);
        }
        $data = [];

        $stateTotal = intval($request->post('state_total', ''));
        if (!$stateTotal) {
            return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => '参数错误']);
        } else {
            // 通过
            if ($stateTotal == ShopQuotaApplyModel::STATE_TOTAL_AUDIT_SUCCESS) {
                $allowTotal = $request->post('allow_total', 0);
                if (!(is_numeric($allowTotal) && is_int(abs($allowTotal)))) {
                    return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => '总额度通过金额请输入整数']);
                }
                $data['state_total'] = ShopQuotaApplyModel::STATE_TOTAL_AUDIT_SUCCESS;
                $data['allow_total'] = $allowTotal;
            } else if ($stateTotal == ShopQuotaApplyModel::STATE_TOTAL_AUDIT_FAILURE) {
                $data['state_total'] = ShopQuotaApplyModel::STATE_TOTAL_AUDIT_FAILURE;
                $data['allow_total'] = 0;
            }
        }
        // 单日限额
        $stateDailyLimit = intval($request->post('state_daily_limit', ''));
        if (!$stateDailyLimit) {
            return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => '参数错误']);
        } else {
            if ($stateDailyLimit == ShopQuotaApplyModel::STATE_DAILY_LIMIT_AUDIT_SUCCESS) {
                $allowDailyLimit = $request->post('allow_daily_limit', 0);
                if (!(is_numeric($allowDailyLimit) && is_int(abs($allowDailyLimit)))) {
                    return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => '单日限额通过金额请输入整数']);
                }
                $data['state_daily_limit'] = ShopQuotaApplyModel::STATE_DAILY_LIMIT_AUDIT_SUCCESS;
                $data['allow_daily_limit'] = $allowDailyLimit;
            } else if ($stateDailyLimit == ShopQuotaApplyModel::STATE_DAILY_LIMIT_AUDIT_FAILURE) {
                $data['state_daily_limit'] = ShopQuotaApplyModel::STATE_DAILY_LIMIT_AUDIT_FAILURE;
                $data['allow_daily_limit'] = 0;
            }
        }

        // 单笔限额
        $stateSingleLimit = intval($request->post('state_single_limit', ''));
        if (!$stateSingleLimit) {
            return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => '参数错误']);
        } else {
            if ($stateSingleLimit == ShopQuotaApplyModel::STATE_SINGLE_LIMIT_AUDIT_SUCCESS) {
                $allowSingleLimit = $request->post('allow_single_limit', 0);
                if (!(is_numeric($allowSingleLimit) && is_int(abs($allowSingleLimit)))) {
                    return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => '单笔限额通过金额请输入整数']);
                }
                $data['state_single_limit'] = ShopQuotaApplyModel::STATE_SINGLE_LIMIT_AUDIT_SUCCESS;
                $data['allow_single_limit'] = $allowSingleLimit;
            } else if ($stateSingleLimit == ShopQuotaApplyModel::STATE_SINGLE_LIMIT_AUDIT_FAILURE) {
                $data['state_single_limit'] = ShopQuotaApplyModel::STATE_SINGLE_LIMIT_AUDIT_FAILURE;
                $data['allow_single_limit'] = 0;
            }
        }
        // 总额度>单日限额>单笔限额>0
        if (($shopQuotaApply->single_limit_quota + $data['allow_single_limit']) > ($shopQuotaApply->daily_limit_quota + $data['allow_daily_limit'])) { // 如果单笔限额大于单日限额时
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '您所提交的表单将造成单笔限额大于单日限额的情况，请调整输入的金额']);
        }
        if (($shopQuotaApply->daily_limit_quota + $data['allow_daily_limit']) > ($shopQuotaApply->total_quota + $data['allow_total'])) { // 如果单日限额大于总额时
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '您所提交的表单将造成单日限额大于总额度的情况，请调整输入的金额']);
        }
        if (($shopQuotaApply->single_limit_quota + $data['allow_single_limit']) < 0) { // 如果单笔限额小于0
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '您所提交的表单将造成商户单笔限额小于零的情况，请调整输入的金额']);
        }
        if (($shopQuotaApply->daily_limit_quota + $data['allow_daily_limit']) < 0) { // 如果单日限额小于0
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '您所提交的表单将造成商户单日限额小于零的情况，请调整输入的金额']);
        }
        if (($shopQuotaApply->total_quota + $data['allow_total']) < 0) { // 如果总授信限额小于0
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '您所提交的表单将造成商户总授信额度小于零的情况，请调整输入的金额']);
        }
        $memo = trim($request->post('memo', ''));
        if ($memo == '') {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请填写备注信息']);
        }
        $data['memo'] = $memo;
        $data['admin_id'] = Yii::$app->user->id;
        $data['state'] = ShopQuotaApplyModel::STATE_AUDITED; // 申请记录状态->已审核
        $transaction =Yii::$app->db->beginTransaction();
        try{
            // 修改审核信息
            if (!ShopQuotaApplyModel::update($shopQuotaApply->id, $data)) {
                throw new \Exception('商户申请记录保存失败');
            }
            // 修改shop表
            if (!ShopModel::updateQuota($shopQuotaApply->shop_id, $data['allow_total'], $data['allow_daily_limit'], $data['allow_single_limit'])) {
                throw new \Exception('商户额度跟新失败');
            }
            $transaction->commit();
            return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
        }catch (\Exception $e){
            $transaction->rollBack();
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => $e->getMessage()]);
        }

    }

    /**
     * 商户额度记录列表
     * @return string
     */
    public function actionShopQuotaLogList()
    {
        $results = $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $shopName = $request->get('shop_name','');
        $shopNo = $request->get('shop_no','');
        $state = ShopQuotaApplyModel::STATE_AUDITED; // 已审核
        $results = ShopQuotaApplyModel::shopQuotaApplyList($offset, $limit, $shopName, $shopNo, $state, ['shop_quota_apply.id' => SORT_DESC]);
        foreach ($results['result'] as $row) {
            $data[] = [
                'id' => $row->id,
                'shop_id' => $row->shop_id,
                'shop_name' => $row->shop->shop_name, // 商户名称
                'shop_no' => $row->shop->shop_no, // 商户号
                'available_quota' => $row->available_quota, // 可用额度，
                // 'fronzen_quota' => $row->total_quota - $row->available_quota, // 冻结额度
                'total_quota' => $row->total_quota, // 总额度
                'daily_limit_quota' => $row->daily_limit_quota,
                'single_limit_quota' => $row->single_limit_quota,
                'apply_total' => $row->apply_total, // 申请的总
                'apply_daily_limit' => $row->apply_daily_limit, // 申请的单日额度
                'apply_single_limit' => $row->apply_single_limit, // 申请的单笔限额
                'allow_total' => $row->allow_total,
                'allow_daily_limit' => $row->allow_daily_limit,
                'allow_single_limit' => $row->allow_single_limit,
                'state_total' => $row->state_total,
                'state_daily_limit' => $row->state_daily_limit,
                'state_single_limit' => $row->state_single_limit,
                'auditor' => $row->admin->username,
                'memo' => $row->memo,
                'updated_at' => $row->created_at,
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $results['count'],
            'results' => $data
        ]);
    }

}