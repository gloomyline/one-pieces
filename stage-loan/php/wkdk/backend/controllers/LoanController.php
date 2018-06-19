<?php
/**
 * 借款管理控制器.
 * User: Administrator
 * Date: 2017/9/18
 * Time: 13:43
 */
namespace backend\controllers;

use backend\bases\BackendController;
use common\models\AntiFraudModel;
use common\models\LoanModel;
use common\models\OverdueLogModel;
use common\models\PayLogModel;
use common\models\VisaModel;
use common\services\LoanService;
use common\services\RuleService;
use common\services\SiteService;
use Yii;
use yii\helpers\Json;
use backend\services\LoanService as BackendLoanService;

class LoanController extends BackendController
{
    /**
     * 借款列表
     */
    public function actionIndex()
    {

        $request = Yii::$app->request;
        // 分页参数
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);

        // 查询条件
        $real_name = $request->get('real_name', ''); // 真实姓名
        $mobile = $request->get('mobile', ''); // 手机号
        $state = $request->get('state', ''); // 借款状态
        $type = $request->get('type', ''); // 消费类型
        $orderState = $request->get('order_state', ''); // 订单状态

        $loanModel = new LoanModel();
        $results = $loanModel -> getAllLoan($offset, $limit, $real_name, $mobile, $state, $type, $orderState); // 查询所有借款记录
        $data = []; //结果
        $shopProductIds = []; // 保存明细中的商品ID
        $shopProductNames = []; // 保存明细中的商品名称
        foreach ($results['result'] as $k => $row) {
            if ($row->orderDetail) {
                foreach ($row->orderDetail as $order) {
                    if (in_array($order->shop_product_id, $shopProductIds)) {
                        continue;
                    }
                    if (count($shopProductIds) <= 3) {
                        if (count($shopProductIds) == 2) {
                            $shopProductNames[] = '...';
                        } else {
                            array_push($shopProductIds, $order->shop_product_id); // 保存明细中的商品ID
                            array_push($shopProductNames, $order->title); // 保存明细中的商品名称
                        }
                    }
                }
            }
            $data[] = [
                'id' => $row->id,
                'encoding' => $row->encoding,
                'type' => $row->type,
                'shop_name' => $row->shop->shop_name ?? '-',
                'product_name' => implode(',', $shopProductNames) == '' ? '-' : implode(',', $shopProductNames),
                'user_id' => $row->user_id,
                'real_name' => $row->user->real_name,
                'mobile' => $row->user->mobile,
                'quota' => $row->quota,
                'period' => $row->period,
                'created_at' => $row->created_at,
                'check_at' => $row->check_at,
                'review_at' => $row->review_at,
                'confirmed_at' => $row->confirmed_at,
                'state' => $row->state,
                'order_state' => $row->shopOrderLog->state ?? '',
                'lending_disabled' => true //标识放款按钮是否可以被点击，true-按钮禁用  false-按钮启用
            ];

            if (($row->state == LoanModel::STATE_REVIEW_SUCCESS && $row->type == LoanModel::TYPE_CASH) || ($row->state == LoanModel::STATE_CONFIRM_SUCCESS && $row->type == LoanModel::TYPE_CONSUMPTION)) { // 状态为复审成功时，判断支付记录是否处于waiting状态
                $payLog = PayLogModel::findActivePayLog($row->user_id, $row->id, PayLogModel::TYPE_REALTIMEPAY);
                $stateArr = [PayLogModel::STATE_PENDING, PayLogModel::STATE_CLOSED, PayLogModel::STATE_FAILURE, PayLogModel::STATE_CANCEL];
                if (!$payLog || ($payLog && in_array($payLog->state, $stateArr))) { // 不存在支付记录
                    $data[$k]['lending_disabled'] = false; // 放款按钮启用
                }
            }
            $shopProductIds = []; // 重置明细中的商品ID
            $shopProductNames = []; // 重置明细中的商品名称
        }

        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $results['count'],
            'results' => $data
        ]);

    }

    /**
     * 初审管理 （state: 等待审核（审核中）、初审成功、初审失败）
     */
    public function actionPreliminary()
    {
        $request = Yii::$app->request;
        // 分页参数
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);

        // 查询条件
        $real_name = $request->get('real_name', ''); // 真实姓名
        $mobile = $request->get('mobile', ''); // 手机号
        $state = $request->get('state', ''); // 借款状态
        $quota = $request->get('quota', 0); // 申请金额
        $begin_at = $request->get('start_at', ''); // 申请起始时间
        $end_at = $request->get('end_at', ''); // 申请截止时间

        $loanModel = new LoanModel();
        $results = $loanModel -> getAuditLoan($offset, $limit, $real_name, $mobile, $state, $quota, $begin_at, $end_at); // 查询借款的初审记录
        $data =[];
        $shopProductIds = []; // 保存明细中的商品ID
        $shopProductNames = []; // 保存明细中的商品名称
        // 返回结果的处理
        foreach ($results['result'] as $k => $v) {
            if ($v->orderDetail) {
                foreach ($v->orderDetail as $order) {
                    if (in_array($order->shop_product_id, $shopProductIds)) {
                        continue;
                    }
                    if (count($shopProductIds) <= 3) {
                        if (count($shopProductIds) == 2) {
                            $shopProductNames[] = '...';
                        } else {
                            array_push($shopProductIds, $order->shop_product_id); // 保存明细中的商品ID
                            array_push($shopProductNames, $order->title); // 保存明细中的商品名称
                        }
                    }
                }
            }
            $data[] = [
                'id' => $v->id,
                'encoding' => $v->encoding,
                'type' => $v->type,
                'shop_name' => $v->shop->shop_name  ?? '-',
                'product_name' => implode(',', $shopProductNames) == '' ? '-' : implode(',', $shopProductNames),
                'user_id' => $v->user_id,
                'real_name' => $v->user->real_name,
                'mobile' => $v->user->mobile,
                'quota' => $v->quota,
                'period' => $v->period,
                'created_at' => $v->created_at,
                'state' => $v->state,
                'preliminary_officer' => $v->preliminaryOfficer->username ?? ''
            ];
            $shopProductIds = []; // 重置明细中的商品ID
            $shopProductNames = []; // 重置明细中的商品名称
        }

        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $results['count'],
            'results' => $data
        ]);
    }

    /**
     * 复审管理 （state: 初审成功/等待复审/复审中、复审成功、复审失败）
     */
    public function actionReviews()
    {
        $request = Yii::$app->request;
        // 分页参数
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);

        // 查询条件
        $real_name = $request->get('real_name', ''); // 真实姓名
        $mobile = $request->get('mobile', ''); // 手机号
        $state = $request->get('state', ''); // 借款状态
        $quota = $request->get('quota', 0); // 申请金额
        $begin_at = $request->get('start_at', ''); // 申请起始时间
        $end_at = $request->get('end_at', ''); // 申请截止时间
        $orderState = $request->get('order_state', ''); // 订单状态

        $loanModel = new LoanModel();
        $results = $loanModel -> getAuditLoan($offset, $limit, $real_name, $mobile, $state, $quota, $begin_at, $end_at, false, $orderState); // 获取借款复审记录

        $data =[];
        $shopProductIds = []; // 保存明细中的商品ID
        $shopProductNames = []; // 保存明细中的商品名称
        # 返回结果的处理
        foreach ($results['result'] as $k => $v) {
            if ($v->orderDetail) {
                foreach ($v->orderDetail as $order) {
                    if (in_array($order->shop_product_id, $shopProductIds)) {
                        continue;
                    }
                    if (count($shopProductIds) <= 3) {
                        if (count($shopProductIds) == 2) {
                            $shopProductNames[] = '...';
                        } else {
                            array_push($shopProductIds, $order->shop_product_id); // 保存明细中的商品ID
                            array_push($shopProductNames, $order->title); // 保存明细中的商品名称
                        }
                    }
                }
            }
            $data[] = [
                'id' => $v->id,
                'encoding' => $v->encoding,
                'user_id' => $v->user_id,
                'type' => $v->type,
                'shop_name' => $v->shop->shop_name ?? '-',
                'product_name' => implode(',', $shopProductNames) == '' ? '-' : implode(',', $shopProductNames),
                'real_name' => $v->user->real_name,
                'mobile' => $v->user->mobile,
                'quota' => $v->quota,
                'period' => $v->period,
                'created_at' => $v->created_at,
                'state' => $v->state,
                'review_officer' => $v->reviewOfficer->username ?? '',
                'order_state' => $v->shopOrderLog->state ?? '',
                'lending_disabled' => true //标识放款按钮是否可以被点击，true-按钮禁用  false-按钮启用
            ];
            if (($v->state == LoanModel::STATE_REVIEW_SUCCESS && $v->type == LoanModel::TYPE_CASH) || ($v->state == LoanModel::STATE_CONFIRM_SUCCESS && $v->type == LoanModel::TYPE_CONSUMPTION)) { // 状态为复审成功时，判断支付记录是否处于waiting状态
                $payLog = PayLogModel::findActivePayLog($v->user_id, $v->id, PayLogModel::TYPE_REALTIMEPAY);
                $stateArr = [PayLogModel::STATE_PENDING, PayLogModel::STATE_CLOSED, PayLogModel::STATE_FAILURE, PayLogModel::STATE_CANCEL];
               if (!$payLog || ($payLog && in_array($payLog->state, $stateArr))) { // 不存在支付记录
                   $data[$k]['lending_disabled'] = false; // 放款按钮启用
               }
            }
            $shopProductIds = []; // 重置明细中的商品ID
            $shopProductNames = []; // 重置明细中的商品名称
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $results['count'],
            'results' => $data
        ]);
    }
    /**
     * 放款管理 (现金分期复审成功，消费分期商家确认成，放款中)
     */
    public function actionGrant()
    {
        $request = Yii::$app->request;
        // 分页参数
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);

        // 查询条件
        $real_name = $request->get('real_name', ''); // 真实姓名
        $mobile = $request->get('mobile', ''); // 手机号
        $state= $request->get('state', ''); // 借款状态
        $type = $request->get('type', ''); // 借款类型
        $begin_at = $request->get('start_at', ''); // 申请起始时间
        $end_at = $request->get('end_at', ''); // 申请截止时间


        $results = LoanModel::getGrantingLoan($offset, $limit, $real_name, $mobile, $begin_at, $end_at, $type, $state);
        $data =[];
        $shopProductIds = []; // 保存明细中的商品ID
        $shopProductNames = []; // 保存明细中的商品名称
        # 返回结果的处理
        foreach ($results['result'] as $k => $v) {
            if ($v->orderDetail) {
                foreach ($v->orderDetail as $order) {
                    if (in_array($order->shop_product_id, $shopProductIds)) {
                        continue;
                    }
                    if (count($shopProductIds) <= 3) {
                        if (count($shopProductIds) == 2) {
                            $shopProductNames[] = '...';
                        } else {
                            array_push($shopProductIds, $order->shop_product_id); // 保存明细中的商品ID
                            array_push($shopProductNames, $order->title); // 保存明细中的商品名称
                        }
                    }
                }
            }
            $data[] = [
                'id' => $v->id,
                'encoding' => $v->encoding,
                'user_id' => $v->user_id,
                'type' => $v->type,
                'shop_name' => $v->shop->shop_name ?? '-',
                'product_name' => implode(',', $shopProductNames) == '' ? '-' : implode(',', $shopProductNames),
                'real_name' => $v->user->real_name,
                'mobile' => $v->user->mobile,
                'quota' => $v->quota,
                'period' => $v->period,
                'created_at' => $v->created_at,
                'check_at' => $v->check_at,
                'review_at' => $v->review_at,
                'confirmed_at' => $v->confirmed_at,
                'state' => $v->state,
                'order_state' => $v->shopOrderLog->state ?? '',
                'lending_disabled' => true //标识放款按钮是否可以被点击，true-按钮禁用  false-按钮启用
            ];
            if (($v->state == LoanModel::STATE_REVIEW_SUCCESS && $v->type == LoanModel::TYPE_CASH) || ($v->state == LoanModel::STATE_CONFIRM_SUCCESS && $v->type == LoanModel::TYPE_CONSUMPTION)) { // 状态为复审成功时，判断支付记录是否处于waiting状态
                $payLog = PayLogModel::findActivePayLog($v->user_id, $v->id, PayLogModel::TYPE_REALTIMEPAY);
                $stateArr = [PayLogModel::STATE_PENDING, PayLogModel::STATE_CLOSED, PayLogModel::STATE_FAILURE, PayLogModel::STATE_CANCEL];
                if (!$payLog || ($payLog && in_array($payLog->state, $stateArr))) { // 不存在支付记录
                    $data[$k]['lending_disabled'] = false; // 放款按钮启用
                }
            }
            $shopProductIds = []; // 重置明细中的商品ID
            $shopProductNames = []; // 重置明细中的商品名称
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $results['count'],
            'results' => $data
        ]);
    }

    /**
     * 还款管理 （state: 还款中、已还款）
     */
    public function actionRepayments()
    {
        $request = Yii::$app->request;
        // 分页参数
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);

        // 查询条件
        $real_name = $request->get('real_name', ''); // 真实姓名
        $mobile = $request->get('mobile', ''); // 手机号
        $state = $request->get('state', ''); // 借款状态
        $begin_at = $request->get('start_at', ''); // 实际还款 起始时间
        $end_at = $request->get('end_at', ''); // 实际还款 截止时间
        $type = $request->get('type', ''); // 消费类型

        $loanModel = new LoanModel();
        $results = $loanModel ->getRepaymentsLoan($offset, $limit, $real_name, $mobile, $state, $begin_at, $end_at, $type); // 查询借款还款记录

        $data = [];
        $shopProductIds = []; // 保存明细中的商品ID
        $shopProductNames = []; // 保存明细中的商品名称
        $recentDate = SiteService::getRecentRepayingDate(date('Y-m-d')); // 获取最近还款日
        // 返回结果的处理
        foreach ($results['result'] as $k => $v) {
            if ($v->orderDetail) {
                foreach ($v->orderDetail as $order) {
                    if (in_array($order->shop_product_id, $shopProductIds)) {
                        continue;
                    }
                    if (count($shopProductIds) <= 3) {
                        if (count($shopProductIds) == 2) {
                            $shopProductNames[] = '...';
                        } else {
                            array_push($shopProductIds, $order->shop_product_id); // 保存明细中的商品ID
                            array_push($shopProductNames, $order->title); // 保存明细中的商品名称
                        }
                    }
                }
            }
            $detail = LoanService::caculateRepaymentAmountDetail($v->id);
            $data[] = [
              'id' => $v->id,
              'encoding' => $v->encoding,
              'type' => $v->type,
              'shop_name' => $v->shop->shop_name ?? '-',
              'product_name' => implode(',', $shopProductNames) == '' ? '-' : implode(',', $shopProductNames),
              'user_id' => $v->user_id,
              'real_name' => $v->user->real_name,
              'mobile' => $v->user->mobile,
              'quota' => $v->quota,
              'period' => $v->period,
              'lending_at' => $v->lending_at,
              'planned_repayment_at' => $v->state == LoanModel::STATE_FINISHED ? '-' : $recentDate, // 计划还款时间
              'repayment_at' => $detail['last_repayment_at'], // 实际还款时间
              'need_amount' => $detail['surplus_amount'] + $detail['repayed_amount'], // 应还金额 = 剩余未还金额 + 已还金额
              'repayed_amount' => $v->repayed_amount, // 已还金额
              'state' => $v->state,
            ];
            $shopProductIds = []; // 重置明细中的商品ID
            $shopProductNames = []; // 重置明细中的商品名称
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $results['count'],
            'results' => $data
        ]);
    }

    /**
     * 设置状态
     */
    public function actionSetState()
    {
        $request = Yii::$app->request;
        $id = $request->get('id',0); // 借款记录ID
        $tel_result = $request->post('tel_result'); // 电审结果 0,不通过 1，通过
        $tel_opinion = trim($request->post('tel_opinion','')); // 电审意见
        $audit_result = $request->post('audit_result'); // 审核结果 0,不通过 1，通过
        $audit_opinion = trim($request->post('audit_opinion','')); // 审核意见
        $current_state =  trim($request->post('current_state','')); // 原状态
        $state = ''; // 变更状态

        if (!$id) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        if (!in_array($audit_result, [1, 2])) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '审核结果不正确']);
        }
        if ($audit_opinion == '') {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '审核意见不能为空']);
        }
        if ($current_state == '' || !in_array($current_state, ['checks', 'reviews'])) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '当前状态非法']);
        } else {
            if ($current_state == 'checks' && !in_array($tel_result, [1, 2])) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '电审结果不正确']);
            }
            if ($current_state == 'checks' && $tel_opinion == '') {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '电审意见不能为空']);
            }
        }

        try {
            if ($audit_result == 1) {
                // 审核成功
                if ($current_state == 'checks') {
                    if ($tel_result == 1) {
                        // 初审成功 && 电审成功 变更状态复审中
                        $state = LoanModel::STATE_REVIEWING; // // 初审通过、电审通过
                    } else {
                        $state = LoanModel::STATE_AUDIT_FAILURE; // 初审通过、电审未通过
                    }
                } else {
                    $state = LoanModel::STATE_REVIEW_SUCCESS; // 复审成功
                }
            } else {
                // 审核失败
                if ($current_state == 'checks') {
                    $state = LoanModel::STATE_AUDIT_FAILURE; // 初审失败
                } else {
                    $state = LoanModel::STATE_REVIEW_FAILURE; // 复审失败
                }
            }
            $msg = LoanModel::setLoanState($id, $state, $audit_opinion, $audit_result, $tel_opinion, $tel_result); // 设置借款状态
            if ($state == LoanModel::STATE_AUDIT_FAILURE || $state == LoanModel::STATE_REVIEW_FAILURE) {
                BackendLoanService::updateCorrelationAfterAuditFailure($id); // 审核失败后 相关信息变更
            }
            if ($state == LoanModel::STATE_REVIEW_SUCCESS) {
                BackendLoanService::updateCorrelationAfterReviewSuccess($id); // 复审成功后 相关信息变更
            }
            return $msg;
        } catch (yii\db\Exception $e) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => $e->getMessage()]);
        } catch (yii\base\Exception $e) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => $e->getMessage()]);
        }
    }

    /**
     * 借款详情
     * @param int id 借款ID
     * @return string
     */
    public function actionDetail()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        if (!$id) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数不能为空']);
        }
        $loanModel = new LoanModel();
        $result = $loanModel::findDetailById($id); // 根据ID查询借款明细
        if (!$result) { // 验证
           return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }

        $ruleService = new RuleService($result->user_id);
        $creditScore = $ruleService->getTotalScore(); // 信用分
        $creditLevel = ''; // 信用等级
        foreach (Yii::$app->params['grade'] as $k => $v) {
            if ($creditScore >= $v['start'] && $creditScore <= $v['end']) {
                $creditLevel = $v['name']; // 信用等级
                break;
            }
        }
        $shopProductIds = []; // 保存明细中的商品ID
        $shopProductNames = []; // 保存明细中的商品名称

        $data = []; // 返回的结果
        if ($result->shop->id ?? false) {
            $visa = VisaModel::findVisaByUserIdShopId($result->user_id, $result->shop->id);
        } else {
            $visa = '';
        }
        // 借款人信息
        $data['borrower'] = [
            'user_id' => $result->user_id, // 用户ID
            'real_name' => $result->user->real_name ?? '', // 姓名
            'identity_no' => $result->userIdentityCard->identity_no ?? '', // 身份证号
            'mobile' => $result->user->mobile ?? '', // 手机号
            'created_at' => $result->user->created_at ?? '', // 注册时间
            'live_area' => $result->userBasic->live_area ?? '', // 所属地区
            'live_addr' => $result->userBasic->live_addr ?? '', // 详细地址
            'live_time' => $result->userBasic->live_time ?? '', // 居住时长
            'sex' => $result->user->gender ?? '', // 性别
            'age' => $result->user->age ?? '', // 年龄
            'position' => $result->userAdditional->position ?? '', // 职业
            'state' => $result->user->state ?? '', // 用户状态

            'success_count' => $result->user->success_count ?? '', // 成功借款次数
            'success_amount' => $result->user->success_amount ?? '', // 成功借款金额
            'success_repay_count' => $result->user->success_repay_count ?? '', // 成功还款次数
            'success_repay_amount' => $result->user->success_repay_amount ?? '', // 成功还款金额

            'invite_num' => $result->user->invite_num ?? 0, // 邀请人数
            'brokerage_odd' => $result->user->brokerage_odd ?? 0.00, // 剩余佣金
            'credit_score' => $creditScore, // 利用后台设置规则计算的信用分
            'credit_level' => $creditLevel, // 信用等级
            'sign_pic' => $visa ? ($visa->sign_pic ?? '') : '', // 亲签照
            'total_quota' => $result->user->total_quota ?? '', // 用户总额度
            'grade' => Yii::$app->params['grade'], // 信用分等级对照信息
        ];
        // 认证信息
        $data['auth'] = [
            'education' => $result->user->education ?? '', // 学历
            'is_identity_auth' => $result->user->is_identity_auth, // 身份认证
            'is_phone_auth' => $result->userMobileReport->idcard_match ?? '', // 手机认证报告的结果需 空-未通过手机认证 3-未知 2-模糊匹配成功 1-匹配成功 0-匹配失败
            'reg_time' => $result->userMobileReport->reg_time ?? '', // 入网时间
            'is_jd_auth' => $result->user->is_jd_auth ?? '', // 京东认证
            'is_taobao_auth' => $result->user->is_taobao_auth ?? '', // 淘宝认证
            'is_edu_auth' => $result->user->is_edu_auth ?? '', // 学历认证

            'is_credit_auth' => $result->user->is_credit_auth ?? '',// 央行征信
            'is_housefund_auth' => $result->user->is_housefund_auth ?? '',// 公积金
            'is_socialsecurity_auth' => $result->user->is_socialsecurity_auth ?? '',// 社保
        ];
        // 工作信息
        $data['work'] = [
            'industry' => $result->userAdditional->industry ?? '', // 所属行业
            'position' => $result->userAdditional->position ?? '', // 工作岗位
            'company_name' => $result->userAdditional->company_name ?? '', // 单位名称
            'company_area' => $result->userAdditional->company_area ?? '', // 单位所在地
            'company_addr' => $result->userAdditional->company_addr ?? '', // 单位详细地址
            'company_tel' => $result->userAdditional->company_tel ?? '', // 单位电话
        ];
        // 人际关系
        $data['relation'] = [
            'linkman_relation_fir' => $result->userAdditional->linkman_relation_fir ?? '', // 1号联系人与本人关系
            'linkman_name_fir' => $result->userAdditional->linkman_name_fir ?? '', // 1号联系人名称
            'linkman_tel_fir' => $result->userAdditional->linkman_tel_fir ?? '', // 1号联系人手机号码
            'linkman_relation_sec' => $result->userAdditional->linkman_relation_sec ?? '', // 2号联系人与本人关系
            'linkman_name_sec' => $result->userAdditional->linkman_name_sec ?? '', // 2号联系人名称
            'linkman_tel_sec' => $result->userAdditional->linkman_tel_sec ?? '', // 2号联系人手机号码
        ];
        // 银行信息
        $data['bank'] = [
            'bank_name' => $result->userBank->bank_name ?? '', // 开户行
            'bank_no' => $result->userBank->bank_no ?? '', // 银行卡号
            'state' => $result->userBank->state ?? '',
        ];
        if ($result->orderDetail) {
            foreach ($result->orderDetail as $order) {
                if (in_array($order->shop_product_id, $shopProductIds)) {
                    continue;
                }
                if (count($shopProductIds) <= 3) {
                    if (count($shopProductIds) == 2) {
                        $shopProductNames[] = '...';
                    } else {
                        array_push($shopProductIds, $order->shop_product_id); // 保存明细中的商品ID
                        array_push($shopProductNames, $order->title); // 保存明细中的商品名称
                    }
                } else {
                    break;
                }
            }
        }
        $interest = $result->interest > 0 ? $result->interest : LoanService::caculateInstallmentFee(date('Y-m-d'), $result->quota, $result->period, $result->annualized_interest_rate, $result->trial_rate, $result->service_rate, $result->poundage);
        $trialFee = round(round($result->trial_rate * $result->quota / $result->period, 2) * $result->period, 2); // 信审费
        $serviceFee = round(round($result->service_rate * $result->quota / $result->period, 2) * $result->period, 2); // 服务费
        $poundageFee = round(round($result->poundage * $result->quota / $result->period, 2) * $result->period, 2); // 手续费
        $surplusAmount = LoanService::caculateRepaymentAmountDetail($result->id)['surplus_amount'];
        // 借款信息
        $data['loan'] = [
            'shop_name' => $result->shop->shop_name ?? '', // 商户名称
            'shop_product_name' => implode(',', $shopProductNames), // 商品名称
            'type' => $result->type, // 消费类型
            'use' => $result->use, // 消费用途
            'encoding' => $result->encoding, // 项目编号
            'quota' => $result->quota, // 借款金额
            'period' => $result->period, // 借款期限
            'repayment_way' => $result->repayment_way, // 还款方式
            'annualized_interest_rate' => $result->annualized_interest_rate, // 年化利率
            'interest' => round(($interest - $trialFee - $serviceFee - $poundageFee), 2), // 借款利息 = 总息费 - 信审费 - 服务费 - 手续费
            'trial_fee' => $trialFee, // 信审费
            'service_fee' => $serviceFee, // 服务费
            'poundage_fee' => $poundageFee, // 手续费率
            'lending_at' => $result->lending_at, // 放款时间
            'planned_repayment_at' => ($result->state == LoanModel::STATE_REPAYING || $result->state == LoanModel::STATE_OVERDUE) ? SiteService::getRecentRepayingDate(date('Y-m-d')) : '', // 计划还款时间
            'need_amount' => $result->state == LoanModel::STATE_FINISHED ? $result->repayed_amount : (($result->state == LoanModel::STATE_REPAYING || $result->state == LoanModel::STATE_OVERDUE) ? ($result->repayed_amount + $surplusAmount) : $result->quota + $interest), // 订单已完成，用户应还金额为用户已还金额；订单未完成，应还总金额 = 用户已还金额+剩余未还金额
            'state' => $result->state, // 借款状态
            'confirmed_at' => $result->confirmed_at, // 商家确认时间
        ];

        // 初审信息
        $data['checks'] = [
            'preliminary_officer' => $result->preliminaryOfficer->username ?? '', // 初审人员
            'preliminary_opinion' => $result->preliminary_opinion ?? '', // 初审意见
            'check_at' => $result->check_at, // 初审时间
            'check_result' => $result->preliminary_result, // 初审结果
            'tel_opinion' => $result->tel_opinion, // 电审意见
            'tel_result' => $result->tel_result, // 电审结果
        ];
        // 复审信息
        $data['reviews'] = [
            'review_officer' => $result->reviewOfficer->username ?? '', // 复审人员
            'review_opinion' => $result->review_opinion ?? '', // 复审意见
            'review_at' => $result->review_at, // 复审时间
            'review_result' => $result->state, // 复审结果
        ];
        // 还款计划
        if ($result->budgetPlan) {
            $budgetPlan = $result->budgetPlan;
        } else {
            $budgetPlan = LoanService::generateBudgetPlan($result->id, false);
        }
        foreach ($budgetPlan as $plan) {
            $data['plan'][] = [
                'term' => $plan['term'],
                'planned_repayment_at' => $plan['planned_repayment_at'],
                'monthly' => $plan['monthly'],
                'principal' => $plan['principal'],
                'interest_fee' => $plan['interest_fee'],
                'trial_fee' => $plan['trial_fee'],
                'service_fee' => $plan['service_fee'],
                'poundage_fee' => $plan['poundage_fee'],
                'repayed_type' => $plan['repayed_type'] ?? '',
                'repayed_amount' => $plan['repayed_amount'] ?? '',
                'repayment_at' => $plan['repayment_at'] ?? '',
            ];
        }

        $overdue = OverdueLogModel::findOverdueByUserId($result->user_id); // 逾期记录
        foreach ($overdue as $k => $v) {
            $data['overdue'][$k] = [
                'term' => $v->budgetPlan->term,
                'created_at' => date('Y-m-d', strtotime($v->created_at)),
                'principal' => $v->budgetPlan->principal,
                'need_amount' => $v->budgetPlan->monthly + $v->overdue_fees, // 月供 + 逾期罚金
                'overdue_fee' => $v->overdue_fees,
                'overdue_day' => $v->overdue_days,
                'begin_over_at' => date('Y-m-d', strtotime($v->budgetPlan->planned_repayment_at) + 3600 * 24), // 开始逾期日期（计划还款时间）
                'state' => $v->budgetPlan->state,
                'repayed_amount' => $v->budgetPlan->repayed_amount,
                'surplus_amount' => round(($v->budgetPlan->monthly + $v->overdue_fees - $v->budgetPlan->repayed_amount), 2),
                'repayment_at' => $v->budgetPlan->repayment_at,
            ];
        }

        // 反欺诈信息
        $data['fraud'] = [];
        $antiFraud = AntiFraudModel::getByUserId($result->user_id);
        if ($antiFraud && $antiFraud->content) {
            $fraudContent = json_decode($antiFraud->content);
            foreach ($fraudContent->riskInfo as $v) {
                $data['fraud'][] = [
                    'riskCode' => $v->riskCode ?? '', // 命中妈
                    'riskCodeValue' => $v->riskCodeValue ?? '', // 命中风险等级
                    'updated_at' => $antiFraud->updated_at ?? '', // 更新时间
                ];
            }
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'error_message' => '',
            'results' => $data
        ]);
    }
}