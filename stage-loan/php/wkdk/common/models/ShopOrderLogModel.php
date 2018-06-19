<?php
/**
 * Created by PhpStorm.
 * User: lzh
 * Date: 2018/1/12
 * Time: 17:42
 */

namespace common\models;


use Yii;
use common\bases\CommonModel;

class ShopOrderLogModel extends CommonModel
{
    const STATE_CONFIRMING = 0 ; // 待确认
    const STATE_CONFIRM_SUCCESS = 1; // 确认通过
    const STATE_CONFIRM_FAILURE = 2; // 确认失败
    const STATE_RECEIVING_SUCCESS = 3; // 已收货
    const STATE_RECEIVING_FAILURE = 4; // 为收货

    /**
     * 添加商户订单操作记录
     * @param $loanId 借款id
     * @param $state 订单确认状态 1/2
     * @param $opinion 确认意见
     * @return bool 成功返回true失败返回false
     */
    public static function addConfirmOrder($loanId, $state, $opinion)
    {
        $model = new ShopOrderLog();
        $model->loan_id = $loanId;
        $model->state = $state;
        $model->confirm_opinion = $opinion;
        $model->created_at = date('Y-m-d H:i:s');
        $model->updated_at = date('Y-m-d H:i:s');
        if ($model->save()) {
            return true;
        }
        return false;
    }

    /**
     * 收货确认
     * @param integer $loanId 借款id
     * @param integer $state 订单状态
     * @param string $opinion 借款意见
     * @return bool 成功返回true 失败返回false
     */
    public static function receivingConfirm($loanId ,$state, $opinion)
    {
        $model = ShopOrderLog::findOne(['loan_id' => $loanId]);
        $model->state = $state;
        $model->receiving_opinion = $opinion;
        $model->updated_at = date('Y-m-d H:i:s');
        if ($model->save()) {
            return true;
        }
        return false;
    }
}