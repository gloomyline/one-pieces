<?php

namespace backend\controllers;

use common\models\Loan;
use common\models\LoanModel;
use common\models\UserModel;
use Yii;
use yii\helpers\Json;
use yii\helpers\Url;
use common\models\User;
use common\services\LimuService;
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
        $request = Yii::$app->request;
        $id = $request->get('id', 0);
        $user = UserModel::getUserDetailByUserId($id);
        if (!$user) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        if (isset($user->userBankList))
        $data = [
            // 会员注册信息
            'real_name' => $user->real_name ?? '', // 真实姓名
            'mobile' => $user->mobile ?? '', // 手机号码
            'identity_no' => $user->userIdentityCard->identity_no ?? '', // 身份证号
            'bank_no' => $user->userBank->bank_no ?? '', // 银行卡号
            'position' => $user->userAdditional->position ?? '', //工作岗位
            'live_addr' => $user->userBasic->live_addr ?? '', // 居住地址
            'created_at' => $user->created_at, // 注册时间
            'sex' => $user->gender ?? '', // 性别
            'age' => $user->age ?? '', // 年龄
            'education' => $user->education ?? '', // 学历
            // 身份认证信息
            'is_identity_auth' => $user->is_identity_auth, // 身份认证
            // 借款资质信息
            'is_profile_auth' => $user->is_profile_auth, // 个人信息认证
            'is_bankcard_auth' => $user->is_bankcard_auth, //银行卡认证
            'is_phone_auth' => $user->is_phone_auth, // 手机认证
            // 提升额度
            'qq' => $user->userBasic->qq ?? '', // qq
            'wechat' => $user->userBasic->wechat ?? '', // 微信
            'credit_card' => $user->userBasic->bankcard ?? '', // 常用信用卡
            'is_jd_auth' => $user->is_jd_auth, // 京东认证
            'is_taobao_auth' => $user->is_taobao_auth, // 淘宝认证
            'is_edu_auth' => $user->is_edu_auth, // 学历认证
            'is_bill_auth' => $user->is_bill_auth, // 信用卡账单认证
            'is_ebank_auth' => $user->is_ebank_auth, // 网银流水
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
        $data = LoanModel::getUserLoanById($offset, $limit, $user->id);
        foreach ($data['result'] as $row) {
            $results[] = [
                'id' => $row->id,
                'encoding' => $row->encoding,
                'quota' => $row->quota,
                'period' => $row->period,
                'interest' => $row->interest,
                'arrival_amount' => $row->arrival_amount,
                'lending_at' => $row->lending_at,
                'repayment_amount' => $row->quota + $row->interest, // 应还款金额
                'actual_repayment_amount' => $row->repayed_amount, // 实际还款金额
                'repayment_at' => $row->repayment_at,
                'state' =>  $row->state,
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $data['count'],
            'results' => $results
        ]);

    }
}
