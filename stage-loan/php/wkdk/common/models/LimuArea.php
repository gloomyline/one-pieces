<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "limu_area".
 *
 * @property integer $id
 * @property string $area_code
 * @property string $area_name
 * @property integer $state
 * @property integer $is_social_security
 * @property string $sort_letter
 */
class LimuArea extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'limu_area';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['state', 'is_social_security'], 'integer'],
            [['area_code', 'area_name'], 'string', 'max' => 30],
            [['sort_letter'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'area_code' => 'Area Code',
            'area_name' => 'Area Name',
            'state' => 'State',
            'is_social_security' => 'Is Social Security',
            'sort_letter' => 'Sort Letter',
        ];
    }
}
