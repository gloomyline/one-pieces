<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "shop_settled".
 *
 * @property integer $id
 * @property string $shop_name
 * @property string $contacts_name
 * @property string $contacts_mobile
 * @property string $contacts_addr
 * @property string $created_at
 * @property string $updated_at
 */
class ShopSettled extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_settled';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'safe'],
            [['shop_name'], 'string', 'max' => 30],
            [['contacts_name'], 'string', 'max' => 10],
            [['contacts_mobile'], 'string', 'max' => 11],
            [['contacts_addr'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'shop_name' => 'Shop Name',
            'contacts_name' => 'Contacts Name',
            'contacts_mobile' => 'Contacts Mobile',
            'contacts_addr' => 'Contacts Addr',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
