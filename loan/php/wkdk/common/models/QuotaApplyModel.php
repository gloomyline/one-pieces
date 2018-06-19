<?php
namespace common\models;

use common\bases\CommonModel;
use Yii;

class QuotaApplyModel extends CommonModel
{
    const STATE_AUDITING = 0; // 待审核
    const STATE_AUDIT_SUCCESS = 1; // 审核成功
    const STATE_AUDIT_FAILURE = 2; // 审核失败

    const TYPE_BACKEND_ADD = 0; // 额度类型：0-后台添加
    const TYPE_USER_APPLY = 1; // 额度类型：1-用户申请
    const TYPE_SYSTEM_ADD = 2; // 额度类型：2-系统添加
    /**
     * 添加提升额度申请
     * @param array $applyDetail 申请明细
     * @return boolean 是否添加成功 true-成功 false-失败
     */
    public static function addQuotaApply($applyDetail)
    {
        $quotaApply = new QuotaApply();
        $quotaApply->user_id = $applyDetail['user_id']; // 用户ID
        if (isset($applyDetail['admin_id'])) {
            $quotaApply->admin_id = $applyDetail['admin_id']; // 审核员ID
        }
        if (isset($applyDetail['apply_quota'])) {
            $quotaApply->apply_quota = $applyDetail['apply_quota']; // 申请额度
        }
        $quotaApply->available_quota = $applyDetail['available_quota']; // 可用额度
        $quotaApply->total_quota = $applyDetail['total_quota']; // 总额度
        $quotaApply->state = $applyDetail['state']; // 审核状态
        $quotaApply->apply_type = $applyDetail['apply_type']; // 额度类型
        if (isset($applyDetail['memo'])) {
            $quotaApply->memo = $applyDetail['memo']; // 备注
        }
        $quotaApply->created_at = date('Y-m-d H:i:s'); // 添加时间

        if ($quotaApply->validate()) {
            if (!$quotaApply->save()) {
                return false;
            }
            return true;
        }
        Yii::info('QuotaModel addQuotaApply Error : ' . $quotaApply->getErrors());
        return false;
    }

    /**
     * 查询指定用户的最新 提升额度申请信息
     * @param integer $userId 用户ID
     * @return array|null|\yii\db\ActiveRecord 查询的结果
     */
    public static function findQuotaApplyByUserId($userId)
    {
       return  QuotaApply::find()->where(['user_id' => $userId])->orderBy('id desc')->one();
    }

    /**
     * 获取额度申请表信息
     * @param integer $offset 查询的基准数
     * @param integer $limit 查询的数量
     * @param string $realName 用户真实姓名
     * @param string $mobile 用户名
     * @param integer $state 状态
     * @param integer $type 额度类型
     * @param array $orderBy 排序
     * @return array|\yii\db\ActiveRecord[] 查询结果
     */
    public static function quotaApplyList($offset, $limit, $realName, $mobile, $state, $type, $orderBy = ['quota_apply.updated_at' => SORT_DESC])
    {
        $model = QuotaApply::find()
            ->select('* , quota_apply.id as id , quota_apply.created_at as created_at, quota_apply.updated_at as updated_at , quota_apply.state as state')
            ->with('admin')
            ->joinWith('user')
            ->where(['quota_apply.state' => $state]);
        if ($realName != '') { // 真实姓名
            $model->andWhere(['user.real_name' => $realName]);
        }
        if ($mobile !='') {
            $model->andWhere(['user.mobile' => $mobile]);
        }
        if ($type !='') {
            $model->andWhere(['quota_apply.apply_type' => $type]);
        }
        return [
            'count' => $model->count(),
            'result' => $model->offset($offset)->limit($limit)->orderBy($orderBy)->all()
        ];

    }

    /**
     * 根据申请额度记录id获取正在审核的记录
     * @param integer $id 记录id
     * @return static 不存在返回空对象
     */
    public static function findQuotaApplyAuditById($id)
    {
        return QuotaApply::findOne(['id' => $id, 'state' => self::STATE_AUDITING]);
    }

    // 获取额度待审的记录数
    public static function getQuotaApplyStateAuditingCount()
    {
        return QuotaApply::find()->where(['state' => self::STATE_AUDITING])->count();
    }
}