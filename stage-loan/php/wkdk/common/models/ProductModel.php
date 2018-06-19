<?php

namespace common\models;

use common\bases\CommonModel;
use yii\helpers\Json;

class ProductModel extends CommonModel
{
    const STATE_ACTIVE = 1; // 开启状态
    const STATE_INACTIVE = 0; // 关闭状态
    const TYPE_CASH = 1; // 现金分期
    const TYPE_CONSUMPTION = 2; // 消费分期
    const WAY_PRINCIPAL_INTEREST_DUE = 1; // 还款方式： 1-等本等息


    /**
     * 获取所有上线的产品配置
     *
     * @return boolean
     */
    public static function getAllActiveProduct()
    {
        $model = Product::find();
        $model->Where(['active' => self::STATE_ACTIVE]);
        return $model->all();
    }

    /**
     * 获取所有产品记录
     * @param integer $offset 查询的基准数
     * @param integer $limit  查询的记录数
     * @return object 返回查询的结果
     */
    public static function getAllProduct($offset, $limit)
    {
        return Product::find()->limit($limit)->offset($offset)->orderBy(['id' => SORT_DESC])->all();
    }

    /**
     * 保存产品信息（包括新增修改）
     * @param string $title 产品名称
     * @param integer $minQuota 借款最小额度
     * @param integer $maxQuota 借款最大额度
     * @param string $periods 借款期数
     * @param double $annualizedInterestRate 年化利率
     * @param integer $repaymentWay 还款方式 1:等本等息
     * @param double $trialRate 信审费率
     * @param double $serviceRate 服务费率
     * @param double $poundage 手续费率
     * @param double $prepayment_poundage 提前还款手续费
     * @param integer $prepayment_poundage_max 提前还款手续费上限
     * @param double $overdueRate 逾期费率
     * @param integer $limitOverdueDays 逾期最大天数限制
     * @param integer $adminId 管理员ID
     * @param null $productId 产品ID 默认值【null】
     * @param string $use 用途
     * @param integer $active 线上状态
     * @return string 正确或错误的提示信息
     */
    public static function saveProduct($title, $minQuota, $maxQuota, $periods, $annualizedInterestRate, $repaymentWay, $trialRate, $serviceRate, $poundage, $prepayment_poundage, $prepayment_poundage_max, $overdueRate, $limitOverdueDays, $adminId, $productId = null, $use, $active)
    {
        if ($productId) {
            $product = Product::findOne(['id' => $productId]); // 修改
        } else {
            $product = new Product();  // 添加
            $product->created_at = date('Y-m-d H:i:s'); // 创建时间
        }
        $product->title = $title; // 产品名称
        $product->min_quota = $minQuota; // 借款最小额度
        $product->max_quota = $maxQuota; // 借款最大额度
        $product->period = $periods; // 借款期数

        $product->annualized_interest_rate = $annualizedInterestRate; // 年化利率
        $product->repayment_way = $repaymentWay; // 还款方式 1:到期本息
        $product->trial_rate = $trialRate; // 信审费率
        $product->service_rate = $serviceRate; // 服务费率
        $product->poundage = $poundage; // 手续费率
        $product->prepayment_poundage = $prepayment_poundage; // 提前还款手续费率
        $product->prepayment_poundage_max = $prepayment_poundage_max; // 提前还款手续费上限

        $product->overdue_rate = $overdueRate; // 逾期费率
        $product->limit_overdue_days = $limitOverdueDays; // 逾期最大天数限制
        $product->admin_id = $adminId; // 管理员ID
        $product->updated_at = date('Y-m-d H:i:s'); // 更新时间

        $product->use = $use; // 用途
        $product->active = $active; // 线上状态

        if ($product->validate()) { // 验证rules
            if (!$product->save()) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '保存失败']);
            }
            return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '保存成功']);
        } else { // 验证失败
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => implode(',', $product->getErrors()['title'])]);
        }
    }

    /**
     * 查询产品配置明细
     * @param integer $productId 产品ID
     * @return array|null 返回查询的明细
     */
    public static function getProductDetail($productId)
    {
       return Product::find()
               ->where(['id' => $productId])
               ->asArray()
               ->one();
    }

    /**
     * 按条件查询产品信息
     * @param array $condition 条件 $key=>$value 注意：仅查询一条记录
     * @return array|null|\yii\db\ActiveRecord 返回查询的结果
     */
    public static function getProductByCondition($condition)
    {
        return Product::find()
            ->where($condition)
            ->one();
    }
}