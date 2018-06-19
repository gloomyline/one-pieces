<?php
namespace common\models;

/**
 * This is the model class for table "coupon_use_log".
 *
 * @property integer $id 代金券使用日志ID
 * @property string $coupon_code 代金券编号
 * @property integer $user_id 用户ID
 * @property integer $coupon_id 代金券ID
 * @property integer$coupon_type 奖励类型
 * @property integer $state 0:未使用/未提现，1:已使用/已提现，2：已过期
 * @property string $used_at 使用时间
 * @property string $created_at 创建时间/赠送时间
 * @property string $updated_at 更新时间
 */
class CouponUseLog extends \yii\db\activeRecord
{
    public static function tableName()
    {
        return 'coupon_use_log';
    }

    public function rules()
    {
        return [
            [['user_id', 'coupon_id', 'coupon_type', 'state'], 'integer'],
            [['coupon_code'], 'string', 'max' => 20],
            [['used_at', 'created_at', 'updated_at'], 'string', 'max' => 19],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'coupon_code' => 'Coupon Code',
            'user_id' => 'User ID',
            'coupon_id' => 'Coupon ID',
            'coupon_type' => 'Coupon Type',
            'state' => 'State',
            'used_at' => 'Used At',
            'created_at' => 'Create At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * 建立与 cash_coupon 表的关联关系
     * @return \yii\db\ActiveQuery
     */
    public function getCashCoupon()
    {
        return $this->hasOne(CashCoupon::className(), ['id' => 'coupon_id']);
    }

    /**
     * 建立与 user 表的关联关系
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}