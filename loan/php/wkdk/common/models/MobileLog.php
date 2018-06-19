<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "mobile_log".
 *
 * @property integer $id ID
 * @property string $mobile 电话号码
 * @property string $created_at 创建时间
 * @property string $type 类型 auth_code:短信验证码 loan:放款通知 repayment:还款通知 overdue:逾期通知 repay_succ:还款成功 withdrawal:提现成功 overdue_mass:逾期群发短信
 * @property string $return_message 返回信息
 * @property string $code 验证码
 * @property string $send_message 发送内容
 * @property integer $loan_id 借款id
 */
class MobileLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mobile_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mobile'], 'string', 'max' => 11],
            [['created_at'], 'string'],
            [['type'], 'string', 'max' => 13],
            [['loan_id'], 'integer'],
            [['return_message', 'code', 'send_message'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mobile' => 'Mobile',
            'created_at' => 'Created At',
            'type' => 'Type',
            'return_message' => 'Return Message',
            'code' => 'Code',
            'send_message' => 'Send Message',
            'loan_id' => 'Loan Id',
        ];
    }
    public function getUser()
    {
        return $this->hasOne(User::className(), ['mobile' => 'mobile']);
    }
}
