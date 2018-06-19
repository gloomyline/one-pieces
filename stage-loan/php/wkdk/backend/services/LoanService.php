<?php
namespace backend\services;

use common\bases\CommonService;
use common\extend\sms\AlidayuSms;
use common\extend\sms\MsgTemplate;
use common\models\LoanModel;
use common\models\MobileLogModel;
use common\models\ShopModel;
use common\models\ShopProductModel;
use common\models\ShopProSpecModel;
use common\models\UserModel;
use common\services\LoanService as CommonLoanService;
use Yii;

class LoanService
{
    /**
     * 修改复审成功后的关联信息
     * @param integer $loanId 借款ID
     */
    public static function updateCorrelationAfterReviewSuccess($loanId)
    {
        $loan = LoanModel::findLoanById($loanId);

        // 设置复审成功时，更新用户状态为正常状态
        UserModel::setUserState($loan->user_id, UserModel::STATE_NORMAL);
        // 添加借款日志
        $loanService = new CommonLoanService();
        $loanService->generateAuditSuccessLog($loanId); // 添加审核成功日志
        // 若为 消费分期 给商家发送提醒短信
        if ($loan->type == LoanModel::TYPE_CONSUMPTION) {
            // 变更状态为 商家确认中
            LoanModel::setLoanState($loanId, LoanModel::STATE_CONFIRMING);
            $loanService->generateShopConfirmingLog($loanId); // 添加商家审核日志
            $service = new CommonService();
            $type = MobileLogModel::TYPE_SHOP_CONFIRM; // 商家确认通知
            $params = [
                'name' => $loan->user->real_name ?? '', // 姓名
                'date' => date('Y-m-d', strtotime($loan->created_at)), // 申请日期
                'account' => $loan->quota // 申请额度
            ]; // ${name}于${date}申请的${account}元，已通过系统审核，请尽快确认订单
            $service->sendSmsCode(AlidayuSms::Sign, MsgTemplate::SHOP_ORDER_CONFIRM, $loan->user->mobile, $params, $type); // 复审成功 商家确认通知
        }
    }

    /**
     * 审核失败后的关联信息
     * @param integer $loanId 借款ID
     */
    public static function  updateCorrelationAfterAuditFailure($loanId)
    {
        $loan = LoanModel::findLoanById($loanId);
        // 添加借款日志
        $loanService = new CommonLoanService();
        $loanService->generateAuditFailureLog($loanId); // 添加审核失败日志
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
}