<?php
namespace backend\controllers;

use common\models\CashCouponModel;
use common\models\CouponUseLogModel;
use common\models\UserModel;
use yii\helpers\Json;
use backend\bases\BackendController;
use common\models\CashCoupon;
use Yii;

class CashCouponController extends BackendController
{
    /**
     * 代金券管理
     * @return string
     */
    public function actionCoupon()
    {
        $results = $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $name = $request->get('name', '');
        $type = $request->get('type', '');
        $state = $request->get('state', '');
        $results = CashCouponModel::getCashCouponList($offset, $limit, $name, $type, $state ,false);
        foreach ($results['result'] as $row) {
            $isExpired = 1;
            if (!empty($row->end_at) && intval(strtotime($row->end_at)) < time()) {
                $isExpired = 0; // 已过期
            }
            $data[] = [
                'id' => $row->id,
                'coupon_name' => $row->coupon_name,
                'coupon_type' => $row->coupon_type,
                'coupon_amount' => $row->coupon_amount,
                'state' => $row->state,
                'begin_at' => $row->begin_at,
                'end_at' => $row->end_at ?? '',
                'validity_period' => $row->validity_period,
                'min_repayment' => $row->min_repayment,
                'is_expired' => $isExpired
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $results['count'],
            'results' => $data
        ]);
    }

    /**
     * 添加代金券
     * @return string
     */
    public function actionAddCashCoupon()
    {
        $request = Yii::$app->request;
        $couponName = $request->post('coupon_name', '');
        if (empty($couponName)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请选择代金券的名称']);
        }
        $couponType = $request->post('coupon_type', '');
        if (empty($couponType)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请选择奖励类型']);
        }
        $couponAmount = intval($request->post( 'coupon_amount', ''));
        if (empty($couponAmount)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '金额仅能输入一个大于零的整数且不为空']);
        }
        $beginAt = $request->post('begin_at', '');
        if (empty($beginAt)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请选择代金券的开始时间']);
        }
        $endAt = $request->post('end_at', '');
        if (empty($endAt)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请选择代金券的截止时间']);
        }
        if (intval($couponName) == CashCouponModel::REPAYMENT_COUPON) { // 还款抵用券

            $validity_period = intval(($endAt-$beginAt) / (3600 * 24) + 1); // 计算有效期
            $minRepayment = $request->post('min_repayment', '');
            if (empty($minRepayment)) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '还款金额下限仅能输入一个大于零的整数且不为空']);
            }
        }
        $state = intval($request->post('state', ''));
        if (!isset($state)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请选择代金券的状态']);
        }
        $model = new CashCoupon();
        $model->coupon_name = $couponName;
        $model->coupon_type = $couponType;
        $model->coupon_amount = $couponAmount;
        $model->state = $state;
        $model->begin_at = date('Y-m-d H:i:s', $beginAt);
        $model->end_at = date('Y-m-d H:i:s', $endAt + (3600 * 24) - 1); // 截止时间为当天XXXX-XX-XX 23:59:59
        if (intval($couponName) == CashCouponModel::REPAYMENT_COUPON) { // 还款抵用券 存在还款金额下限，以及有效期
            $model->min_repayment = $minRepayment;
            $model->validity_period = $validity_period;
        }
        if (!$model->save()) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '保存失败']);
        }
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    /**
     * 编辑代金券---能编辑的字段为金额，还款下限以及代金券状态.注：当代金券有派发记录时不能进行金额修改
     * @return string
     */
    public function actionUpdateCoupon()
    {
        $request = Yii::$app->request;
        $id = intval($request->post('id', ''));
        if (empty($id)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        $model = CashCouponModel::getCouponById($id);  // 获取当前编辑的数据对象
        if (!$model) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        $couponAmount = intval($request->post( 'coupon_amount', ''));
        if (empty($couponAmount)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '金额仅能输入一个大于零的整数且不为空']);
        }
        if ($model->coupon_name == CashCouponModel::REPAYMENT_COUPON) {
            $minRepayment = $request->post('min_repayment', '');
            if (empty($minRepayment)) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '还款金额下限仅能输入一个大于零的整数且不为空']);
            }
        }
        $state = intval($request->post('state', ''));
        if (!isset($state)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请选择代金券的状态']);
        }
        if (!empty($couponAmount)) {
            $model->coupon_amount = $couponAmount;
        }
        if (isset($state)) {
            $model->state = $state;
        }
        if (!empty($minRepayment)) {
            $model->min_repayment = $minRepayment;
        }
        if (!$model->save()) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '保存失败']);
        }
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    /**
     * 更具id获取代金券数据
     * @return string
     */
    public function actionGetCoupon()
    {
        $result = $data = [];
        $request = Yii::$app->request;
        $id = $request->get('id', 0);
        $result = CashCouponModel::getCouponById($id);
        if (!$result) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数有误']);
        }
        $count = CouponUseLogModel::getCountByCouponId($result->id); // 获取该代金券派发的条数
        $data = [
            'id' => $result->id,
            'coupon_name' => $result->coupon_name ?? '',
            'coupon_type' => $result->coupon_type ?? '',
            'state' => $result->state ?? '',
            'coupon_amount' => $result->coupon_amount,
            'min_repayment' => $result->min_repayment,
            'begin_at' => date("Y-m-d",strtotime($result->begin_at)),
            'end_at' => empty($result->end_at) ? '' : date("Y-m-d",strtotime($result->end_at)),
            'count' => (int)$count,  // 是否有相关代金券使用记录条数
        ];
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'results' => $data
        ]);
    }

    /**
     * 删除代金券--  需判断当前代金券是否有派发，有派发不能删除
     * @return string
     */
    public function actionCouponDel()
    {
        $request = Yii::$app->request;
        $id = $request->post('coupon_id', '');
        $count = CouponUseLogModel::getCountByCouponId($id); // 回去代金券使用记录条数
        if ($count > 0) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '当前代金券已派发不能进行删除操作！']);
        }
        $result = CashCouponModel::delCouponById($id);
        if (!$result) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    /**
     * 代金券使用记录列表
     * @return string
     */
    public function actionCouponUseLog()
    {
        $results = $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $couponName = $request->get('coupon_name', ''); // 代金券名称
        $state = $request->get('state', ''); // 使用记录状态
        $orderBy = ['coupon_use_log.id' => SORT_DESC];
        $results = CouponUseLogModel::getCouponUseLogList($offset, $limit, $couponName, $state, $orderBy);
        foreach ($results['result'] as $row) { // 用户名，代金券名称，奖励类型，金额，代金券编号，代金券使用状态，赠送时间，截止时间，使用时间，有效期
            $isExpired = 1;
            if ($row->cashCoupon->coupon_name !== CashCouponModel::CASH_BONUS && !empty($row->cashCoupon->end_at) && intval(strtotime($row->cashCoupon->end_at)) < time()) {
                $isExpired = 0; // 已过期标志
            }
            $data[] = [
                'id' => $row->id,
                'mobile' => $row->user->mobile,
                'coupon_name' => $row->cashCoupon->coupon_name,
                'coupon_type' => $row->cashCoupon->coupon_type,
                'coupon_amount' => $row->cashCoupon->coupon_amount,
                'coupon_code' => $row->coupon_code,
                'state' => $row->state,
                'assign_at' => $row->created_at, // 赠送时间
                'used_at' => empty($row->used_at) ? '' : $row->used_at,
                'end_at' => $row->cashCoupon->end_at ?? '',
                'validity_period' => $row->cashCoupon->validity_period,
                'min_repayment' => $row->cashCoupon->min_repayment,
                'is_expired' => $isExpired // 是否过期
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $results['count'],
            'results' => $data
        ]);
    }


    /**
     * 派发代金券
     * @return string
     */
    public function actionManualAssign()
    {
        $request = Yii::$app->request;
        $mobile = trim($request->post('username', ''));
        if (empty($mobile)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '用户名不能为空']);
        }
        $couponId = intval($request->post('coupon_id', ''));
        if (empty($couponId)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请选择要派发的代金券']);
        }
        $user = UserModel::getUserByMobile($mobile); // 通过手机号码查找用户
        $coupon = CashCouponModel::getCouponById($couponId);
        if (!$user) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '用户不存在，请检查所填写的用户名是否正确']);
        }
        if (!$coupon) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        if (!CouponUseLogModel::generateCouponUseLog($user->id, $coupon->coupon_type, $coupon->id)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '发送失败']);
        }
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    /**
     * 获取可手动派发的代金券--仅限代金券状态为开启且名称为还款抵用券的代金券
     * @return string
     */
    public function actionGetRepaymentCoupon()
    {
        $results = $data = [];
        $name = CashCouponModel::REPAYMENT_COUPON; // 还款抵扣券
        $state = CashCouponModel::STATE_OPEN;
        $endAt = true;
        $results = CashCouponModel::getCashCouponList('', '', $name, '', $state, $endAt);
        $couponTypeArr = [
            1 => '注册成功',
            2 => '邀请好友注册成功',
            3 => '邀请好友借款成功'
        ];
        foreach ($results['result'] as $row) {
            $data[] = [
                'id' => $row->id,
                'name' => '还款抵扣券:'.$couponTypeArr[$row->coupon_type].' 金额:'.$row->coupon_amount. ' 还款金额下限:'.$row->min_repayment.' 开始日期:'.$row->begin_at.' 截止日期:'.$row->end_at,
                'coupon_type' => $row->coupon_type,
                'coupon_amount' => $row->coupon_amount,
                'begin_at' => $row->begin_at,
                'end_at' => empty($row->end_at) ? '' : $row->end_at,
                'min_repayment' => $row->min_repayment,
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $results['count'],
            'results' => $data
        ]);
    }
}