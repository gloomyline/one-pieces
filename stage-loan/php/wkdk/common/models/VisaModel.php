<?php
namespace common\models;

use common\bases\CommonModel;
use Yii;

class VisaModel extends CommonModel
{
    /**
     * 添加亲签照信息
     * @param integer $userId 用户ID
     * @param integer $shopId 商家ID
     * @param string $signPic 亲签照地址
     * @return bool 保存的结果 true-添加成功 false-添加失败
     */
    public static function add($userId, $shopId, $signPic)
    {
        $visa = new Visa();
        $visa->user_id = $userId;
        $visa->shop_id = $shopId;
        $visa->sign_pic = $signPic;
        if ($visa->save()) {
            return true;
        }
        return false;
    }

    /**
     * 更新亲签照信息
     * @param integer $userId 用户ID
     * @param integer $shopId 商家ID
     * @param string $signPic 亲签照地址
     * @return bool 保存的结果 true-更新成功 false-更新失败
     */
    public static function update($userId, $shopId, $signPic) {
        $visa = Visa::findOne(['user_id' => $userId, 'shop_id' => $shopId]);
        $visa->sign_pic = $signPic;
        if ($visa->save()) {
            return true;
        }
        return false;
    }

    /**
     * 按照用户ID、商家ID 查找用户亲签照信息
     * @param integer $userId 用户ID
     * @param integer $shopId 商家ID
     * @return static
     */
    public static function findVisaByUserIdShopId($userId, $shopId)
    {
        return Visa::findOne(['user_id' => $userId, 'shop_id' => $shopId]);
    }
}