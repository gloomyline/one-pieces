<?php
/**
 * Created by PhpStorm.
 * User: lzh
 * Date: 2018/1/9
 * Time: 15:05
 */

namespace common\models;


use Yii;
use common\bases\CommonModel;

class ShopQuotaApplyModel extends CommonModel
{
    const STATE_AUDITING = 0; // 待审核
    const STATE_AUDITED = 1; // 已审核
    const STATE_TOTAL_AUDITING = 0; // 总额度待审核
    const STATE_TOTAL_AUDIT_SUCCESS = 1; // 总额度审核成功
    const STATE_TOTAL_AUDIT_FAILURE = 2; // 总额度审核失败

    const STATE_DAILY_LIMIT_AUDITING = 0; // 单日限额待审核
    const STATE_DAILY_LIMIT_AUDIT_SUCCESS = 1; // 单日限额审核成功
    const STATE_DAILY_LIMIT_AUDIT_FAILURE = 2; // 单日限额审核失败

    const STATE_SINGLE_LIMIT_AUDITING = 0; // 单笔限额待审核
    const STATE_SINGLE_LIMIT_AUDIT_SUCCESS = 1; // 单笔限额审核成功
    const STATE_SINGLE_LIMIT_AUDIT_FAILURE = 2; // 单笔限额审核失败

    const TYPE_BACKEND_ADD = 0; // 额度类型：0-后台添加

    /**
     * 添加商户额度申请记录
     * @param $data 商户额度创建记录的参数
     * @return bool|string 成功返回创建的记录id 失败返回false
     */
    public static function add($data)
    {
        $model = new ShopQuotaApply();
        $model->setAttributes($data);
        $result = $model->save();
        if ($result) {
            return $model->id;
        }
        return false;
    }

    /**
     * 根据额度申请记录id查找记录
     * @param $id
     * @return static
     */
    public static function findOneById($id)
    {
        return ShopQuotaApply::findOne(['id' => $id]);
    }

    /**
     * 查找商户额度申请为审核的记录
     * @param $shopId 商户id
     * @return static 返回一条记录数据对象
     */
    public static function findAuditingOneByShopId($shopId)
    {
        return ShopQuotaApply::findOne(['shop_id' => $shopId, 'state' => self::STATE_AUDITING]);
    }

    /**
     * 商户额度审核列表
     * @param $offset
     * @param $limit
     * @param $shopName
     * @param $shopNo
     * @param $state
     * @param array $orderBy
     * @return array
     */
    public static function shopQuotaApplyList($offset, $limit, $shopName, $shopNo, $state, $orderBy = ['shop_quota_apply.updated_at' => SORT_DESC])
    {
        $model = ShopQuotaApply::find()
            ->with('admin')
            ->joinWith('shop')
            ->where(['shop_quota_apply.state' => $state]);

        if ($shopName != '') {
            $model->andWhere(['shop.shop_name' => $shopName]);
        }
        if ($shopNo !='') {
            $model->andWhere(['shop.shop_no' => $shopNo]);
        }
        return [
            'count' => $model->count(),
            'result' => $model->offset($offset)->limit($limit)->orderBy($orderBy)->all()
        ];
    }

    /**
     * 更新商户额度信息
     * @param $id 商户额度申请记录id
     * @param $data 更新的参数
     * @return bool 成功返回true 失败返回false
     */
    public static function update($id, $data)
    {
        $model = ShopQuotaApply::findOne(['id' => $id]);
        $model->setAttributes($data);
        $result = $model->save();
        if ($result) {
            return true;
        }
        return false;
    }

    /**
     * 商户额度待审核统计数
     * @return int|string 商户额度申请状态为待审核的记录数
     */
    public static function getShopQuotaApplyStateAuditingCount()
    {
        return ShopQuotaApply::find()->where(['state' => self::STATE_AUDITING])->count();
    }
}