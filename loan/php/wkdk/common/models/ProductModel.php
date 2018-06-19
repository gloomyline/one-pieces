<?php

namespace common\models;

use common\bases\CommonModel;
use yii\helpers\Json;

class ProductModel extends CommonModel
{
    const STATE_ACTIVE = 1; // 上线状态

    const WAY_PRINCIPAL_INTEREST_DUE = 1; // 还款方式： 1-到期本息

    /**
     * 获取上线的产品配置
     *
     * @return boolean
     */
    public static function getActiveProduct()
    {
        $model = Product::find();
        $model->Where(['active' => self::STATE_ACTIVE]);
        return $model->one();
    }

    /**
     * 获取所有产品记录
     * @param integer $offset 查询的基准数
     * @param integer $limit  查询的记录数
     * @return object 返回查询的结果
     */
    public static function getAllProduct($offset, $limit)
    {
        return Product::find()->limit($limit)->offset($offset)->orderBy(['product.id' => SORT_DESC])->all();
    }

    /**
     * 保存产品信息（包括新增、修改）
     * @param string $title 产品名称
     * @param integer $minQuota 借款最小额度
     * @param integer $maxQuota 借款最大额度
     * @param integer $minPeriod 借款周期下限
     * @param integer $maxPeriod 借款周期上限
     * @param double $annualizedInterestRate 年化利率
     * @param integer $repaymentWay 还款方式 1:到期本息
     * @param double $trialRate 信审费率
     * @param double $serviceRate 服务费率
     * @param double $poundage 手续费率
     * @param double $overdueRate 逾期费率
     * @param integer $limitOverdueDays 逾期最大天数限制
     * @param integer $adminId 管理员ID
     * @param integer  $productId 产品ID 默认值【null】
     * @return string 正确或错误的提示信息
     */
    public static function saveProduct($title, $minQuota, $maxQuota, $minPeriod, $maxPeriod, $annualizedInterestRate, $repaymentWay, $trialRate, $serviceRate, $poundage, $overdueRate, $limitOverdueDays, $adminId, $productId = null)
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
        $product->min_period = $minPeriod; // 借款周期下限
        $product->max_period = $maxPeriod; // 借款周期上限

        $product->annualized_interest_rate = $annualizedInterestRate; // 年化利率
        $product->repayment_way = $repaymentWay; // 还款方式 1:到期本息
        $product->trial_rate = $trialRate; // 信审费率
        $product->service_rate = $serviceRate; // 服务费率
        $product->poundage = $poundage; // 手续费率

        $product->overdue_rate = $overdueRate; // 逾期费率
        $product->limit_overdue_days = $limitOverdueDays; // 逾期最大天数限制
        $product->admin_id = $adminId; // 管理员ID
        $product->updated_at = date('Y-m-d H:i:s'); // 更新时间

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
               ->select(['title', 'min_quota', 'max_quota', 'min_period', 'max_period', 'annualized_interest_rate', 'repayment_way', 'trial_rate', 'service_rate', 'poundage', 'overdue_rate', 'limit_overdue_days'])
               ->andWhere(['id' => $productId])
               ->asArray()
               ->one();
    }
}