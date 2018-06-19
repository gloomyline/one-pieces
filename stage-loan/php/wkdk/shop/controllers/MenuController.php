<?php
namespace shop\controllers;

use Yii;
use yii\helpers\Json;
use shop\models\Menu;
use shop\models\MenuModel;
use shop\bases\ShopBackendController;

class MenuController extends ShopBackendController
{
    public function actionIndex()
    {
        $results = $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $isTop = $request->get('is_top', '');
        $title = $request->get('title','');
        $module = $request->get('module','');
        $customize = $request->get('customize', 0);
        $menuModel = Menu::find();

        if ($isTop == 1) {
            $menuModel->andWhere(['parent_id' => MenuModel::IS_PARENT]);
        } else if ($isTop == 2) {
            $menuModel->andWhere(['!=', 'parent_id' , MenuModel::IS_PARENT]);
        }
        if ($title) {
            $menuModel->andWhere(['title' => trim($title)]);
        }
        if ($module) {
            $menuModel->andWhere(['module' => trim($module)]);
        }

        $count = $menuModel->count();
        $result = $menuModel->offset($offset)->limit($limit)->all();

        foreach ($result as $row) {
            $item = [
                'id' => (string)$row->id,
                'title' => $row->title,
            ];
            if (!$customize) {
                $item['parent_id'] = $row->parent_id;
                $item['module'] = $row->module;
                $item['route'] = $row->route;
                $item['created_at'] = $row->created_at;
            }
            $data[] = $item;
        }

        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $count,
            'results' => $data
        ]);
    }

    public function actionDetail()
    {
        $request = Yii::$app->request;
        $menu = Menu::find()
                        ->select(['title', 'module', 'parent_id', 'route'])
                        ->andWhere(['id' => $request->get('id')])
                        ->asArray()
                        ->one();
        if (!$menu) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'error_message' => '',
            'results' => $menu
        ]);
    }

    public function actionAdd()
    {
        $request = Yii::$app->request;
        $title = trim($request->post('title', ''));
        $route = trim($request->post('route', ''));
        $module = trim($request->post('module', ''));
        $parentId = trim($request->post('parent_id', ''));
        if (empty($title)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请输入名称']);
        }
        if (empty($module)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请输入模块名称']);
        }
        if ($parentId == "") {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请选择父级菜单']);
        }

        $data = ['title' => $title, 'parent_id' => (int)$parentId, 'module' => $module, 'route' => $route, 'is_show' => 1];
        $menuModel = new MenuModel();
        if (!$menuModel->add($data)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '保存失败']);
        }
        
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    public function actionUpdate()
    {
        $request = Yii::$app->request;
        $menu = Menu::findOne(['id' => $request->get('id')]);
        if (!$menu) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }

        $title = trim($request->post('title', ''));
        $route = trim($request->post('route', ''));
        $module = trim($request->post('module', ''));
        $parentId = trim($request->post('parent_id', ''));
        if (empty($title)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请输入名称']);
        }
        if (empty($module)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请输入模块名称']);
        }
        if ($parentId == "") {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请选择模块']);
        }

        $data = ['title' => $title, 'parent_id' => (int)$parentId, 'module' => $module, 'route' => $route, 'is_show' => 1];
        $menu->title = $title;
        $menu->route = $route;
        $menu->module = $module;
        $menu->parent_id = $parentId;
        $menu->updated_at = date('Y-m-d H:i:s');
        if (!$menu->save()) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '保存失败']);
        }

        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    /**
     * 获取用户菜单列表
     */
    public function actionMine()
    {
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'error_message' => '',
            'results' => MenuModel::getMenus()
        ]);
    }
}