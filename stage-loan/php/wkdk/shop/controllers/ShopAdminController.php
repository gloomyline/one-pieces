<?php
/**
 * Created by PhpStorm.
 * User: lzh
 * Date: 2017/12/29
 * Time: 16:32
 */

namespace shop\controllers;


use Yii;
use shop\bases\ShopBackendController;
use yii\helpers\Json;

class ShopAdminController extends ShopBackendController
{
    // 登入名以及商户名称
    public function actionBasic()
    {
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'error_message' => '',
            'results' => [['name' => Yii::$app->user->identity->username]]
        ]);
    }
}