<?php
namespace common\models;

use Yii;

/**
 * This is the model class for table "pay_log".
 *
 * @property integer $id 支付记录ID
 * @property integer $user_id 用户ID
 * @property integer $loan_id 借款ID
 * @property integer $pay_type 支付类型：1-用户主动还款（认证支付）2-放款（实时支付）3-平台代扣（分期支付）
 * @property string $no_order 商户唯一订单号
 * @property string $oid_paybill 连连支付支付单号
 * @property string $state 交易状态：waiting:等待处理 success:交易成功 processing:处理中
 * @property float $money_order 交易金额
 * @property string $info_order 订单描述
 * @property string $settle_date 清算日期
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * @property string $confirm_code 验证码
 */
class PayLog extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'pay_log';
    }

    public function rules()
    {
        return [
            [['user_id', 'pay_type'], 'integer'],
            [['no_order', 'oid_paybill'],  'string', 'max' => '32'],
            [['state'],  'string', 'max' => '10'],
            [['confirm_code'],  'string', 'max' => '6'],
            [['info_order'],  'string', 'max' => '255'],
            [['settle_date', 'created_at', 'updated_at'],  'string', 'max' => '19'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'loan_id' => 'Loan ID',
            'pay_type' => 'Pay Type',
            'no_order' => 'No Order',
            'oid_paybill' => 'Oid Paybill',
            'state' => 'State',
            'money_order' => 'Money Order',
            'info_order' => 'Info Order',
            'settle_date' => 'Settle Date',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'confirm_code' => 'Confirm Code',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    public function getUserBank()
    {
        return $this->hasOne(UserBank::className(), ['user_id' => 'user_id'])->where(['is_default' => UserBankModel::DEFAULT_BANKCARD]);
    }
}