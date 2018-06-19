<?php
namespace common\models;

use Yii;

class XunSeearchShop extends \hightman\xunsearch\ActiveRecord
{
    public static function projectName()
    {
        return 'shop';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'city_id' => 'City Id',
            'category_id' => 'Category Id',
        ];
    }
}