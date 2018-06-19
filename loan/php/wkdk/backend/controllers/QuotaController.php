<?php
namespace backend\controllers;

use common\models\QuotaApplyModel;
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
        $data = UserModel::getUserList($offset, $limit, $realName, $mobile, '', '', '', 100); //获取用户信息列表集
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
            'results' => $results
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
     * 审核额度申请操作
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
}