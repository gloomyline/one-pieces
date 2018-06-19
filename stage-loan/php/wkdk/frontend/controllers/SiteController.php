<?php

namespace frontend\controllers;

use common\extend\utils\IPUtils;
use common\models\ProductModel;
use common\models\ShopSettledModel;
use common\models\UserBankModel;
use common\models\UserBasicModel;
use common\models\UserIdentityCardModel;
use common\models\UserModel;
use common\models\UserTokenModel;
use common\models\MobileCodeModel;
use common\models\FeedbackModel;
use common\config\SmsConfig;
use common\services\RedisService;
use frontend\bases\FrontendController;
use yii\helpers\Json;
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
                'actions' => ['index', 'login', 'logout', 'signup', 'home', 'shop-settled'],
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
        $products = ProductModel::getAllActiveProduct();
        if(!$products) {
            return Json::encode(['status' => self::STATUS_FAILURE, "error_message" => "获取产品配置失败，请刷新后重试"]);
        }
        $user = Yii::$app->user->identity;
        $data = [];
        foreach ($products as $k => $v) {
            $data[] = [
                'product_id' => $v->id, // 产品id
                'title' => $v->title, // 产品名称
                'type' => $v->type, // 产品分类 1：现金分期 2：消费分期
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            "error_message" => "",
            'results' => $data,
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
     * 获取现金分期产品最高额度
     * @return string
     */
    public function actionGetCashProductQuota()
    {
        $product = ProductModel::getProductByCondition(['type' => ProductModel::TYPE_CASH, 'active' => ProductModel::STATE_ACTIVE]);
        if (!$product) {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                'error_message' => '获取产品配置失败，请联系客服'
            ]);
        }
        $data['max_quota'] = $product['max_quota'] ?? 0.00;
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            "error_message" => "",
            'results' => $data,
        ]);
    }

    /**
     * 商户入驻
     */
    public function actionShopSettled()
    {
        $request = Yii::$app->request;
        $shopName = $request->post('shop_name', '');
        $contactsName = $request->post('contacts_name', '');
        $contactsMobile = $request->post('contacts_mobile', '');
        $contactsAddr = $request->post('contacts_addr', '');

        $settledIp =sprintf('%s%s', RedisService::SETTLED_PREFIX, $_SERVER["REMOTE_ADDR"]); // 入驻的商家IP
        try {
            if ((integer)RedisService::getKey($settledIp) >= 2) {
                return Json::encode([
                    'status' => self::STATUS_FAILURE,
                    'error_message' =>'您今日已多次提交申请，请耐心等待',
                ]);
            }
        } catch (yii\base\ErrorException $e) {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                'error_message' => '服务器异常，请检查redis服务是否开启'
            ]);
        }
        if (empty($shopName)) {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                'error_message' => '请填写商户名称～'
            ]);
        }
        if (empty($contactsName)) {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                'error_message' => '请填写联系人名称～'
            ]);
        }
        if (empty($contactsMobile)) {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                'error_message' => '请填写联系电话～'
            ]);
        }
        if (empty($contactsAddr)) {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                'error_message' => '请填写联系地址～'
            ]);
        }
        $result = ShopSettledModel::addShopSettled($shopName, $contactsName, $contactsMobile, $contactsAddr); // 保存商户入驻信息
        if ($result) {
            try {
                $expireSeconds = strtotime(date('Y-m-d',time() + 24*3600)) - time(); // 距离明天00:00:00分的秒数
                $newValue = (integer)RedisService::getKey($settledIp) + 1; // 本日入驻次数 + 1
                RedisService::setKeyWithExpire($settledIp, $newValue, $expireSeconds); // 设置00:00:00秒键值过期
            } catch (yii\base\ErrorException $e) {
                return Json::encode([
                    'status' => self::STATUS_FAILURE,
                    'error_message' => '服务器异常，请检查redis服务是否开启'
                ]);
            }
            return Json::encode([
                'status' => self::STATUS_SUCCESS,
                'error_message' => '您的信息已提交，请等待业务员联系，我们期待您的加入~'
            ]);
        }
        return Json::encode([
            'status' => self::STATUS_FAILURE,
            'error_message' => "提交信息失败，请重试~"
        ]);
    }

    /**
     * 获取手机号码
     */
    public function actionGetMobile()
    {
        $user = Yii::$app->user->identity; // 登录用户信息
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            "error_message" => "",
            'results' => ['mobile' => $user->mobile ?? ''],
        ]);

    }

    /**
     * 借款合同
     */
    public function actionLoanContract()
    {
        $request = Yii::$app->request;
        $loanType = $request->get('loan_type'); // 借款类型 1-现金分期  2-消费分期
        if (!$loanType || !in_array($loanType, [1, 2])) {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                'error_message' => '参数错误'
            ]);
        }
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
        $product = ProductModel::getProductByCondition(['type' =>$loanType]); // 根据消费类型 获取对应的分期产品
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
            'repayment_day' => Yii::$app->params['repayment_day'], // 每期还款日
        ];
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'error_message' => '',
            'results' => $data,
        ]);
    }


}
