<?php
/**
 * Created by PhpStorm.
 * User: lzh
 * Date: 2018/1/29
 * Time: 9:59
 */

namespace shop\services;


use common\models\LoanModel;
use common\models\ShopModel;
use common\models\ShopProductModel;
use common\models\ShopProSpecModel;
use common\models\UserModel;
use common\services\LoanService;
use shop\bases\ShopBackendService;

class OrderService extends ShopBackendService
{

    /**
     * 商家确认失败的关联信息
     * @param integer $loanId 借款ID
     */
    public static function  updateCorrelationAfterConfirmFailure($loanId)
    {
        $loan = LoanModel::findLoanById($loanId);
        // 添加借款日志
        LoanService::generateShopConfirmFailureLog($loanId); // 添加审核失败日志
        // 解冻用户额度
        UserModel::thawUserQuota($loan->user_id, $loan->quota);
        // 若为 消费分期 归还商品总库存、解冻商家额度
        if ($loan->type == LoanModel::TYPE_CONSUMPTION) {
            // 归还商品库存
            foreach ($loan->orderDetail as $v) {
                ShopProSpecModel::returnProStock($v['spec_id'], $v['quantity']); // 归还 商品库存
                ShopProductModel::returnProTotalStock($v['shop_product_id'], $v['quantity']);// 归还商品的总库存
            }
            // 解冻商家额度
            $isUpdateDailyQuota = false; // 表示是否更新商家当日可用额度，若借款为今日订单时更新 true-更新 false-不更新
            if (date('Y-m-d', strtotime($loan->created_at)) == date('Y-m-d')) {
                $isUpdateDailyQuota = true;
            }
            ShopModel::thawShopQuota($loan->shop_id, $loan->quota, $isUpdateDailyQuota); // 解冻商家额度，同时更新当日商家可用额度
        }
    }

    /**
     * 商家确认成功的关联信息
     * @param $loanId
     */
    public static function updateCorrelationAfterConfirmSuccess($loanId)
    {
        LoanService::generateShopConfirmSuccessLog($loanId); // 添加审核失败日志
    }

}