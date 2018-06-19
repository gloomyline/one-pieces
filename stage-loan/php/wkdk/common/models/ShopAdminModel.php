<?php
/**
 * Created by PhpStorm.
 * User: lzh
 * Date: 2018/1/2
 * Time: 9:44
 */

namespace common\models;


use Yii;
use common\bases\CommonModel;

class ShopAdminModel extends CommonModel
{
    /**
     * 添加商户管理员
     * @param $username 登陆名
     * @param $password 密码
     * @param $shopId 商户id
     * @return bool 添加成功返回true 失败返回false
     */
    public static function add($username, $password, $shopId)
    {
        $model = new ShopAdmin();
        $model->username = $username;
        $model->password = Yii::$app->getSecurity()->generatePasswordHash($password);
        $model->shop_id = $shopId;
        $model->created_at = date("Y-m-d H:i:s");
        $model->updated_at = date("Y-m-d H:i:s");
        if (!$model->save()) {
            return false;
        }
        return true;
    }

    /**
     * 修改商户管理员密码
     * @param $shopAdminId 商户管理员id
     * @param $newPassword 新密码
     * @return bool 修改成功返回true 失败返回false
     */
    public static function update($shopAdminId, $newPassword)
    {
        $model = ShopAdmin::findOne(['id' => $shopAdminId]);
        $model->password = Yii::$app->getSecurity()->generatePasswordHash($newPassword);
        $model->updated_at = date("Y-m-d H:i:s");
        if (!$model->save()) {
            return false;
        }
        return true;
    }

    /**
     * 根据id找商户管理员
     * @param $id
     * @return static
     */
    public static function findShopAdminById($id)
    {
        return ShopAdmin::findOne(['id' => $id]);
    }

    /**
     * 根据店铺id查找管理员
     * @param $shopId 商户id
     * @return static
     */
    public static function findShopAdminByShopId($shopId)
    {
        return ShopAdmin::findOne(['shop_id' => $shopId]);
    }

    /**
     * 修改密码时检验密码是否正确
     * @param $shopAdmin 商户管理员对象
     * @param $password 原始密码
     * @return bool 密码正确返回true 错误返回false
     */
    public static function validatePassword($shopAdmin, $password)
    {
        $res = $shopAdmin->validatePassword($password);

        if (!$res) {
            return false;
        }
        return true;
    }
}