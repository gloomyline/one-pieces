<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "city".
 *
 * @property integer $id
 * @property string $name
 * @property integer $province_id
 * @property integer $is_open
 * @property integer $app_city_sort
 * @property string $code
 * @property string $initial
 * @property integer $hot_sort
 */
class City extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'city';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['province_id', 'is_open', 'hot_sort'], 'integer'],
            [['name'], 'string', 'max' => 30],
            [['code'], 'string', 'max' => 32],
            [['initial'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'province_id' => 'Province ID',
            'is_open' => 'Is Open',
            'code' => 'Code',
            'initial' => 'Initial',
            'hot_sort' => 'Hot Sort',
        ];
    }

    public function getProvince()
    {
        return $this->hasOne(Province::className(), ['id' => 'province_id']);
    }
}
