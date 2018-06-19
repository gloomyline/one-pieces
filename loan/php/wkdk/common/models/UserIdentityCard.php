<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_identity_card".
 *
 * @property integer $id ID
 * @property integer $user_id 用户ID
 * @property string $real_name 真实姓名
 * @property string $identity_no 身份证号
 * @property string $state 状态 pass：验证通过 nopass：验证不通过
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class UserIdentityCard extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_identity_card';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['state'], 'string', 'max' => 10],
            [['real_name'], 'string', 'max' => 10],
            [['identity_no'], 'string', 'max' => 30],
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
            'real_name' => 'Real Name',
            'identity_no' => 'Identity No',
            'state' => 'State',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

}
