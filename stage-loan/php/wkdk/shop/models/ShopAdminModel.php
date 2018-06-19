<?php
/**
 * Created by PhpStorm.
 * User: lzh
 * Date: 2017/12/29
 * Time: 14:53
 */

namespace shop\models;


use Yii;
use common\models\ShopAdmin;
use common\bases\CommonModel;

class ShopAdminModel extends CommonModel
{
    // 启用禁用
    const STATE_ACTIVATED = 1; // 激活
    const STATE_FORBIDDEN = 0; // 冻结

    // 更新用户的登陆信息
    public static function updateLoginMsg($shopAdminId)
    {
        $model = ShopAdmin::findOne(['id' => $shopAdminId]);
        $model->login_time = date('Y-m-d H:i:s');
        $model->login_ip = Yii::$app->request->getUserIP();
        $model->save();
    }

}