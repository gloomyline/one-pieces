<?php
namespace backend\controllers;

use Yii;
use yii\helpers\Json;
use backend\models\MenuModel;
use backend\bases\BackendController;

class MenuController extends BackendController
{

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