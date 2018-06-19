<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_token".
 *
 * @property integer $id ID
 * @property integer $userid 用户ID
 * @property string $access_token Token
 * @property integer $expiry_timestamp token过期时间
 * @property string $source 来源
 * @property integer $created_at 创建时间
 */
class UserToken extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_token';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid', 'expiry_timestamp', 'created_at'], 'required'],
            [['userid', 'expiry_timestamp', 'created_at'], 'integer'],
            [['access_token', 'source'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userid' => 'Userid',
            'access_token' => 'Access Token',
            'expiry_timestamp' => 'Expiry Timestamp',
            'source' => 'Source',
            'created_at' => 'Created At',
        ];
    }
}
