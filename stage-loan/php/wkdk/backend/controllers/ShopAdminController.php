<?php
/**
 * Created by PhpStorm.
 * User: lzh
 * Date: 2018/1/2
 * Time: 9:57
 */

namespace backend\controllers;


use common\models\ShopAdmin;
use common\models\ShopAdminModel;
use Yii;
use yii\helpers\Json;
use backend\bases\BackendController;

class ShopAdminController extends BackendController
{
    /**
     * 添加商户管理员
     * @return string
     */
    public function actionAdd()
    {
        $request = Yii::$app->request;

        $username = trim($request->post('username'));
        if (empty($username)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请输入用户名']);
        }
        $password = trim($request->post('password'));
        if (empty($password)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请输入密码']);
        }
        $shopId = intval($request->post('shop_id'));
        if (empty($password)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        //判断登录名是否已经存在
        $checkExist = ShopAdmin::find()->where(['username' => $username])->one();
        if ($checkExist) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '用户名已存在']);
        }
        // 判断该店铺是否已经存在管理员
        $checkShopAdminExist = ShopAdminModel::findShopAdminByShopId($shopId);
        if ($checkShopAdminExist) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '该商户已经存在管理员']);
        }

        if (!ShopAdminModel::add($username, $password, $shopId)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '保存失败']);
        }

        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    /**
     * 修改商户管理员密码
     * @return string
     */
    public function actionSetPassword()
    {
        $request = Yii::$app->request;
        $shopAdminId = intval($request->post('admin_id', '')); // 管理员id
        $password = trim($request->post('password', '')); // 新密码
        if (empty($shopAdminId)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        if (empty($password)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '新密码不能为空']);
        }
        if (!ShopAdminModel::update($shopAdminId, $password)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '设置失败']);
        }
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    /**
     * 商户管理员详情
     * @return string
     */
    public function  actionDetail()
    {
        $data = [];
        $request = Yii::$app->request;
        $shopId = intval($request->get('shop_id', '')); // 商户id
        if (empty($shopId)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        $model = ShopAdminModel::findShopAdminByShopId($shopId);
        if (!empty($model)) {
            $data = [
                'username' => $model->username,
                'id' => $model->id,
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'results' => $data ?? []
        ]);
    }
}