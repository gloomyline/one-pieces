<?php
namespace common\models;

use common\bases\CommonModel;
use Yii;

class CityModel extends CommonModel
{
    const NOT_OPENED = 0; // 是否开放城市 0 未开放 1开放
    const IS_OPEN = 1; // 是否开放城市 0 未开放 1开放

    /**
     * 查询所有已开放城市列表
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getOpenCity()
    {
        return City::find()->where(['is_open' => self::IS_OPEN])->all();
    }

    /**
     * 获取某省所有的市
     * @param $provinceId 省id
     * @return static[]
     */
    public static function getCityByProvinceId($provinceId)
    {
        return City::findAll(['province_id' => $provinceId]);
    }

    // 根据id查找城市
    public static function findCityByCityId($cityId)
    {
        return City::find()->where(['id' => $cityId])->with('province')->one();
    }
    // 根据城市id设置为开放城市
    public static function setOpenCityByCityId($cityId)
    {
        $model = City::findOne(['id' => $cityId]);
        $model->is_open = self::IS_OPEN;
        if ($model->save()) {
            return true;
        }
        return false;

    }
}