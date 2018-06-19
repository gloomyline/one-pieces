<?php
/**
 * Created by PhpStorm.
 * User: lzh
 * Date: 2018/1/3
 * Time: 11:31
 */

namespace common\models;


use Yii;
use shop\bases\ShopBackendController;

class ShopProSpecModel extends ShopBackendController
{

    /**
     * 批量生成规格记录
     * @param array $data 【规格，价格，库存】
     * @param $productId 所属产品id
     * @return bool 添加成功返回true,失败返回false
     */
    public static function batchInsert($data = [], $productId)
    {
        $model = new ShopProSpec();
        foreach($data as $attributes)
        {
            $model->isNewRecord = true;
            $model->setAttributes($attributes);
            $model->product_id = $productId;
            if ($model->save() && $model->id=0) {
                return false;
            }
        }
        return true;
    }

    /**
     * 添加一天规格记录
     * @param $data
     * @return bool|string 添加成功返回记录id,失败返回false
     */
    public static function add($data)
    {
        $model = new ShopProSpec();
        $model->setAttributes($data);
        $result = $model->save();
        if ($result) {
            return $model->id;
        }
        return false;
    }

    /**
     * 根据规格记录id修改
     * @param $proSpecId 规格记录id
     * @param $data 修改的参数
     * @return bool 成功返回true,失败返回false
     */
    public static function update($proSpecId, $data)
    {
        $model = ShopProSpec::findOne(['id' => $proSpecId]);
        $model->setAttributes($data);
        $result = $model->save();
        if ($result) {
            return true;
        }
        return false;
    }

    /**
     * 根据商品的id计算总库存
     * @param $productId 商品id
     * @return mixed
     */
    public static function calculateStockById($productId)
    {
        return ShopProSpec::find()->where(['product_id' => $productId])->sum('stock') ?? 0;
    }

    /**
     * 根据规格记录id删除记录
     * @param $id 规格记录id
     * @return int 返回删除条数
     */
    public static function delById($id)
    {
        return ShopProSpec::deleteAll(['id' => $id]);
    }

    /**
     * 统计当前商品关联的规格记录条数
     * @param $proId 商品id
     * @return int|string 返回统计结果的条数
     */
    public static function countProSpecByProId($proId) {
        return ShopProSpec::find()->where(['product_id' => $proId])->count();
    }

    /**
     * 超找一天规格记录
     * @param $proSpecId 规格记录id
     * @return static
     */
    public static function findShopProSpecById($proSpecId)
    {
        return ShopProSpec::findOne(['id' => $proSpecId]);
    }

    /**
     * 根据商品ID 查询该商品的最低价信息
     * @param integer $productId 商品ID
     * @return array|null|\yii\db\ActiveRecord 返回查询的结果
     */
    public static function findShopProSpecByMinPrice($productId)
    {
        return ShopProSpec::find()->where(['product_id' => $productId])->orderBy('price asc')->one();
    }

    /**
     *  扣除产品库存
     * @param integer $specId 产品规格ID
     * @param integer $quantity 数量
     * @return boolean true-保存成功 false-保存失败
     */
    public static function deductProStock($specId, $quantity)
    {
        $shopProSpec = ShopProSpec::findOne(['id' => $specId]);
        $shopProSpec->stock -= $quantity; // 扣除产品库存
        if ($shopProSpec->save()) {
            return true;
        }
        return false;
    }

    /**
     *  归还产品库存
     * @param integer $specId 产品规格ID
     * @param integer $quantity 数量
     * @return bool true-保存成功 false-保存失败
     */
    public static function returnProStock($specId, $quantity)
    {
        $shopProSpec = ShopProSpec::findOne(['id' => $specId]);
        $shopProSpec->stock += $quantity; // 归还产品库存
        if ($shopProSpec->save()) {
            return true;
        }
        return false;
    }
}