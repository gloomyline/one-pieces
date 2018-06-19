<?php
namespace frontend\controllers;

use common\models\CashCouponModel;
use common\models\CouponUseLog;
use common\models\CouponUseLogModel;
use frontend\bases\FrontendController;
use Yii;
use yii\helpers\Json;

class CashCouponController extends FrontendController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        // 访问控制
        $behaviors['access']['rules'] = [
            // 允许访客用户访问的Action
            [
                'actions' => [],
                'allow' => true,
                'roles' => ['?'],
            ],
            // 其它的Action必须要授权用户才可访问
            [
                'allow' => true,
                'roles' => ['@'],
            ],
        ];
        return $behaviors;
    }

    /**
     * 获取优惠券信息
     * @return string
     */
    public function actionCashCouponGet()
    {
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0); // 偏移量
        $limit = $request->get('limit', 10); // 返回的记录数量

        $userId = Yii::$app->user->getId(); // 当前登录用户的Id
        $result = CouponUseLogModel::getUserCoupon($userId, $offset, $limit);

        $count = (integer)CouponUseLog::find()->where(['coupon_use_log.user_id' => $userId])->count();
        if (($offset + $limit) < $count) {
            $hasMore = true; // 标识是否存在更多数据，true 时标识存在， false 标识无更多数据
        } else {
            $hasMore = false; // 标识是否存在更多数据，true 时标识存在， false 标识无更多数据
        }

        $data = [];
        foreach ($result as $k=>$v) {
            $data[] = [
                'coupon_id' => $v->id ?? 0, // 代金券使用日志Id
                'coupon_name' => $v->cashCoupon->coupon_name, // 代金券名称, 1 还款抵扣券 2 现金券
                'coupon_amount' => $v->cashCoupon->coupon_amount ?? 0, // 代金券金额
                'min_repayment' => $v->cashCoupon->min_repayment ?? 0, // 还款金额下限
                'min_withdrawal' => $v->cashCoupon->min_withdrawal ?? 0, // 提现金额下限
                'end_at' => strtotime($v->cashCoupon->end_at ?? ''), // 截止日期
            ];
            // 验证用户代金券是否已过期，若过期state = 2，否则state按$v->state原样输出。注：现金券不过期
            // 已过期
            if ($v->cashCoupon->coupon_name != CashCouponModel::CASH_BONUS && !empty($v->cashCoupon->end_at) && intval(strtotime($v->cashCoupon->end_at)) < time()) {
                $data[$k]['state'] = CouponUseLogModel::STATE_EXPIRATION; // 代金券状态 0:未使用/未提现，1:已使用/已提现，2：已过期
            } else {
                $data[$k]['state'] = $v->state; // 代金券状态 0:未使用/未提现，1:已使用/已提现，2：已过期
            }
        }

        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            "error_message" => "",
            'results' => $data,
            'has_more' => $hasMore
        ]);
    }
}