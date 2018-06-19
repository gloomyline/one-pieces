<?php
/**
 * Created by PhpStorm.
 * User:lzh
 * Date: 2017/12/18
 * Time: 15:10
 */

namespace backend\controllers;


use common\models\CategoryModel;
use Yii;
Use yii\helpers\Json;
use backend\bases\BackendController;

class CategoryController extends BackendController
{

    /**
     * 添加分类
     * @return string
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;

        $title = trim($request->post('title', ''));
        if (empty($title)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '分类名称不能为空']);
        }

        $parentId = intval($request->post('parent_id', '')); //上级分类id可为空

        $sort = intval($request->post('sort', 0)); // 排序

        $description = trim($request->post('description', '')); // 分类描述

        $isShow = intval($request->post('is_show', 0));

        $data = [
            'title' => $title,
            'parent_id' =>   $parentId ?? 0,
            'sort' => $sort,
            'description' => $description,
            'is_show' => $isShow,
            'admin_id' => Yii::$app->user->id,
            'created_at' => date('Y-m-d H:i:s'),
            'update_at' => date('Y-m-d H:i:s'),
        ];
        $result = CategoryModel::add($data);
        if (!$result) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '添加失败']);
        }
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    /**
     * 更新分类
     * @return string
     */
    public function actionUpdate()
    {
        $request = Yii::$app->request;
        $categoryId = intval($request->post('category_id', ''));
        if (empty($categoryId)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误！请重试']);
        }
        $title = trim($request->post('title', ''));
        if (empty($title)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '分类名称不能为空']);
        }
        $parentId = intval($request->post('parent_id', ''));
        $sort = intval($request->post('sort', 0));
        $description = trim($request->post('description', ''));
        $isShow = intval($request->post('is_show', 0));
        // 修改的数据
        $data = [
            'title' => $title,
            'parent_id' =>   $parentId ?? 0,
            'sort' => $sort,
            'description' => $description,
            'is_show' => $isShow,
            'admin_id' => Yii::$app->user->id,
            'update_at' => date('Y-m-d H:i:s'),
        ];

        $category = CategoryModel::findOneByCategoryId($categoryId); // 获取分类记录
        $children = CategoryModel::findChildrenByParentId($categoryId); // 获取下级
        // 若已有下级分类不能更改上级分类
        if ($children && $category->parent_id !== $parentId) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '该分类拥有多条下级分类记录，不能修改上级分类名称！']);
        }

        $result = CategoryModel::update($categoryId, $data);
        if (!$result) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '更新失败！']);
        }
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    /**
     * 商户分类列表页
     * @return string
     */
    public function actionShopCategory()
    {
        $result = $data = [];
        $result = CategoryModel::findChildrenByParentId(0);
        foreach ($result as $row) {
            $data [] = [
                'id' => (string)$row->id,
                'title' => $row->title,
                'sort' => $row->sort,
                'parent_id' => (int)$row->parent_id,
                'is_show' => $row->is_show,
                'description' => $row->description,
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'results' => $data
        ]);
    }

    /**
     * 商品分类列表页
     * @return string
     */
    public function actionProCategory()
    {
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $parentId = $request->get('parent_id', '');
        $results = $data = $list = [];
        $results = CategoryModel::getChildrenList($offset, $limit, $parentId);
        foreach ($results['result'] as $row) {
            $data[] = [
                'id' => (int)$row->id,
                'title' => $row->title,
                'sort' => $row->sort,
                'parent_id' => (int)$row->parent_id,
                'is_show' => $row->is_show,
                'description' => $row->description,
            ];
        }
        // 一级分类数据
        $parentList = CategoryModel::findChildrenByParentId(0);
        foreach ($parentList as $row) {
            $list[] = [
                'id' => (int)$row->id,
                'title' => $row->title,
                'parent_id' => (int)$row->parent_id,
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int)$results['count'],
            'results' => [
                'children' => $data ?? [],
                'parent' => $list ?? [],
            ]
        ]);
    }

    /**
     * 删除分类
     * @return string
     */
    public function actionDel()
    {
        $request = Yii::$app->request;
        $categoryId = intval($request->post('category_id', ''));
        if (empty($categoryId)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '无效参数！请重试。']);
        }
        $result = CategoryModel::findChildrenByParentId($categoryId);
        if ($result) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '该分类存在下级分类！请先清空该分类的下级分类，再进行删除操作。']);
        }
        if (CategoryModel::delByCategoryId($categoryId)) {
            return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
        }
        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '删除失败']);

    }
}