<?php

namespace common\services;

use common\models\CashCouponModel;
use common\models\CouponUseLogModel;
use Yii;

class CouponService
{
    /**
     * 注册成功时调用
     * 用户注册成功发送代金券
     * @param $userId  用户id
     */
    public static function assignRegisterCashCoupon($userId)
    {
        $type = CashCouponModel::TYPE_REGISTER_SUCCESS; // 注册成功奖励
        $registerCoupons = CashCouponModel::getCurrentActiveCoupons($type); // 1.判断是否有类型为注册成功 && 状态为开启 && 截止日期大于当前日期获去coupon_id数组以及代金券的类型

        if (!empty($registerCoupons)) {
            foreach ($registerCoupons as $lt) {
                CouponUseLogModel::generateCouponUseLog($userId, $type, $lt['id']); // 2.创建生成代金券记录
            }
        }
    }
}