<?php
namespace common\models;

use common\models\CouponUseLog;
use Yii;
use common\bases\CommonModel;

class CouponUseLogModel extends CommonModel
{
    const STATE_UNUSED = 0; // 未使用
    const STATE_USED = 1; // 已使用
    const STATE_EXPIRATION = 2; // 已过期

    const TYPE_SIGNUP_SUCCESS = 1; // 代金券类型：1-注册成功
    const TYPE_INVITE_FRIENDS_SIGNUP_SUCCESS = 2; // 代金券类型：2-邀请好友注册成功
    const TYPE_INVITE_FRIENDS_BORROWED_SUCCESS = 3; // 代金券类型：3-邀请好友借款成功

    /**
     * 获取用户的所有代金券信息
     * @param integer $userId 用户ID
     * @param integer $offset 查询的基准数
     * @param integer $limit 查询的记录数
     * @return object 返回用户的所有代金券信息
     */
    public static function getUserCoupon($userId, $offset = 0, $limit = 10)
    {
       return  CouponUseLog::find()
               ->joinWith('cashCoupon')
               ->where(['user_id' => $userId])
               ->andWhere(['cash_coupon.state' => CashCouponModel::STATE_OPEN]) // 状态开启的代金券
               ->offset($offset)
               ->limit($limit)
               ->all();
    }

    /**
     * 获取代金券使用的记录列表数据
     * @param integer $offset 查询的基准数
     * @param integer $limit 查询的记录数
     * @param integer $couponName 代金券名称
     * @param integer $state 使用记录状态
     * @param array $orderBy 排序
     * @return array|\yii\db\ActiveRecord[] 返回满足条件代金券使用记录列表数据对象
     */
    public static function getCouponUseLogList($offset, $limit, $couponName, $state, $orderBy = ['id' => SORT_DESC])
    {
        $model = CouponUseLog::find()
            ->select('* , coupon_use_log.id as id, coupon_use_log.state as state, coupon_use_log.created_at as created_at')
            ->joinwith('cashCoupon')
            ->with('user');
        if ($couponName != '') {
            $model->andWhere(['cash_coupon.coupon_name' => $couponName]);
        }
        if ($state != '') {
            $model->andWhere(['coupon_use_log.state' => $state]);
        }
        return [
            'count' => $model->count(),
            'result' => $model->orderBy($orderBy)->offset($offset)->limit($limit)->all()
        ];
    }

    /**
     * 创建代金券使用记录
     * @param integer $userId 用户id
     * @param integer $couponId 代金券id
     * @return bool 返回true--创建成功 false--创建失败
     */
    public static function generateCouponUseLog($userId, $type, $couponId)
    {
        $model = new CouponUseLog();
        $model->state = self::STATE_UNUSED;
        $model->user_id = $userId;
        $model->coupon_id = $couponId;
        $model->coupon_type = $type;
        $model->coupon_code = self::generateCouponCode();
        return $model->save();
    }

    /**
     * 生成代金券的编号
     * @return string 返回一个字符串
     */
    public static function generateCouponCode()
    {
        $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $length = strlen($str) - 1; // 获取字符串的长度
        $first = rand(0, $length); // 第一个随机
        $sec = rand(0, $length); // 第二个随机数
        $three = rand(0, $length); // 第三个随机数
        return 'DK'.time().$str[$first].$str[$sec].$str[$three] ?? '';
    }

    /**
     * 根据代金券的id获取相关的所使用记录条数
     * @param integer $couponId 代金券id
     * @return int|string 返回查询结果条数
     *     */
    public static function getCountByCouponId($couponId)
    {
        return CouponUseLog::find()->where(['coupon_id' => $couponId])->count();
    }
}