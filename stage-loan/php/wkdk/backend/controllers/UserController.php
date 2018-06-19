<?php

namespace backend\controllers;

use common\models\AntiFraudModel;
use common\models\LoanModel;
use common\models\UserModel;
use common\services\SiteService;
use Yii;
use yii\helpers\Json;
use common\models\User;
use backend\bases\BackendController;

class UserController extends BackendController 
{
    /**
     * 用户列表数据显示
     * @return string
     */
    public function actionIndex()
    {
        $results = $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $state = $request->get('state','');
        $mobile = $request->get('mobile','');
        $realName = $request->get('real_name','');
        $beginAt = $request->get('start_at', ''); // 申请起始时间
        $endAt = $request->get('end_at', ''); // 申请截止时间
        $data = UserModel::getUserList($offset, $limit, $realName, $mobile, $state, $beginAt, $endAt); //获取用户信息列表集
        foreach ($data['result'] as $row) {
            $results[] = [
                'id' => $row->id,
                'mobile' => $row->mobile,
                'real_name' => $row->real_name,
                'success_count' => $row->success_count,
                'referrals' => "--",
                'state' => $row->state,
                'created_at' => $row->created_at,
                'is_forbidden' => $row->is_forbidden,
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $data['count'],
            'results' => $results
        ]);
    }

    /**
     * 查询用户详情
     * @return string
     */
    public function actionDetail()
    {
        $data = [];
        $request = Yii::$app->request;
        $id = $request->get('id', 0);
        $user = UserModel::getUserDetailByUserId($id);
        if (!$user) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        // 获得亲签照--最近一次消费分期借款的亲签照
        $loan = LoanModel::getLastConsumptionByUserId($id);
        if (isset($user->userBankList))
        $data = [
            // 会员注册信息
            'real_name' => $user->real_name ?? '', // 真实姓名
            'identity_no' => $user->userIdentityCard->identity_no ?? '', // 身份证号
            'mobile' => $user->mobile ?? '', // 手机号码
            'bank_no' => $user->userBank->bank_no ?? '', // 银行卡号
            'education' => $user->education ?? '', // 学历
            'position' => $user->userAdditional->position ?? '', //工作岗位
            'age' => $user->age ?? '', // 年龄
            'sex' => $user->gender ?? '', // 性别
            'live_addr' => $user->userBasic->live_addr ?? '', // 居住地址
            'created_at' => $user->created_at ?? '', // 注册时间
            'sign_pic' => $loan->visa->sign_pic ?? '', // 亲签照
            'available_quota' => $user->available_quota ?? 0,// 可用额度

           // 借款资质信息
            'is_profile_auth' => $user->is_profile_auth ?? 0, // 个人信息认证
            'is_bankcard_auth' => $user->is_bankcard_auth ?? 0, //银行卡认证
            // ---芝麻认证
            'is_phone_auth' => $user->is_phone_auth ?? 0, // 手机认证
            'is_identity_auth' => $user->is_identity_auth ?? 0, // 身份认证

            // 提升额度
            'is_taobao_auth' => $user->is_taobao_auth ?? 0, // 淘宝认证
            'is_jd_auth' => $user->is_jd_auth ?? 0, // 京东认证
            'is_credit_auth' => $user->is_credit_auth ?? 0, // 央行认证
            'is_housefund_auth' => $user->is_housefund_auth ?? 0, // 公积金认证
            'is_socialsecurity_auth' => $user->is_socialsecurity_auth ?? 0, // 社保认证
            'is_edu_auth' => $user->is_edu_auth ?? 0, // 学历认证
            'is_bill_auth' => $user->is_bill_auth ?? 0, // 信用卡账单认证
            'is_ebank_auth' => $user->is_ebank_auth ?? 0, // 网银流水
        ];
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'error_message' => '',
            'results' => $data
        ]);
    }

    /**
     * 启用-禁用用户
     * @return string
     */
    public function actionForbid()
    {
        $request = Yii::$app->request;
        $user = User::findOne(['id' => $request->post('user_id')]);
        if (!$user) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        if ($user->is_forbidden === UserModel::ENABLE) {
            $user->is_forbidden = UserModel::DISABLE;
        } elseif ($user->is_forbidden === UserModel::DISABLE) {
            $user->is_forbidden = UserModel::ENABLE;
        }
        if (!$user->save()) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '设置失败']);
        }
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    /**
     * 用户借还信息
     * @return string
     */
    public function actionLoan()
    {
        $results = $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $user = User::find()
            ->where(['id' => intval($request->get('id'))])
            ->one();
        if (!$user) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        $results = LoanModel::getUserLoanById($offset, $limit, $user->id);
        $recentDate = SiteService::getRecentRepayingDate(date('Y-m-d')); // 获取最近还款日
        foreach ($results['result'] as $row) {
            if ($row->orderDetail) {
                $productName = '';
                $productTitle = [];
                foreach ($row->orderDetail as $lt) {
                    $productTitle[] = $lt->title ;
                }
                $productName = implode(',', $productTitle);
            }
            $data[] = [
                'id' => $row->id,
                'encoding' => $row->encoding, // 借款编号
                'type' => $row->type, // 借款类型
                'shop_name' => $row->shop->shop_name ?? '--', // 商户名称
                'product_name' => $productName ?? '--', // 商品名称
                'quota' => $row->quota,  // 借款金额
                'period' => $row->repayed_count . '/' . $row->period, // 已还期数/期数
                'interest' => $row->interest, // 借款息费
                'arrival_amount' => $row->arrival_amount, // 放款金额
                'lending_at' => $row->lending_at, //放款时间
                'should_repayment_amount' => $row->quota + $row->interest, // 应还金额
                'repayment_amount' => $row->repayed_amount, // 实际已还款金额
                'planned_repayment_at' => $row->state == LoanModel::STATE_FINISHED ? '已结清' : $recentDate, // 计划还款时间
                'repayment_at' => $row->repayment_at, // 实际还款时间
                'state' =>  $row->state,
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $results['count'],
            'results' => $data
        ]);
    }

    /**
     * 获取用户反欺诈信息
     * @param user_id 用户ID
     * @return string
     */
    public function actionGetAntiFraud()
    {
        $request = Yii::$app->request;
        $userId = $request->get('user_id');
        if (empty($userId) || !is_numeric($userId)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        $userAntiFraud = AntiFraudModel::getByUserId($userId);
        if ($userAntiFraud && $userAntiFraud->content) {
            $fraudContent = json_decode($userAntiFraud->content);
            foreach ($fraudContent->riskInfo as $v) {
                $data['fraud'][] = [
                    'riskCode' => $v->riskCode ?? '', // 命中码
                    'riskCodeValue' => $v->riskCodeValue ?? '', // 命中风险等级
                    'updated_at' => $userAntiFraud->updated_at ?? '', // 更新时间
                ];
            }
        }

        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'results' => $data ?? []
        ]);
    }
}
