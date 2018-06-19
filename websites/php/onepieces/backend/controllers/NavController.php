<?php
/**
 * Created by PhpStorm.
 * User: lzh
 * Date: 2018/3/15
 * Time: 11:07
 */

namespace backend\controllers;


use backend\bases\BackendController;
use Yii;
use common\models\NavigationModel;
use yii\helpers\Json;

class NavController extends BackendController
{
    // 导航列表
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $pid = $request->get('pid', '');
        $result = $data = [];
        $result = NavigationModel::getAllNav($offset, $limit, $pid);
        $parent = NavigationModel::findOneById($pid);
        $data['pid'] = $parent->pid ?? 0;
        foreach ($result['result'] as $row) {
            $data['data'][] = [
                'id' => $row->id,
                'name' => $row->name,
                'sort' => $row->sort,
                'pid' => (int)$row->pid,
                'link' => $row->link,
                'type' => $row->type,
                'is_show' => $row->is_show,
                'is_open' => $row->is_open,
                'description' => $row->description, // 描述
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $result['count'],
            'results' => $data
        ]);
    }

    // 添加
    public function actionAdd()
    {
        $request = Yii::$app->request;
        $name = trim($request->post('name', ''));
        $description = trim($request->post('description', ''));
        $pid = intval($request->post('pid', '')); // 未检验
        $link = trim($request->post('link', ''));
        $type = intval($request->post('type'));
        $sort = intval($request->post('sort')); // 未检验
        $isShow = intval($request->post('is_show'));
        $isOpen = intval($request->post('is_open')); // 当跳转功能关闭时可不写链接

        // 表单检验
        if (empty($name)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '名称不能为空']);
        }
        if (empty($isOpen) || !in_array($isOpen, [NavigationModel::IS_OPEN_TRUE, NavigationModel::IS_OPEN_FALSE])) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        if ($isOpen == NavigationModel::IS_OPEN_TRUE && empty($link)) { // 打开新页面选项为是时URL为必填
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '打开新页面选项为是时,URL链接地址不能为空']);
        }
        if (empty($isShow) || !in_array($isShow, [NavigationModel::IS_SHOW, NavigationModel::IS_NOT_SHOW])) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        if (empty($type)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '导航类型不能为空']);
        }
        if (empty($type) || !in_array($type, [NavigationModel::TYPE_TEXT_LIST, NavigationModel::TYPE_IMAGE_LIST, NavigationModel::TYPE_PAGE, NavigationModel::TYPE_CONTENT])) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        $data = [
            'name' => $name,
            'description' => $description,
            'pid' => $pid,
            'link' => $link,
            'type' => $type,
            'sort' => $sort,
            'is_show' => $isShow,
            'is_open' => $isOpen,
            'admin_id' => Yii::$app->user->id,
        ];
        $result = NavigationModel::add($data);
        if (!$result) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '添加失败']);
        }
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    // 编辑导航
    public function actionUpdate()
    {
        $request = Yii::$app->request;
        $id = intval($request->post('id'));
        $name = trim($request->post('name', ''));
        $description = trim($request->post('description', ''));
        $pid = intval($request->post('pid', '')); // 未检验
        $link = trim($request->post('link', ''));
        $type = intval($request->post('type'));
        $sort = intval($request->post('sort')); // 未检验
        $isShow = intval($request->post('is_show'));
        $isOpen = intval($request->post('is_open')); // 当跳转功能关闭时可不写链接

        // 表单检验
        $nav = NavigationModel::findOneById($id);
        if (!$nav) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误,操作的记录不存在']);
        }
        if (empty($name)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '名称不能为空']);
        }
        if (empty($isOpen) || !in_array($isOpen, [NavigationModel::IS_OPEN_TRUE, NavigationModel::IS_OPEN_FALSE])) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        if ($isOpen == NavigationModel::IS_OPEN_TRUE && empty($link)) { // 打开新页面选项为是时URL为必填
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '打开新页面选项为是时,URL链接地址不能为空']);
        }
        if (empty($isShow) || !in_array($isShow, [NavigationModel::IS_SHOW, NavigationModel::IS_NOT_SHOW])) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        if (empty($type)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '导航类型不能为空']);
        }
        if (empty($type) || !in_array($type, [NavigationModel::TYPE_TEXT_LIST, NavigationModel::TYPE_IMAGE_LIST, NavigationModel::TYPE_PAGE, NavigationModel::TYPE_CONTENT])) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        $data = [
            'name' => $name,
            'description' => $description,
            'pid' => $pid,
            'link' => $link,
            'type' => $type,
            'sort' => $sort,
            'is_show' => $isShow,
            'is_open' => $isOpen,
            'admin_id' => Yii::$app->user->id,
        ];
        $result = NavigationModel::update($id, $data);
        if (!$result) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '保存成功']);
        }
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    // 删除
    public function actionDel()
    {
        $request = Yii::$app->request;
        $id = intval($request->post('nav_id', ''));

        if (empty($id)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '无效参数！请重试。']);
        }
        $result = NavigationModel::findChildrenByPid($id);
        if ($result) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '该导航存在下级分类！请先清空该导航的下级导航，再进行删除操作。']);
        }
        if (NavigationModel::delById($id)) {
            return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
        }
        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '删除失败']);

    }

    // 根据上级id获取导航
    public function actionGetNav()
    {
        $request = Yii::$app->request;
        $pid = intval($request->get('pid', 0));

        $result = $data = [];
        $result = NavigationModel::findChildrenByPid($pid);
        foreach ($result as $row) {
            $data[] = [
                'id' => (int)$row->id,
                'name' => $row->name
            ];
        }

        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'results' => $data
        ]);
    }


}