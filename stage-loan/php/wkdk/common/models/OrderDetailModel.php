<?php
namespace common\models;

use common\bases\CommonModel;
use Yii;

class OrderDetailModel extends CommonModel
{
    /**
     * 添加消费分期订单明细信息
     * @param integer $loanId 借款ID
     * @param double $total 合计 = 单价 * 数量
     * @param integer $shopProductId 商家产品ID
     * @param integer $specId 产品规格ID
     * @param string $title 商品名称
     * @param string $spec 规格
     * @param integer $quantity 数量
     * @param double $price 单价
     * @return boolean 是否添加成功 true-是 false-否
     */
    public static function addOrderDetail($loanId, $total, $shopProductId, $specId, $title, $spec, $quantity, $price)
    {
        $orderDetail = new OrderDetail();
        $orderDetail->loan_id = $loanId;
        $orderDetail->total = $total;
        $orderDetail->shop_product_id = $shopProductId;
        $orderDetail->spec_id = $specId;
        $orderDetail->title = $title;
        $orderDetail->spec = $spec;
        $orderDetail->quantity = $quantity;
        $orderDetail->price = $price;
        if ($orderDetail->save()) {
            return true;
        }
        return false;
    }
}