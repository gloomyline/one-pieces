<?php

namespace frontend\controllers;

use common\extend\utils\IPUtils;
use common\models\ProductModel;
use common\models\User;
use common\models\LoanModel;
use common\models\LoanLogModel;
use common\models\UserBankModel;
use common\models\UserBasicModel;
use common\models\UserIdentityCardModel;
use common\models\UserModel;
use common\models\UserTokenModel;
use common\models\MobileCodeModel;
use common\models\FeedbackModel;
use common\config\SmsConfig;
use common\services\CouponService;
use common\services\LoanService;
use frontend\bases\FrontendController;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use Yii;

class SiteController extends FrontendController
{

    const DEFAULT_QUOTA = 2000; //默认额度
    
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        // 访问控制
        $behaviors['access']['rules'] = [
            // 允许访客用户访问的Action
            [
                'actions' => ['index', 'login', 'logout', 'signup', 'home'],
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
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionHome()
    {
        $product = ProductModel::getActiveProduct();
        if(!$product) {
            return Json::encode(['status' => self::STATUS_FAILURE, "error_message" => "获取产品配置失败，请刷新后重试"]);
        }

        $data = [
                'quota' => self::DEFAULT_QUOTA,
                'state' => '', 'success_count' => 0,
                'min_quota' =>$product->min_quota,
                'max_quota' =>$product->max_quota,
                'overdue_rate' => $product->overdue_rate * 100,
                'detail' => []
        ];

        //登入状态
        $user = Yii::$app->user->identity;
        if ($user) {
            $data['quota'] = $user->available_quota;
            $data['success_count'] = $user->success_count;
            $data['mobile'] = $user->mobile; // 手机号码
            //获取最新的借款订单
            $loan = LoanModel::getUserLatestLoan($user->id);
            if ($loan && $loan->state != LoanModel::STATE_FINISHED) {
                $data['id'] = $loan->id;
                $data['state'] = LoanService::STATE_MAP[$loan->state];

                // 判断逾期
                $difference = (time() - strtotime($loan->planned_repayment_at)) / 3600 / 24; // 当前时间与计划还款时间的差值
                $difference = $difference > 0 ? floor($difference) : false;

                if (($loan->state == LoanModel::STATE_REPAYING || $loan->state == LoanModel::STATE_OVERDUE) && $difference) { // 当前时间 > 计划还款时间 = 借款逾期 变更状态/ 确认逾期上限
                    $MaxOverdue = $product->limit_overdue_days > $difference ? false : true; // 逾期上限标识 指示是否逾期上限 true-已达逾期上限 false - 未达上限

                    $overdueDetail = LoanService::caculateRepaymentAmountDetail($loan->id);
                    $content = [
                        'overdue_day' => $difference, // 逾期天数
                        'overdue_principal' => $overdueDetail['overduePrincipal'], // 逾期中的本金 = 借款额度 + 借款息费
                        'overdue_amount' => $overdueDetail['overdueAmount'], // 逾期罚金
                        'overdue_rate' => $overdueDetail['overdueRate'], // 逾期费率
                        'max_overdue' => $MaxOverdue, // 是否达逾期上限标识 true-已达逾期上限 false- 未达逾期上限
                        'limit_overdue_days' => $product->limit_overdue_days ?? '' // 逾期上限天数
                    ];
                    $detail = ['title' => 'overdue', 'content' => $content];
                    $data['detail'][] = $detail;
                    $data['state'] = LoanModel::STATE_OVERDUE;
                } else { // 非逾期状态，正常下发日志
                    $logs = LoanLogModel::getLoanLog($loan->id);
                    if ($logs) {
                        foreach ($logs as $k=>$log) {
                            if ($k > 2) {
                                break;
                            }
                            $detail = ['title' => $log->title, 'content' => json_decode($log->content) ?? '', 'time' => strtotime($log->created_at)];
                            $data['detail'][] = $detail;
                        }
                    }
                }
            }
        }
        
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            "error_message" => "",
            'results' => [$data],
        ]);
    }

    /**
     * 登录
     */
    public function actionLogin()
    {
        $request = Yii::$app->request;
        $mobile = trim($request->post('mobile'));
        $password = trim($request->post('password'));
        
        $user = UserModel::getUserByMobile($mobile);
        if (!$user || md5($password) != $user->password) {
            return Json::encode([
                "status" => "FAILURE",
                "error_message" => "手机号或密码错误",
            ]);
        }
        // 生成token
        $userToken = UserTokenModel::generateUserToken($user->id, 'H5');
        if (!$userToken) { // 创建token失败
            return Json::encode([
                "status" => "FAILURE",
                "error_message" => "系统故障了, 请重试",
            ]);
        }
        $tokenArr = [
            'mobile' => $mobile,
            "token" => $userToken->access_token,
            "expiry_timestamp" => $userToken->expiry_timestamp
        ];
        return Json::encode([
            "status" => "SUCCESS",
            "error_message" => "",
            "results" => [$tokenArr]
        ]);
    }

    /**
     * 用户注册
     * 
     * @param string $mobile 手机号
     * @param string $password 密码
     * @param string $code 验证码
     * @return array
     */
    public function actionSignup()
    {
        $request = Yii::$app->request;
        $code = $request->post('code', '');
        $mobile = trim($request->post('mobile', ''));
        $password = trim($request->post('password', ''));

        if (empty($mobile)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请输入手机号']);
        }
        // 验证重复注册
        if (UserModel::getUserByMobile($mobile)) {
            return Json::encode([
                "status" => self::STATUS_FAILURE,
                "error_message" => "该手机号码已注册",
            ]);
        }
        if (empty($password)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请输入密码']);
        }
        if ($code == '') {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请输入验证码']);
        }
        
        //短信验证码
        $checkCode = MobileCodeModel::checkMobileCode($mobile, $code);
        if (!$checkCode) {
            return Json::encode([
                "status" => self::STATUS_FAILURE,
                "error_message" => SmsConfig::MOBILE_CODE_ERR,
            ]);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $userModel = new UserModel();
            $userIp = IPUtils::getUserIP();
            $user = $userModel->addUser($mobile, $password, $userIp);
            if (!$user) { // DB保存用户失败
                return Json::encode([
                    "status" => self::STATUS_FAILURE,
                    "error_message" => "系统异常, 请联系客服",
                ]);
            }
            $transaction->commit();
        } catch (yii\db\IntegrityException $e) {
            Yii::error($e);
            $transaction->rollBack();
            return Json::encode([
                "status" => self::STATUS_FAILURE,
                "error_message" => "系统故障了, 请重试",
            ]);
        }

        CouponService::assignRegisterCashCoupon($user->id); // 发送用户注册成功代金券
        // 生成token
        $userToken = UserTokenModel::generateUserToken($user->id, 'H5');
        if (!$userToken) { // 创建token失败
            Yii::error('error: generate token failed. userid=' . $user->id . ', source=H5');
            return Json::encode([
                "status" => self::STATUS_FAILURE,
                "error_message" => "系统故障了, 请重试",
            ]);
        }
        $tokenArr = [
            'mobile' => $mobile,
            "token" => $userToken->access_token,
            "expiry_timestamp" => $userToken->expiry_timestamp
        ];
        return Json::encode([
            "status" => self::STATUS_SUCCESS,
            "error_message" => "",
            "results" => [$tokenArr]
        ]);
    }

    public function actionLogout()
    {
        $headers = Yii::$app->request->headers;
        $user = Yii::$app->user->identity;
        if ($user) {
            Yii::$app->user->logout();
        }
        $accessToken = trim($headers->get('Authorization'));

        // 销毁token
        $droppedUserToken = UserTokenModel::dropUserToken($accessToken);
        if ($droppedUserToken === false) {
            return Json::encode([
                'status' => self::STATUS_SUCCESS,
                'error_message' => ''
            ]);
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'error_message' => ''
        ]);
    }

    /**
     * 意见反馈
     */
    public function actionFeedback()
    {
        $request = Yii::$app->request;
        $type = $request->post('type') ?? "";
        $content = $request->post('content') ?? "";

        $user = Yii::$app->user->identity;

        if (empty($type) || mb_strlen($type) > 30) {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                'error_message' => '请选择问题类型，并且不能超过10个字～'
            ]);
        }
        if (empty($content) || mb_strlen($content) > 1000) {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                'error_message' => '请填写内容，并且不能超过1000个字～'
            ]);
        }

        $data['type'] = trim($type);
        $data['content'] = trim($content);
        $data['user_id'] = $user->id;

        $result = FeedbackModel::add($data);
        if ($result) {
            return Json::encode([
                'status' => self::STATUS_SUCCESS,
                'error_message' => '提交反馈成功'
            ]);
        }

        return Json::encode([
            'status' => self::STATUS_FAILURE,
            'error_message' => "提交反馈失败，数据异常"
        ]);
    }

    public function actionIndex()
    {
        $this->redirect('/m/#/home');
    }
    /**
     * 借款合同
     */
    public function actionLoanContract()
    {
        $user = Yii::$app->user->identity;
        if ($user->is_identity_auth == UserModel::HAS_NO_IDENTITY_AUTH || $user->is_profile_auth == UserModel::HAS_NO_PROFILE_AUTH || $user->is_bankcard_auth ==  UserModel::HAS_NO_BANKCARD_AUTH  || $user->is_phone_auth ==  UserModel::HAS_NO_PHONE_AUTH ) {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                'error_message' => '您的基础认证未完成，请完成认证后继续'
            ]);
        }
        $userIdentityCard = UserIdentityCardModel::getIdentityCard($user->getId()); // 身份信息
        if (!$userIdentityCard) {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                'error_message' => '请完善身份信息认证后继续'
            ]);
        }
        $userBasic = UserBasicModel::getUserBasic($user->getId());
        if (!$userBasic) {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                'error_message' => '请完善个人信息认证后继续'
            ]);
        }
        $product = ProductModel::getActiveProduct();
        if (!$product) {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                'error_message' => '未查询到产品信息，请联系客服'
            ]);
        }
        $userBank = UserBankModel::getUserDefaultBankCard($user->getId());
        if (!$userBank) {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                'error_message' => '未查询到默认银行卡信息，请更新银行卡认证信息'
            ]);
        }
        $addr = explode('-', $userBasic->live_area);
        $data = [
            'borrower' => $userIdentityCard->real_name, // 借款人
            'id_no' => $userIdentityCard->identity_no, // 身份证号
            'addr' => sprintf('%s%s%s%s', $addr[0] ?? '', $addr[1] ?? '', $addr[2] ?? '', $userBasic->live_addr), // 住址（家庭住址）
            'mobile' => $user->mobile, // 联系电话
            'annualized_interest_rate' => $product->annualized_interest_rate, // 贷款年化利率
            'account_name' => $userBank->bank_name, // 账户名 银行名称
            'account_no' => $userBank->bank_no, // 账号
        ];
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'error_message' => '',
            'results' => $data,
        ]);
    }
}
