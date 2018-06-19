<?php
namespace common\services;

use common\models\XunSeearchShop;

class SearchService
{
    /**
     * 添加商户数据至索引库
     * @param integer $id 数据库ID字段
     * @param integer $title 数据库title字段
     * @param integer $cityId 商户所在城市id
     * @param string $categoryId 商户分类
     */
    public static function createShopDoc($id, $title, $cityId, $categoryId)
    {
        $model = new XunSeearchShop;
        $model->id = $id;
        $model->title = trim($title);
        $model->city_id = $cityId;
        $model->category_id = $categoryId;
        $model->save(); 
    }

    /**
     * 查询商户索引库
     * @param array $keyword 关键字
     * @param integer $size 分页大小
     * @param integer $pn 当前页数
     * @return array 命中索引ID数组
     */
    public static function searchShopDoc($keyword, $condition, $size = 10, $pn = 1)
    {
        $result = [];
        $offset = $pn == 1 ? 0 : (($pn - 1) * $size);

        $model = new XunSeearchShop;
        $model = $model->find()->where($keyword)->andWhere($condition)->limit($size)->offset($offset);
        $docs = $model->all();
        $result['count'] = XunSeearchShop::find()->where($keyword)->count();
        if ($docs ) {
            foreach ($docs  as $doc) {
                $result['ids'][] = $doc->id;
            }
        }

        return $result;
    }

    /**
     * 根据ID删除索引
     * @param $id ID
     */
    public static function delShopDoc($id)
    {
        $model = new XunSeearchShop;
        $model = $model->deleteAll(['id' => $id]);
    }

    /**
     * 更具ID修改索引数据
     * @param integer $id
     * @param string $title
     * @param integer $cityId
     * @param string $categoryId
     */
    public static function updateShopDocById($id, $title, $cityId, $categoryId)
    {
        // 如果有就修改，没有则添加
        $model = XunSeearchShop::findOne(['id' => $id]);
        if ($model) {
            $model->title = trim($title);
            $model->city_id = $cityId;
            $model->category_id = $categoryId;
            $model->save();
        } else {
            self::createShopDoc($id, $title, $cityId, $categoryId);
        }
    }
}