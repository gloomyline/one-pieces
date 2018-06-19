<?php
namespace common\models;
use common\bases\CommonModel;
use Yii;

class ShopModel extends CommonModel
{
    const IS_EQ = 1; // 法人与实际控制人一致
    const IS_NEQ = 0; // 法人与实际控制人不一致

    const STATE_AUDITING = 0; // 待审核
    const STATE_AUDIT_PASS = 1; // 审核通过
    const STATE_AUDIT_NOPASS = 2; // 审核不通过

    const SHOP_NO_BEGIN = 100000; // 商户号起始值


    /**
     * 获取所有商户列表
     * @param integer $offset 查询的基准数
     * @param integer $limit 查询的记录数
     * @return array 返回所有商户信息
     */
    public static function getAllShop($offset, $limit)
    {
        return Shop::find()->offset($offset)->limit($limit)->all();
    }

    /**
     * 添加商户
     * @param $data 商户添加的参数
     * @return bool|string 成功返回id失败返回false
     */
    public static function add($data)
    {
        $model = new Shop();
        $model->setAttributes($data);
        $result = $model->save();
        if ($result) {
            return $model->id;
        }
        return false;
    }

    /**
     * 修改商户
     * @param $shopId 商户id
     * @param $data 商户修改的参数
     * @return bool 更新成功返回true，失败返回false
     */
    public static function update($shopId, $data)
    {
        $model = Shop::findOne(['id' => $shopId]);
        $model->setAttributes($data);
        $result = $model->save();
        if ($result) {
            return $model;
        }
        return false;
    }

    /**
     * 获取商户列表
     * @param $offset
     * @param $limit
     * @param $shopName 商户名称
     * @param $shopNo 商户号
     * @param $state 审核状态
     * @return array 【商户记录数，商户列表数据对象】
     */
    public static function getShopList($offset, $limit, $shopName, $shopNo, $state)
    {
        $model = Shop::find()->with('salesman', 'auditor');
        if ($shopName) {
            $model->andWhere(['shop_name' => $shopName]);
        }

        if ($shopNo) {
            $model->andWhere(['shop_no' => $shopNo]);
        }

        if ($state != '') {
            $model->andWhere(['state' => $state]);
        }
        return [
            'count' => $model->count(),
            'result' => $model->offset($offset)->limit($limit)->orderBy(['id' => SORT_DESC])->all()
        ];
    }

    /**
     * 根据商户id获取商户详情
     * @param $shopId
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function findShopByShopId($shopId)
    {
        return Shop::findOne(['id' => $shopId]);
    }

    /**
     * 根据条件查询商户信息
     * @param array $condition 条件
     * @return array|\yii\db\ActiveRecord[] 返回查询的结果
     */
    public static function findShopByCondition($condition)
    {
        return Shop::find()->where($condition)->asArray()->all();
    }

    /**
     * 设置指定商户 每日可用额度 为 用户每日限额
     * @param integer $shopId 商户ID
     * @return bool 是否设置成功 true-成功 false-失败
     */
    public static function updateShopAvailableQuota($shopId)
    {
        $shop = Shop::findOne($shopId);
        $shop->daily_available_quota = $shop->daily_limit_quota;
        $shop->updated_at = date('Y-m-d H:i:s');
        if ($shop->save()) {
            return true;
        }
        return false;
    }

    /**
     * 通过商户号查询商户信息
     * @param string $shopNo 商户号
     * @return array|object|null 商户信息
     */
    public static function findShopByShopNo($shopNo)
    {
        return Shop::findOne(['shop_no' => $shopNo]);
    }

    /**
     * 冻结商家额度
     * @param integer $shopId 商家ID
     * @param double $quota 借款额度
     * @return bool 是否更高成功 true-是，false-否
     */
    public static function frozenShopQuota($shopId, $quota)
    {
        $shop = Shop::findOne(['id' => $shopId]);
        $shop->daily_available_quota -= $quota; // 当日可用额度
        $shop->available_quota -= $quota; // 商家可用总额度
        $shop->updated_at = date('Y-m-d H:i:s'); // 更新时间
        if ($shop->save()) {
            return true;
        }
        return false;
    }


    /*
     * 额度申请审核后变更商户的总额度，单日限额，单笔限额
     * @param $shopId 商户id
     * @param $allowTotal 审核通过申请总额度
     * @param $allowDailyLimit 审核通过的单日限额
     * @param $allowSingleLimit 审核通过的单笔限额
     * @return bool 更新成功返回true 失败返回false
     */
    public static function updateQuota($shopId, $allowTotal, $allowDailyLimit, $allowSingleLimit)
    {
        $model = Shop::findOne(['id' => $shopId]);
        $model->total_quota += $allowTotal; // 总额度
        $model->available_quota += $allowTotal; // 可用额度
        $model->daily_limit_quota += $allowDailyLimit; // 单日限额
        $model->single_limit_quota += $allowSingleLimit; // 单笔限额
        if (!$model->save()) {
            return false;
        }
        return true;
    }
    /**
     * 解冻商家额度
     * @param integer $shopId 商家ID
     * @param integer $quota 金额
     * @param boolean $isUpdateDailyQuota 是否同时更新商家当日可用额度 ，默认【false】不更新 tips:通常为审核失败或商户确认未通过场景下，需同时更新商家当日可用额度
     * @return boolean 是否更新成功 true-是 false-否
     */
    public static function thawShopQuota($shopId, $quota, $isUpdateDailyQuota = false)
    {
        $shop = Shop::findOne(['id' => $shopId]);
        $shop->available_quota += $quota; // 可用额度 = 原来可用额度 + 还款额度/申请额度
        $shop->updated_at = date('Y-m-d H:i:s'); // 更新时间
        if ($isUpdateDailyQuota) {
            $shop->daily_available_quota += $quota; // 当日可用额度 = 原来当日可用额度 + 申请额度
            if ($shop->daily_available_quota > $shop->daily_limit_quota) {
                $shop->daily_available_quota = $shop->daily_limit_quota; // 恢复当日可用额度 不得超过 商户单日限额
            }
        }
        if (!$shop->save()) {
            return false;
        }
        return true;
    }

    // 统计有效商户数
    public static function countShopStatePass()
    {
        return Shop::find()->where(['state' => self::STATE_AUDIT_PASS])->count();
    }
}