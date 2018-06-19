<?php
namespace common\models;

use common\bases\CommonModel;

class CashCouponModel extends CommonModel
{
    const REPAYMENT_COUPON = 1; // 还款抵扣券
    const CASH_BONUS = 2; // 现金奖励
    const STATE_OPEN = 1;// 状态开启
    const STATE_DOWN = 0; // 状态关闭
    const TYPE_REGISTER_SUCCESS = 1; // 注册成功
    const TYPE_FRIEND_REGISTER_SUCCESS = 2; // 好友注册成功
    const TYPE_FRIEND_LOAN_SUCCESS = 3; // 好友借款成功

    /**
     * 获取代金券列表
     * @param integer $offset 查询的偏移量
     * @param integer $limit 查询的记录数
     * @param integer $name 代金券名称
     * @param integer $type 奖励类型
     * @param integer $state 代金券状态
     * @param array $orderBy 查询结果排序
     * @return array|\yii\db\ActiveRecord[] 若无查询结果返回空对象，有返回数据对象
     */
    public static function getCashCouponList($offset, $limit, $name, $type, $state, $endAt, $orderBy = ['id' => SORT_DESC])
    {
        $model = CashCoupon::find();
        if ($name != '') {
            $model->andWhere(['coupon_name' => $name]);
        }
        if ($type != '') {
            $model->andWhere(['coupon_type' => $type]);
        }
        if ($state != '') {
            $model->andWhere(['state' => $state]);
        }
        if ($endAt) {
            $model->andWhere(['>', 'end_at', date('Y-m-d H:i:s', time())]);
        }
        return [
            'count' => $model->count(),
            'result' => $model->orderBy($orderBy)->offset($offset)->limit($limit)->all()
        ];

    }

    /**
     * 根据代金券的id删除代金券
     * @param integer $id  代金券id
     * @return int 返回删除结果条数
     */
    public static function delCouponById($id)
    {
        return CashCoupon::deleteAll(['id' => $id]);
    }

    /**
     * 根据代金券id获取代金券信息
     * @param integer $id 代金券id
     * @return static 返回一条代金券数据对象
     */
    public static function getCouponById($id)
    {
        return CashCoupon::findOne(['id' => $id]);
    }

    /**
     * 获取当前有效的代金券
     * @param integer $type 代金券的类型
     * @return array|\yii\db\ActiveRecord[] 满足条件的代金券id
     */
    public static function getCurrentActiveCoupons($type)
    {
        return  CashCoupon::find()
            ->select('id')
            ->where(['and', ['=', 'coupon_type', $type], ['=', 'state', self::STATE_OPEN], ['>', 'end_at' , date('Y-m-d H:i:s', time())]])
            ->asArray()
            ->all();
    }

}