<?php
namespace common\models;

use common\bases\CommonModel;
use Yii;

class ProvinceModel extends CommonModel
{

    /**
     * 获取所有的省份
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getProvinces()
    {
        return Province::find()->all();
    }
}