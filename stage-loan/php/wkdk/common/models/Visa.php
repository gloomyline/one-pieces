<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "visa".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $shop_id
 * @property string $sign_pic
 * @property string $created_at
 * @property string $updated_at
 */
class Visa extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'visa';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'shop_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['sign_pic'], 'string', 'max' => 100],
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
            'shop_id' => 'Shop ID',
            'sign_pic' => 'Sign Pic',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
