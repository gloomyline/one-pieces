<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_bank".
 *
 * @property integer $id ID
 * @property integer $user_id 用户ID
 * @property string $bank_name 银行名称
 * @property string $bank_no 银行卡号
 * @property string $bank_user 银行卡姓名
 * @property string $bank_code 银行编码
 * @property string $card_type 银行卡类型 2-储蓄卡 3-信用卡
 * @property string $agreeno 连连支付签约协议号
 * @property integer $is_default 是否默认使用的卡 0:不是 1:是
 * @property string $state 状态 valid:有效 invalid:无效
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class UserBank extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_bank';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'is_default', 'card_type'], 'integer'],
            [['bank_name'], 'string', 'max' => 60],
            [['bank_no'], 'string', 'max' => 32],
            [['bank_code'], 'string', 'max' => 10],
            [['agreeno'], 'string', 'max' => 16],
            [['bank_user'], 'string', 'max' => 30],
            [['state'], 'string', 'max' => 10],
            [['created_at', 'updated_at'], 'string', 'max' => 19],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'bank_name' => 'Bank Name',
            'bank_no' => 'Bank No',
            'bank_user' => 'Bank User',
            'bank_code' => 'Bank Code',
            'card_type' => 'Card Type',
            'agreeno' => 'Agreeno',
            'is_default' => 'Is Default',
            'state' => 'State',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * 关联user表
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * 关联user_identity_auth 表
     * @return \yii\db\ActiveQuery
     */
    public function getUserIdentityCard()
    {
        return $this->hasOne(UserIdentityCard::className(), ['user_id' => 'user_id']);
    }
}
