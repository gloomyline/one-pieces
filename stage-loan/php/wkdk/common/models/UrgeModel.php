<?php
namespace common\models;

use common\bases\CommonModel;
use Yii;

class UrgeModel extends  CommonModel
{
    const STATE_DEFAULT = 1; //未催收
    const STATE_REPAYING = 2; //催收未还
    const STATE_FINISHED = 3; //催收已还
    const STATE_BAD = 4; //坏账

    /**
     *
     * @param integer $budgetPlanId 分期计划id
     * @return static 查找一条催收记录
     */
    public static function getUrgeByBudgetPlanId($budgetPlanId)
    {
        return Urge::findOne(['budget_plan_id' => $budgetPlanId]);
    }

    /**
     * 根据催收记录id获取催收记录
     * @param integer $id  催收记录id
     * @return static 返回一条催收记录
     */
    public static function getUrgeById($id)
    {
        return Urge::findOne(['id' => $id]);
    }

    /**
     * 获取逾期催收记录
     * @param integer $offset 查询的基准数
     * @param integer $limit 查询记录数
     * @param string $realName 真实姓名
     * @param string $mobile 手机号
     * @param integer $adminId 催收员id
     * @param integer $state 催收记录状态
     * @param string $beginAt 预计还款开始时间
     * @param string $endAt 预计还款结束时间
     * @return array|\yii\db\ActiveRecord[] 返回逾期催收记录的数组
     */
    public static function getUrgeList($offset, $limit, $realName, $mobile, $adminId, $state, $beginAt, $endAt)
    {
        $urge = Urge::find()
            ->joinWith('budgetPlan.user')
            ->with('admin', 'loan.shop', 'loan.orderDetail');
        if ($realName != '') {
            $urge->andWhere(['user.real_name' => trim($realName)]); // 真实姓名
        }
        if ($mobile != '') {
            $urge->andWhere(['user.mobile' => trim($mobile)]); // 手机号
        }
        if ($adminId != '') {
            $urge->andWhere(['urge.admin_id' => trim($adminId)]); // 催收员
        }
        if ($state != '') {
            $urge->andWhere(['urge.state' => trim($state)]); // 催收结果状态
        }
        if ($beginAt != '') {
            $beginAt = date('Y-m-d H:i:s', (int)($beginAt)); // 时间戳转字符串
            $urge->andWhere(['>=', 'budget_plan.planned_repayment_at', $beginAt]); // 预计还款 起始时间
        }
        if ($endAt != '') {
            $endAt = date('Y-m-d H:i:s', (int)($endAt) + 3600 * 24 - 1); // 时间戳转字符串, 截止时间设置为当前日期的 23:59:59，如 2017-09-20 23:59:59
            $urge->andWhere(['<=', 'budget_plan.planned_repayment_at', $endAt]); // 预计还款 截止时间
        }
        return [
            'count' => $urge->count(),
            'result' =>  $urge->offset($offset)->limit($limit)->orderBy(['urge.loan_id' => SORT_DESC])->all()
        ];
    }

    /**
     * 催收记录状态更新以及催收次数更新
     * @param integer $id 逾期催收记录id
     * @param integer $result 催收结果
     * @return bool 更新操作是否成功
     */
    public static function updateUrge($id, $result)
    {
        $model = Urge::findOne(['id' => $id]);
        if ($model->urge_count === 0 && $result !== UrgeLogModel::RESULT_BAD) {
            $model->state = self::STATE_REPAYING; // 催收未还款
        }
        if ($model->urge_count > 0 && $result === UrgeLogModel::RESULT_BAD) {
            $model->state = self::STATE_BAD;  // 坏账
        }
        $model->urge_count += 1;
        if ($model->save()) {
            return true;
        }
        return false;
    }

    /**
     * 用户还款后，存在逾期催收记录的借款将催收记录状态改为催收已还款
     * @param integer $loanId 借款订单ID
     * @param integer $budgetPlanId 分期计划id
     * @return bool 操作成功返回true 失败返回false
     */
    public static function urgeFinished($loanId, $budgetPlanId)
    {
        $result = Urge::updateAll(['state' => self::STATE_FINISHED], ['loan_id' => $loanId, 'budget_plan_id' => $budgetPlanId]);
        if (!$result) {
            return false;
        }
        return true;
    }

    /**
     * 某日催收成功次数
     * @param string $date 日期
     * @return int|string 返回查询到的结果条数
     */
    public static function statUrgeSuccessCountByDate($date = '')
    {
        return Urge::find()
            ->where(['state' => self::STATE_FINISHED, "DATE_FORMAT(`updated_at` ,'%Y-%m-%d')" => $date])
            ->count();
    }

    /**
     * 根据催收员统计该催收员的所有催收记录
     * @param integer $id 催收员id
     * @param array $cond 条件
     * @return array|null|\yii\db\ActiveRecord 返回数组【催收数量， 催收总额】
     */
    public static function getUrgeStatisticsByCatcherId($id, $cond)
    {
        $model = Urge::find()
            ->select('* , count(urge.id) as urge_count, sum(loan.quota) as urge_amount')
            ->joinWith('loan')
            ->where(['urge.admin_id' => $id]);
        if ($cond != '') {
           $model->andWhere($cond);
        }
        return $model->asArray()->one();
    }

}