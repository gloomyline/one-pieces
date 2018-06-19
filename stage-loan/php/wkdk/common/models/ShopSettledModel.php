<?php
namespace common\models;

use common\bases\CommonModel;
use Yii;

class ShopSettledModel extends CommonModel
{
    /**
     * 添加商家入驻信息
     * @param string $shopName 商户名称
     * @param string $contactsName 联系人姓名
     * @param string $contactsMobile 联系人电话
     * @param string $contactsAddr 联系地址
     * @return bool 是否添加成功 true-是 false-否
     */
    public static function addShopSettled($shopName, $contactsName, $contactsMobile, $contactsAddr)
    {
        $shopSettled = new ShopSettled();
        $shopSettled->shop_name = $shopName;
        $shopSettled->contacts_name = $contactsName;
        $shopSettled->contacts_mobile = $contactsMobile;
        $shopSettled->contacts_addr = $contactsAddr;
        if ($shopSettled->save()) {
            return true;
        }
        return false;
    }

    /**
     * 商户入驻列表
     * @param $offset
     * @param $limit
     * @param $shopName 商户名称
     * @return array 返回满足条件的所有商户入驻记录信息数据对象
     */
    public static function getShopSettledList($offset, $limit, $shopName)
    {
        $model = ShopSettled::find();
        if ($shopName != '') {
            $model->andWhere(['like', 'shop_name', $shopName]);
        }
        return  [
            'count' => $model->count(),
            'result' =>$model->orderBy(['id' => SORT_DESC])->offset($offset)->limit($limit)->all()
        ];
    }

    /**
     * 根据id删除商户入驻记录
     * @param $id 商户入驻记录id
     * @return int 返回成功删除的记录数
     */
    public static function delShopSettled($id)
    {
        return ShopSettled::deleteAll(['id' => $id]);
    }

    /**
     * 根据id查找商户入驻记录
     * @param $id 商户入驻记录id
     * @return static 返回成功商户入驻记录
     */
    public static function finShopSettledById($id)
    {
        return ShopSettled::findOne(['id' => $id]);
    }
}