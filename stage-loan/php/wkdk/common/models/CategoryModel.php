<?php
/**
 * Created by PhpStorm.
 * User: lzh
 * Date: 2017/12/18
 * Time: 14:38
 */

namespace common\models;


use common\bases\CommonModel;

class CategoryModel extends CommonModel
{
    /**
     * 添加分类
     * @param array $data 分类的参数
     * @return bool|string 成功返回id失败返回false
     */
    public static function add($data)
    {
        $model = new Category();
        $model->setAttributes($data);
        $result = $model->save();
        if ($result) {
            return $model->id;
        }
        return false;
    }

    /**
     * 根据分类id修改分类
     * @param integer $categoryId 分类id
     * @param array $data 分类的参数
     * @return bool 更新成功返回true，失败返回false
     */
    public static function update($categoryId, $data)
    {
        $model = Category::findOne(['id' => $categoryId]);
        $model->setAttributes($data);
        $result = $model->save();
        if ($result) {
            return true;
        }
        return false;
    }

    /**
     * 根据分类的id删除分类
     * @param integer|array $categoryId 分类id
     * @return int 返回删除的条数
     */
    public static function delByCategoryId($categoryId)
    {
       return Category::deleteAll(['id' => $categoryId]);
    }

    /**
     * 根据parent_id 查找二级分类
     * @param integer $parentId 上级分类id，顶级分类的parent_id 为0
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function findChildrenByParentId($parentId)
    {
        return Category::find()->where(['parent_id' => $parentId])->all();
    }

    /**
     * 根据id获取分类记录
     * @param integer $categoryId 分类id
     * @return static 返回数据对象
     */
    public static function findOneByCategoryId($categoryId)
    {
        return Category::findOne(['id' => $categoryId]);
    }

    /**
     * 根据上级分类获取下级分类
     * @param integer $offset
     * @param integer $limit
     * @param integer $parentId 上级分类id
     * @return array 返回数组【记录条数，列表数据对象】
     */
    public static function getChildrenList($offset, $limit, $parentId)
    {
        $model = Category::find();
        if ($parentId !== '') {
            $model->andWhere(['parent_id' => $parentId]);
        } else {
            $model->andWhere(['>', 'parent_id', 0]);
        }

        return [
            'count' => $model->count(),
            'result' => $model->offset($offset)->limit($limit)->orderBy(['parent_id' => SORT_DESC])->all()
        ];
    }

    /**
     * 获取所有分类
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getCategoryList()
    {
        return Category::find()->where(['parent_id' => 0])->with('children')->all();
    }

    /**
     * 查找指定id的商户分类包括下级分类
     * @param array $ids 分类id数组
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getShopCategoryWithChildrenByIds($ids)
    {
        return Category::find()->where(['parent_id' => 0, 'id' => $ids])->with('children')->all();
    }

    /**
     * 查找指定的商户分类
     * @param integer|array $ids 分类ID
     * @return array|\yii\db\ActiveRecord[] 返回查询的结果
     */
    public static function getShopCategoryByIds($ids)
    {
        return Category::find()->where(['parent_id' => 0, 'id' => $ids])->all();
    }

    /**
     * 查找指定的二级分类
     * @param integer|array $ids 分类ID
     * @return array|\yii\db\ActiveRecord[] 返回查询的结果
     */
    public static function getChildrenCategoryByIds($ids)
    {
        $category = Category::find();
        $category->where(['<>', 'parent_id', 0]);
        if ($ids != 'all') {
            $category->andWhere(['id' =>$ids]);
        }
        return $category->all();
    }

    /**
     * 更具商品id查找商品分类信息
     * @param integer|array $ids
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getProductCategoryByIds($ids)
    {
        $model = Category::find()->where(['and', ['id' => $ids], ['>', 'parent_id', 0]])->all();
        return $model;
    }
}