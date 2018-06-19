<?php
/**
 * Created by PhpStorm.
 * User: lzh
 * Date: 2018/1/3
 * Time: 10:49
 */


namespace common\models;


use Yii;
use common\bases\CommonModel;

class ShopProductModel extends CommonModel
{
    const IS_ON_SALE = 1; // 上架
    const IS_NOT_ON_SALE = 0; // 下架

    const STATE_AUDITING = 0; // 待审核
    const STATE_AUDIT_PASS = 1; // 审核通过
    const STATE_AUDIT_NOPASS = 2; // 审核不通过

    /**
     * 添加商品记录
     * @param array $data 商品记录参数
     * @return bool|string 成功返回id失败返回false
     */
    public static function add($data)
    {
        $model = new ShopProduct();
        $model->setAttributes($data);
        $result = $model->save();
        if ($result) {
            return $model->id;
        }
        return false;
    }

    /**
     * 修改商品记录
     * @param integer $proId 商品id
     * @param array $data 商品参数
     * @return bool 成功返回true失败返回false
     */
    public static function update($proId, $data)
    {
        $model = ShopProduct::findOne(['id' => $proId]);
        $model->setAttributes($data);
        $result = $model->save();
        if ($result) {
            return true;
        }
        return false;
    }

    /**
     * 根据商品id查找商品记录（关联商品的分类，以及商品的规格记录）
     * @param integer $id 商品id
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function findProductByIdWithSpecAndCategory($id)
    {
        return ShopProduct::find()->where(['id' => $id])->with('proSpec', 'category.parent')->one();
    }

    /**
     * 根据id查找商品
     * @param integer $id 商品id
     * @return static 返回商品数据对象
     */
    public static function findProductById($id)
    {
        return ShopProduct::find()->where(['id' => $id])->with('proSpec')->one();
    }

    /**
     * 根据商品的id更新相应的总库存
     * @param integer $productId 商品id
     * @param integer $totalStock 总库存
     * @return bool 成功返回true 失败返回false
     */
    public static function updateTotalStock($productId, $totalStock)
    {
        $model = ShopProduct::findOne(['id' => $productId]);
        if ($model) {
            $model->total_stock = $totalStock;
            $model->updated_at = date('Y-m-d H:i:s');
            if ($model->save()) {
                return true;
            }

        }
        return false;
    }

    /**
     * 商户商品列表
     * @param integer $offset
     * @param integer $limit
     * @param integer $shopId 商户id
     * @param string $title 商品名称
     * @param string $no 商品货号
     * @param integer $category 商品分类
     * @param string $beginAt 商品添加起始时间
     * @param string $endAt 结束时间
     * @param integer $state 商品审核状态
     * @param integer $onSale 商品上下架状态
     * @return array 返回满足条件的记录数据对象
     */
    public static function getShopProductListByShopId($offset, $limit, $shopId, $title, $no, $category, $beginAt, $endAt, $state, $onSale)
    {
        $result = $data = [];
        $model = ShopProduct::find()->where(['shop_id' => $shopId])->with('category');
        if ($title) {
            $model->andWhere(['title' => trim($title)]);
        }
        if ($no != '') {
            $model->andWhere(['no' => trim($no)]);
        }
        if ($state != '') {
            $model->andWhere(['state' => intval($state)]);
        }
        if ($category) {
            $model->andWhere(['category_id' => intval($category)]);
        }
        if ($onSale != '') {
            $model->andWhere(['on_Sale' => intval($onSale)]);
        }
        if ($beginAt != '') {
            $beginAt = date('Y-m-d H:i:s', (int)($beginAt)); // 时间戳转字符串
            $model->andWhere(['>=', 'created_at', $beginAt]); // 注册起始时间
        }
        if ($endAt != '') {
            $endAt = date('Y-m-d H:i:s', (int)($endAt) + 3600 * 24 - 1); // 时间戳转字符串, 截止时间设置为当前日期的 23:59:59，如 2017-09-20 23:59:59
            $model->andWhere(['<=', 'created_at', $endAt]); // 注册截止时间
        }
        return [
            'count' => $model->count(),
            'result' => $model->offset($offset)->limit($limit)->orderBy(['id' => SORT_DESC])->all()
        ];
    }

    /**
     * 根据传入的条件统计商品数量
     * @param array $cond 查询条件参数
     * @return int|string 返回查询的到记录数
     */
    public static function countByCondition($cond = [])
    {
        return ShopProduct::find()->where($cond)->count();
    }

    /**
     * 根据分类查询商家产品(审核通过、上架)
     * @param integer|string $categoryId 分类
     * @param integer $shopId 商家ID
     * @param integer $offset 查询基准数
     * @param integer $limit 查询记录数
     * @return array|null 返回查询的结果
     */
    public static function getShopProductByCategoryId($categoryId, $shopId, $offset, $limit)
    {
        $shopProduct = ShopProduct::find()->where(['shop_id' =>$shopId, 'state'=> ShopProductModel::STATE_AUDIT_PASS, 'on_sale' => ShopProductModel::IS_ON_SALE]);
        if ($categoryId != 0) {
            $shopProduct->andWhere(['category_id' => $categoryId]);
        }
        $result['detail'] = $shopProduct->offset($offset)->limit($limit)->all();
        $result['count'] = $shopProduct->count();
        return $result;
    }

    /**
     * 根据ID查询商品信息
     * @param integer|array $id 商品ID
     * @return static 返回查询的结果
     */
    public static function getShopProductById($id)
    {
        return ShopProduct::find()->where(['id' => $id])->one();
    }

    /**
     *  扣除产品库存
     * @param integer $productId 产品ID
     * @param integer $quantity 数量
     * @return boolean true-保存成功 false-保存失败
     */
    public static function deductProTotalStock($productId, $quantity)
    {
        $shopProduct = ShopProduct::findOne(['id' => $productId]);
        $shopProduct->total_stock -= $quantity; // 扣除产品库存
        if ($shopProduct->save()) {
            return true;
        }
        return false;
    }
    /**
     *  归还商品库存
     * @param integer $productId 产品ID
     * @param integer $quantity 数量
     * @return boolean true-保存成功 false-保存失败
     */
    public static function returnProTotalStock($productId, $quantity)
    {
        $shopProduct = ShopProduct::findOne(['id' => $productId]);
        $shopProduct->total_stock += $quantity; // 归还商品库存
        if ($shopProduct->save()) {
            return true;
        }
        return false;
    }

    /**
     * 更新商品的销量
     * @param integer $productId 商品id
     * @param integer $sale 新增的销量
     * @return bool 修改成功返回true 失败返回false
     */
    public static function updateProductSale($productId, $sale)
    {
        $model = ShopProduct::findOne(['id' => $productId]);
        $model->sale += $sale;
        if ($model->save()) {
            return true;
        }
        return false;
    }
    // 获取商户商品分类 $shopId
    public static function getShopProductCategoryByShopId($shopId)
    {
        $model = ShopProduct::find()->select('category_id')->where(['shop_id' => $shopId])->with('category')->distinct()->asArray()->all();
        return $model;
    }
}