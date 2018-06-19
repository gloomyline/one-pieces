<?php
/**
 * 借款管理控制器.
 * User: Administrator
 * Date: 2017/9/18
 * Time: 13:43
 */
namespace backend\controllers;

use backend\bases\BackendController;
use common\models\Loan;
use common\models\LoanModel;
use common\models\OverdueLogModel;
use common\models\PayLogModel;
use common\services\LoanService;
use Yii;
use yii\helpers\Json;

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

        $loanModel = new LoanModel();
        $results = $loanModel -> getAllLoan($offset, $limit, $real_name, $mobile, $state); // 查询所有借款记录

        $data = []; //结果
        foreach ($results['result'] as $k => $row) {
          $data[] = [
              'id' => $row->id,
              'encoding' => $row->encoding,
              'user_id' => $row->user_id,
              'real_name' => $row->user->real_name,
              'mobile' => $row->user->mobile,
              'quota' => $row->quota,
              'period' => $row->period,
              'created_at' => $row->created_at,
              'check_at' => $row->check_at,
              'review_at' => $row->review_at,
              'lending_at' => $row->lending_at,
              'state' => $row->state,
              'preliminary_officer' => $row->preliminaryOfficer->username ?? '',
              'review_officer' => $row->reviewOfficer->username ?? '',
              'lending_disabled' => true //标识放款按钮是否可以被点击，true-按钮禁用  false-按钮启用
          ];
            if ($row->state == LoanModel::STATE_REVIEW_SUCCESS) { // 状态为复审成功时，判断支付记录是否处于waiting状态
                $payLog = PayLogModel::findActivePayLog($row->user_id, $row->id, PayLogModel::TYPE_REALTIMEPAY);
                $stateArr = [PayLogModel::STATE_PENDING, PayLogModel::STATE_CLOSED, PayLogModel::STATE_FAILURE, PayLogModel::STATE_CANCEL];
                if (!$payLog || ($payLog && in_array($payLog->state, $stateArr))) { // 不存在支付记录
                    $data[$k]['lending_disabled'] = false; // 放款按钮启用
                }
            }
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

        // 返回结果的处理
        foreach ($results['result'] as $k => $v) {
            $data[] = [
                'id' => $v->id,
                'encoding' => $v->encoding,
                'user_id' => $v->user_id,
                'real_name' => $v->user->real_name,
                'mobile' => $v->user->mobile,
                'quota' => $v->quota,
                'period' => $v->period,
                'created_at' => $v->created_at,
                'trial_rate' => $v->trial_rate,
                'service_rate' => $v->service_rate,
                'poundage' => $v->poundage,
                'state' => $v->state,
                'preliminary_officer' => $v->preliminaryOfficer->username ?? ''
            ];
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

        $loanModel = new LoanModel();
        $results = $loanModel -> getAuditLoan($offset, $limit, $real_name, $mobile, $state, $quota, $begin_at, $end_at, false); // 获取借款复审记录

        $data =[];
        # 返回结果的处理
        foreach ($results['result'] as $k => $v) {
            $data[] = [
                'id' => $v->id,
                'encoding' => $v->encoding,
                'user_id' => $v->user_id,
                'real_name' => $v->user->real_name,
                'mobile' => $v->user->mobile,
                'quota' => $v->quota,
                'period' => $v->period,
                'created_at' => $v->created_at,
                'trial_rate' => $v->trial_rate,
                'service_rate' => $v->service_rate,
                'poundage' => $v->poundage,
                'state' => $v->state,
                'review_officer' => $v->reviewOfficer->username ?? '',
                'lending_disabled' => true //标识放款按钮是否可以被点击，true-按钮禁用  false-按钮启用
            ];
            if ($v->state == LoanModel::STATE_REVIEW_SUCCESS) { // 状态为复审成功时，判断支付记录是否处于waiting状态
                $payLog = PayLogModel::findActivePayLog($v->user_id, $v->id, PayLogModel::TYPE_REALTIMEPAY);
                $stateArr = [PayLogModel::STATE_PENDING, PayLogModel::STATE_CLOSED, PayLogModel::STATE_FAILURE, PayLogModel::STATE_CANCEL];
               if (!$payLog || ($payLog && in_array($payLog->state, $stateArr))) { // 不存在支付记录
                   $data[$k]['lending_disabled'] = false; // 放款按钮启用
               }
            }
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

        $loanModel = new LoanModel();
        $results = $loanModel ->getRepaymentsLoan($offset, $limit, $real_name, $mobile, $state, $begin_at, $end_at); // 查询借款还款记录

        $data = [];
        // 返回结果的处理
        foreach ($results['result'] as $k => $v) {
            $data[] = [
              'id' => $v->id,
              'encoding' => $v->encoding,
              'user_id' => $v->user_id,
              'real_name' => $v->user->real_name,
              'mobile' => $v->user->mobile,
              'quota' => $v->quota,
              'period' => $v->period,
              'lending_at' => $v->lending_at,
              'planned_repayment_at' => $v->planned_repayment_at,
              'arrival_amount' => $v->arrival_amount,
              'repayment_at' => $v->repayment_at,
              'state' => $v->state,
            ];
            # 还款金额
            // 若借款订单状态为已还完，则无需计算逾期等个汇总费用，以用户已还款金额显示
            if ($v->state == LoanModel::STATE_FINISHED) {
                $data[$k]['need_amount'] = $v->repayed_amount; // 用户已还款金额
            } else {
                $needAmount = LoanService::caculateRepaymentAmountDetail($v['id'])['repaymentAmount'] ?? 0; // 还款金额
                $data[$k]['need_amount'] = $needAmount;
            }

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
        $audit_result = $request->post('audit_result'); // 审核结果 0,不通过 1，通过
        $audit_opinion = trim($request->post('audit_opinion','')); // 审核意见
        $current_state =  trim($request->post('current_state','')); // 原状态
        $state = ''; // 变更状态

        if (!$id) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        if (!in_array($audit_result, [1, 0])) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '审核结果不正确']);
        }
        if ($audit_opinion == '') {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '审核意见不能为空']);
        }
        if ($current_state == '' || !in_array($current_state, ['checks', 'reviews'])) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '当前状态非法']);
        }

        $loanModel= new LoanModel();
        if ($current_state == 'checks') { // 初审
            if ($audit_result == 1) {
                $state = $loanModel::STATE_REVIEWING;
            } else {
                $state = $loanModel::STATE_AUDIT_FAILURE;
            }
        }
        if ($current_state == 'reviews') { // 复审
            if ($audit_result == 1) {
                $state = $loanModel::STATE_REVIEW_SUCCESS;
            } else {
                $state = $loanModel::STATE_REVIEW_FAILURE;
            }
        }
        return $loanModel->setLoanState($id, $state, $audit_opinion);
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
        // print_r($result);exit();
        if (!$result) { // 验证
           return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }

        $data = []; // 返回的结果

        foreach ($result as $k => $v) {
            // 借款人信息
            $data['borrower'] = [
                'real_name' => $v->user->real_name ?? '', // 姓名
                'identity_no' => $v->userIdentityCard->identity_no ?? '', // 身份证号
                'mobile' => $v->user->mobile ?? '', // 手机号
                'created_at' => $v->user->created_at ?? '', // 注册时间
                'live_area' => $v->userBasic->live_area ?? '', // 所属地区
//
                'live_addr' => $v->userBasic->live_addr ?? '', // 详细地址
                'live_time' => $v->userBasic->live_time ?? '', // 居住时长
                'sex' => $v->user->gender ?? '', // 性别
                'age' => $v->user->age ?? '', // 年龄
//                '' => $v->userAdditional->create_at ?? '', // 通讯录上传时间

                'state' => $v->user->state ?? '', // 用户状态
                'success_count' => $v->user->success_count ?? '', // 成功借款次数
                'success_amount' => $v->user->success_amount ?? '', // 成功借款金额
                'success_repay_count' => $v->user->success_repay_count ?? '', // 成功还款次数
                'success_repay_amount' => $v->user->success_repay_amount ?? '', // 成功还款金额

//                'invite_num' => $v->user->invite_num ?? '', // 邀请人数
//                'brokerage_odd' => $v->user->brokerage_odd ?? '', // 剩余佣金
                'position' => $v->userAdditional->position ?? '', // 职业
//                'id_card_img' => $v->userBasic->id_card_img ?? '', // 身份证正面
//                'id_card_img_bg' => $v->userBasic->id_card_img_bg ?? '', // 身份证反面

            ];
            // 认证信息
            $data['auth'] = [
                'qq' => $v->userBasic->qq ?? '', // QQ
                'wechat' => $v->userBasic->wechat ?? '', // 微信
                'bankcard' => $v->userBasic->bankcard ?? '', // 常用信用卡
                'education' => $v->user->education ?? '', // 学历
                'zhima_credit' => $v->user->zm_open_id != '' ? '已认证' : '未认证', // 芝麻信用
                'is_identity_auth' => $v->user->is_identity_auth, // 身份认证
                'is_phone_auth' => $v->userMobileReport->idcard_match ?? '', // 手机认证报告的结果需 空-未通过手机认证 3-未知 2-模糊匹配成功 1-匹配成功 0-匹配失败
                'reg_time' => $v->userMobileReport->reg_time ?? '', // 入网时间
                'is_jd_auth' => $v->user->is_jd_auth ?? '', // 京东认证
                'is_taobao_auth' => $v->user->is_taobao_auth ?? '', // 淘宝认证
                'is_edu_auth' => $v->user->is_edu_auth ?? '', // 学历认证
                'is_bill_auth' => $v->user->is_bill_auth ?? '',// 信用卡账单
                'is_ebank_auth' => $v->user->is_ebank_auth ?? '',// 网银流水
            ];
            // 工作信息
            $data['work'] = [
                'industry' => $v->userAdditional->industry ?? '', // 所属行业
                'position' => $v->userAdditional->position ?? '', // 工作岗位
                'company_name' => $v->userAdditional->company_name ?? '', // 单位名称
                'company_area' => $v->userAdditional->company_area ?? '', // 单位所在地
                'company_addr' => $v->userAdditional->company_addr ?? '', // 单位详细地址
                'company_tel' => $v->userAdditional->company_tel ?? '', // 单位电话
            ];
            // 人际关系
            $data['relation'] = [
                'linkman_relation_fir' => $v->userAdditional->linkman_relation_fir ?? '', // 1号联系人与本人关系
                'linkman_name_fir' => $v->userAdditional->linkman_name_fir ?? '', // 1号联系人名称
                'linkman_tel_fir' => $v->userAdditional->linkman_tel_fir ?? '', // 1号联系人手机号码
                'linkman_relation_sec' => $v->userAdditional->linkman_relation_sec ?? '', // 2号联系人与本人关系
                'linkman_name_sec' => $v->userAdditional->linkman_name_sec ?? '', // 2号联系人名称
                'linkman_tel_sec' => $v->userAdditional->linkman_tel_sec ?? '', // 2号联系人手机号码
            ];
            // 银行信息
            $data['bank'] = [
                'bank_name' => $v->userBank->bank_name ?? '', // 开户行
//                'bank_addr' => $v->userBank->bank_addr ?? '', // 银行所在地
                'bank_no' => $v->userBank->bank_no ?? '', // 银行卡号
                'state' => $v->userBank->state ?? '',
//                'reserved_mobile' => $v->userBank->reserved_mobile ?? '', // 预留手机号
            ];
            // 借款信息
            $data['loan'] = [
                'encoding' => $v->encoding, // 项目编号
                'quota' => $v->quota, // 借款金额
                'period' => $v->period, // 借款期限
                'repayment_way' => $v->repayment_way, // 还款方式
                'annualized_interest_rate' => $v->annualized_interest_rate, // 年化利率
                'lending_at' => $v->lending_at, // 放款时间
                'trial_rate' => $v->trial_rate, // 信审费率
                'service_rate' => $v->service_rate, // 服务费率
                'state' => $v->state, // 借款状态
                'repayment_channel' => $v->state == LoanModel::STATE_FINISHED ? 'H5' : '', // 还款渠道
                'poundage' => $v->poundage, // 手续费率
                'interest' => number_format($v->quota * $v->period * ($v->annualized_interest_rate / 12 / 30), 2), // 借款利息
                'planned_repayment_at' => $v->planned_repayment_at, // 计划还款时间
                'repayment_at' => $v->repayment_at, // 实际还款时间
                'repayed_amount' => $v->repayed_amount, // 实际还款金额
            ];
            # 还款金额
            // 若借款订单状态为已还完，则无需计算逾期等个汇总费用，以用户已还款金额显示， 若订单预计还款日期为空则应还金额=本金加息费
            if ($v->state == LoanModel::STATE_FINISHED) {
                $data['loan']['need_amount'] = $v->repayed_amount; // 用户已还款金额
            } else if (!isset($v->planned_repayment_at)){
                $data['loan']['need_amount'] = $v->quota + $v->interest; // 用户已还款金额
            } else {
                $needAmount = LoanService::caculateRepaymentAmountDetail($v['id'])['repaymentAmount'] ?? 0; // 还款金额
                $data['loan']['need_amount'] = $needAmount; // 应还总额
            }

            // 初审信息
            $data['checks'] = [
                'preliminary_officer' => $v->preliminaryOfficer->username ?? '', // 初审人员
                'preliminary_opinion' => $v->preliminary_opinion ?? '', // 初审意见
                'check_at' => $v->check_at, // 初审时间
                'check_result' => $v->state, // 初审结果
            ];
            // 复审信息
            $data['reviews'] = [
                'review_officer' => $v->reviewOfficer->username ?? '', // 复审人员
                'review_opinion' => $v->review_opinion ?? '', // 复审意见
                'review_at' => $v->review_at, // 复审时间
                'review_result' => $v->state, // 复审结果
            ];
        }

        $overdue = OverdueLogModel::findOverdueByUserId($result[0]['user_id']); // 逾期记录
        foreach ($overdue as $k => $v) {
            $data['overdue'][$k] = [
                'id' => $v->loan_id,
                'encoding' => $v->loan->encoding,
                'quota' => $v->loan->quota,
                'repayment_at' => $v->loan->repayment_at ?? '',
                'repayed_amount' => $v->loan->repayed_amount ?? 0, // 用户已还款金额
                'begin_over_at' => date('Y-m-d', strtotime($v->loan->planned_repayment_at) + 3600 * 24), // 开始逾期日期（计划还款时间）
                'state' => $v->loan->repayed_amount > 0 ? '是' : '否', // 开始逾期日期（计划还款时间）
            ];

            # 还款金额
            // 若借款订单状态为已还完
            if ($v->loan->state == LoanModel::STATE_FINISHED) {
                $data['overdue'][$k]['need_amount'] = $v->loan->repayed_amount; // 用户已还款金额
                $data['overdue'][$k]['overdue'] = $v->overdue_fees; // 逾期费用
                $data['overdue'][$k]['over_day'] = $v->overdue_days; // 逾期天数

            } else {
                $repaymentAmountDetail = LoanService::caculateRepaymentAmountDetail($v->loan->id); //
                $data['overdue'][$k]['need_amount'] = $repaymentAmountDetail['repaymentAmount']; // 应还总额
                $data['overdue'][$k]['overdue'] = $repaymentAmountDetail['overdueAmount']; // 逾期费用
                $data['overdue'][$k]['over_day'] = $repaymentAmountDetail['overDay']; // 逾期天数
            }
        }

        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'error_message' => '',
            'results' => $data
        ]);
    }
}