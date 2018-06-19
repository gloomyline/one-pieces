<?php
/**
 * Created by PhpStorm.
 * User: lzh
 * Date: 2018/3/19
 * Time: 15:00
 */

namespace backend\controllers;


use Yii;
use yii\helpers\Json;
use common\models\ExampleModel;
use backend\bases\BackendController;

class ExampleController extends BackendController
{
    // 案例列表
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $result = $data = [];
        $result = ExampleModel::getAllExample($offset, $limit);
        foreach ($result['result'] as $row) {
            $data[] = [
                'id' => $row->id,
                'name' => $row->name,
                'nav_id' => $row->nav_id,
                'description' => $row->description,
                'sort' => $row->sort,
                'link' => $row->link,
                'image' => $row->image,
                'state' => $row->state,
                'created_at' => $row->created_at,
            ];
        }
        $exampleId = Yii::$app->params['example_id'] ?? '';
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $result['count'],
            'example_id' => (int)$exampleId,
            'results' => $data
        ]);
    }

    // 添加案例
    public function actionAdd()
    {
        $request = Yii::$app->request;
        $navId = intval($request->post('nav_id'));
        $name = trim($request->post('name', ''));
        $description = trim($request->post('description', ''));
        $link = trim($request->post('link', ''));
        $state = intval($request->post('state'));
        $sort = intval($request->post('sort')); //
        $image = trim($request->post('image')); // 合作伙伴图片

        // 表单检验
        if (empty($navId)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '案例分类不能为空']);
        }
        if (empty($name)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '名称不能为空']);
        }
        if (empty($state) || !in_array($state, [ExampleModel::STATE_SHOW, ExampleModel::STATE_HIDDEN])) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }

        $data = [
            'nav_id' => $navId,
            'name' => $name,
            'description' => $description,
            'link' => $link,
            'sort' => $sort,
            'state' => $state,
            'image' => $image,
        ];
        $result = ExampleModel::add($data);
        if (!$result) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '添加失败']);
        }
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    // 编辑案例
    public function actionUpdate()
    {
        $request = Yii::$app->request;
        $id = intval($request->post('id'));
        $navId = intval($request->post('nav_id'));
        $name = trim($request->post('name', ''));
        $description = trim($request->post('description', ''));
        $link = trim($request->post('link', ''));
        $state = intval($request->post('state'));
        $sort = intval($request->post('sort')); //
        $image = trim($request->post('image')); // 合作伙伴图片


        // 表单检验
        $example = ExampleModel::findOneById($id);
        if (!$example) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误,操作的记录不存在']);
        }
        if (empty($navId)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '案例分类不能为空']);
        }
        if (empty($name)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '名称不能为空']);
        }
        if (empty($state) || !in_array($state, [ExampleModel::STATE_SHOW, ExampleModel::STATE_HIDDEN])) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }

        $data = [
            'nav_id' => $navId,
            'name' => $name,
            'description' => $description,
            'link' => $link,
            'sort' => $sort,
            'state' => $state,
            'image' => $image,
        ];
        $result = ExampleModel::update($id, $data);
        if (!$result) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '保存成功']);
        }
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    // 删除案例
    public function actionDel()
    {
        $request = Yii::$app->request;
        $id = intval($request->post('example_id', ''));

        if (empty($id)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '无效参数！请重试。']);
        }

        if (ExampleModel::delById($id)) {
            return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
        }
        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '删除失败']);

    }
}