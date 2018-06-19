<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/12
 * Time: 15:22
 */
namespace common\models;

/**
 * This is the model class for table "cash_coupon".
 *
 * @property integer $id 代金券ID
 * @property integer $coupon_name 代金券名称：1-还款抵扣券 2-现金奖励
 * @property integer $coupon_type 代金券类型：1-注册成功 2-邀请好友注册成功 3-邀请好友借款成功
 * @property integer $coupon_amount 代金券金额
 * @property integer $state 0:关闭, 1:开启
 * @property string $begin_at 开始时间
 * @property string $end_at 截止时间
 * @property integer $validity_period 有效期
 * @property integer $min_repayment 还款金额下限
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class CashCoupon extends \yii\db\activeRecord
{
    public static function tableName()
    {
        return 'cash_coupon';
    }

    public function rules()
    {
        return [
            [['coupon_name', 'coupon_type', 'coupon_amount', 'state', 'validity_period', 'min_repayment'], 'integer'],
            [['begin_at', 'end_at', 'created_at', 'updated_at'], 'string', 'max' => '19'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'coupon_name' => 'Coupon Name',
            'coupon_type' => 'Coupon Type',
            'coupon_amount' => 'Coupon Amount',
            'state' => 'State',
            'begin_at' => 'Begin At',
            'end_at' => 'End At',
            'validity_period' => 'Validity Period',
            'min_repayment' => 'Min Repayment',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

}