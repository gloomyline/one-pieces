<?php
namespace frontend\controllers;

use common\extend\payment\lianlianpay\LianlianpayApi;
use common\extend\payment\lianlianpay\LianlianpayConfig;
use common\extend\payment\lianlianpay\LianlianpayNotify;
use common\models\BudgetPlanModel;
use common\models\UserModel;
use common\models\LoanModel;
use common\models\PayLogModel;
use common\models\UserBankModel;
use common\services\LoanService;
use frontend\bases\FrontendController;
use common\models\UserIdentityCardModel;
use yii\helpers\Json;
use Yii;

/**
 * 支付相关
 */
class PaymentController extends FrontendController
{

    const PAYMENT_TYPE_LLPAY = 'llpay';

    const LLPAY_CALLBACK_RETURN = 'return';
    const LLPAY_CALLBACK_NOTIFY = 'notify';
    const LLPAY_BODY_MAX_LENGTH = 120; // 订单描述的最大长度（字节）

    private $lianlianpayConfig; // 连连支付配置

    // 禁用CSRF的action,用于异步回调接受POST
    private $disableCsrfActions = [
        'llpay-notify',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        // 访问控制
        $behaviors['access']['rules'] = [
            // 允许访客用户访问的Action
            [
                'actions' => ['lianlianpay-sign-return', 'lianlian-auth-pay-url-return'],
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

    public function beforeAction($action)
    {
        if (in_array($action->id, $this->disableCsrfActions)) {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    private function lianlianpayInit()
    {
        $this->lianlianpayConfig = LianlianpayConfig::getConfig();
        //构造要请求的参数数组，无需改动
        $parameter = [
            "oid_partner" => $this->lianlianpayConfig['oid_partner'],
            "version" => $this->lianlianpayConfig['version'],
            "sign_type" => $this->lianlianpayConfig['sign_type'],
        ];

        return $parameter;
    }

    /**
     * 连连支付签约
     */
    public function actionLianlianpaySign()
    {
        $request = Yii::$app->request;
        $idType = trim($request->get('id_type', 0)); // 证件类型,默认0，即身份证
        $cardNo = trim($request->get('card_no', '')); // 银行卡号

        if ($idType != 0 || empty($cardNo)) {
            return Json::encode([
                "status" => self::STATUS_FAILURE,
                "error_message" => '参数错误',
            ]);
        }

        //用户信息
        $userId = Yii::$app->user->identity->id;
        $userMobile = Yii::$app->user->identity->mobile;
        $userRegistTime = Yii::$app->user->identity->created_at;

        //获取身份认证
        $identityCard = UserIdentityCardModel::getIdentityCard($userId);
        if (!$identityCard || $identityCard->state == UserIdentityCardModel::STATE_NOPASS) {
            return Json::encode([
                "status" => self::STATUS_FAILURE,
                "error_message" => '请先完善个人信息认证',
            ]);
        }
        $userRealName = $identityCard->real_name;
        $userIdentityNo= $identityCard->identity_no;

        $existCard = UserBankModel::getBankByUserIdAndNo($userId, $cardNo);
        //如果卡号已经存在，并且已认证通过
        if ($existCard && $existCard->state == UserBankModel::STATE_VALID) {
            return Json::encode([
                "status" => self::STATUS_FAILURE,
                "error_message" => '该银行卡已认证',
            ]);
        } 

        //如果卡号不存在
        if (!$existCard) {
            $data = ['user_id' => $userId, 'bank_no' => $cardNo, 'bank_user' => $identityCard->real_name];
            $userBank = UserBankModel::add($data);
            if (!$userBank) { // DB保存用户银行卡失败
                return Json::encode([
                    "status" => self::STATUS_FAILURE,
                    "error_message" => "添加银行卡失败",
                ]);
            }
        }

        $params = $this->lianlianpayInit();
        $params['app_request'] = LianlianpayApi::APP_REQUEST_WAP;
        $params['user_id'] = sprintf('%s%s',LianlianpayApi::USER_ID_PREFIX, $userId);
        $params['id_type'] = $idType;
        $params['id_no'] = $userIdentityNo;
        $params['card_no'] = $cardNo;
        $params['acct_name'] = $userRealName;
        $params['pay_type'] = "I"; //分期付签约传 I
        $params['url_return'] = Yii::$app->params['llpay_sign_return_url'];
        $params['risk_item'] = '{"frms_ware_category":"2010","user_info_mercht_userno":"'. sprintf('%s%s',LianlianpayApi::USER_ID_PREFIX, $userId) . '","user_info_bind_phone":"' . $userMobile . '","user_info_dt_register":"' . date('YmdHis',strtotime($userRegistTime)) . '","user_info_identify_type":"1","user_info_identify_state":"1","user_info_full_name":"' . $userRealName . '","user_info_id_no":"' . $userIdentityNo . '"}';

        $lianlianpayApi = new LianlianpayApi($this->lianlianpayConfig);
        $requestPara = $lianlianpayApi->buildRequestPara($params);

        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'error_message' => '',
            'results' => [['req_data' => $requestPara]],
        ]);
        
    }

    /**
     * 连连支付签约回调
     * @param string $status 交易结果代码 0000
     * @param string $result 交易结果描述 {"oid_partner":"201103171000000000",
                                        "user_id":"2222222",
                                        "agreeno":"12313232313312331",
                                         "sign_type":"RSA",
                                        "sign":"ZPZULntRpJwFmGNIVKwjLEF2Tze7bqs60rxQ22CqT5J1UlvGo575QK9z/
                                        +p+7E9cOoRoWzqR6xHZ6WVv3dloyGKDR0btvrdqPgUAoeaX/YOWzTh00vwcQ+HBtX
                                        E+vPTfAqjCTxiiSJEOY7ATCF1q7iP3sfQxhS0nDUug1LP3OLk="
                                        }
     */
    public function actionLianlianpaySignReturn()
    {
        $request = Yii::$app->request;
        $status = trim($request->get('status'));
        $result = trim($request->get('result', ''));

        $lianlianpayNotify = new LianlianpayNotify();

        if ($status != '0000') {
            Yii::info('连连支付-用户签约回调失败:status:' . $status . 'result:' . $result, 'lianlianpay');

            $im = Yii::$app->companyim;
            $im->sendNotice('sys_error', $im::TECH_DEPT_CHAT_ID, '连连支付-用户签约回调失败:status:' . $status . 'result:' . $result);

            return $this->redirect(["/m/#/me/auth/bankcard/addResults?status={$status}&result={$result}"]);
        }

        $data = Json::decode($result);
        $verifyResult = $lianlianpayNotify->verifyCallback($data); // 验签
        if (!$verifyResult) {
            Yii::info('连连支付-用户签约回调验签失败:' . $result, 'lianlianpay');

            $im = Yii::$app->companyim;
            $im->sendNotice('sys_error', $im::TECH_DEPT_CHAT_ID, '连连支付-用户签约回调验签失败:' . $result);

            return $this->redirect(["/m/#/me/auth/bankcard/addResults?status={$status}&result={$result}"]);
        }

        $params = $this->lianlianpayInit();
        $params['user_id'] = $data['user_id'];
        $params['pay_type'] = 'D';
        $params['offset'] = '0';
        $params['no_agree'] = $data['agreeno'];

        // 用户签约信息查询 参考连连支付分期付WAP4.8接口
        $lianlianpayApi = new LianlianpayApi($this->lianlianpayConfig);
        $requestPara = $lianlianpayApi->buildRequestPara($params);
        $queryResult = $lianlianpayApi->queryBankcardByAgreeNo($requestPara);

        $queryData = Json::decode($queryResult);
        if ($queryData['ret_code'] != LianlianpayApi::REQUEST_SUCCESS) {
            Yii::error('连连支付-用户签约信息查询失败ret_code:' . $queryData['ret_code'] . ' ret_msg:' . $queryData['ret_msg'], 'lianlianpay');

            $im = Yii::$app->companyim;
            $im->sendNotice('sys_error', $im::TECH_DEPT_CHAT_ID, '连连支付-用户签约信息查询失败 用户' . $params['user_id'] . 'ret_code:' . $queryData['ret_code'] . ' ret_msg:' . $queryData['ret_msg']);

            exit();
        }

        $userId = explode('_', $queryData['user_id'])[1]; // 用户ID
        $agreementList = $queryData['agreement_list'];
        $bankName = $agreementList[0]['bank_name']; // 银行名称
        $noAgree = $agreementList[0]['no_agree']; // 签约协议号
        $cardNo = $agreementList[0]['card_no']; // 卡号后4位
        $cardType = $agreementList[0]['card_type']; // 银行卡类型 2-储蓄卡 3-信用卡
        $bankCode = $agreementList[0]['bank_code']; // 银行编码

        unset($queryData['agreement_list']);
        $queryVerifyResult = $lianlianpayNotify->verifyCallback($queryData); // 验签
        if (!$queryVerifyResult) {
            Yii::error('连连支付-用户签约信息验签失败:' . $queryResult, 'lianlianpay');

            $im = Yii::$app->companyim;
            $im->sendNotice('sys_error', $im::TECH_DEPT_CHAT_ID, '连连支付-用户签约信息验签失败:' . $queryResult);

            exit();
        }

        $cardInfo = ['agreeno' => $noAgree, 'bank_name' => $bankName, 'card_type' => $cardType, 'bank_code' => $bankCode, 'state' => UserBankModel::STATE_VALID];
        $userCards = UserBankModel::getBankByUserId($userId);
        if (count($userCards) == 0) {
            $cardInfo['is_default'] = UserBankModel::DEFAULT_BANKCARD;
        }
        if(!UserBankModel::update($userId, $cardNo, $cardInfo)) {
            Yii::error('连连支付-用户签约更新数据失败:user_id:' . $userId, 'lianlianpay');

            $im = Yii::$app->companyim;
            $im->sendNotice('sys_error', $im::TECH_DEPT_CHAT_ID, '连连支付-用户签约更新数据失败:user_id:' . $userId);
        }

        //更新用户表的银行卡认证字段为已认证
        UserModel::setUserBankCardAuth($userId);

        return $this->redirect(['/m/#/me/auth/bankcard/addResults?status=0000']);

    }

    /**
     * 连连支付银行卡卡 BIN 查询
     */
    public function actionLianlianpayCardbin()
    {
        $request = Yii::$app->request;
        $cardNo = trim($request->get('card_no', '')); // 银行卡号

        $params = $this->lianlianpayInit();
        $params['card_no'] = $cardNo;

        $lianlianpayApi = new LianlianpayApi($this->lianlianpayConfig);
        $requestPara = $lianlianpayApi->buildRequestPara($params);
        $queryResult = $lianlianpayApi->queryBankcardBin($requestPara);

        $queryData = Json::decode($queryResult);
        if ($queryData['ret_code'] != LianlianpayApi::REQUEST_SUCCESS) {
            return Json::encode([
                "status" => self::STATUS_FAILURE,
                "error_message" => $queryData['ret_msg'],
            ]);
        }

        $lianlianpayNotify = new LianlianpayNotify();
        $queryVerifyResult = $lianlianpayNotify->verifyCallback($queryData); // 验签
        if (!$queryVerifyResult) {
            return Json::encode([
                "status" => self::STATUS_FAILURE,
                "error_message" => '数据错误',
            ]);
        }

        return Json::encode([
            "status" => self::STATUS_SUCCESS,
            "error_message" => '',
        ]);
    }

    /**
     * WAP认证支付接口（客户主动还款）
     */
    public function actionLianlianAuthPay()
    {
        $request = Yii::$app->request;
        $loanId = $request->post('loan_id'); // 借款ID
        if (!$loanId) {
            return Json::encode([
                "status" => self::STATUS_FAILURE,
                "error_message" => '参数错误',
            ]);
        }
        $loan = LoanModel::findLoanById($loanId);
        if ($loan['state'] != LoanModel::STATE_REPAYING && $loan['state'] != LoanModel::STATE_OVERDUE) {
            return Json::encode([
                "status" => self::STATUS_FAILURE,
                "error_message" => '非法的借款参数',
            ]);
        }
        //用户信息
        $userId = Yii::$app->user->identity->id;
        $userMobile = Yii::$app->user->identity->mobile;
        $userRegistTime = Yii::$app->user->identity->created_at;

        //获取身份认证
        $identityCard = UserIdentityCardModel::getIdentityCard($userId);
        if (!$identityCard || $identityCard->state == UserIdentityCardModel::STATE_NOPASS) {
            return Json::encode([
                "status" => self::STATUS_FAILURE,
                "error_message" => '请先完善个人信息认证',
            ]);
        }
        $userRealName = $identityCard->real_name;
        $userIdentityNo= $identityCard->identity_no;

        $isReuseFlag = false; // 标识是否存在可重复提交的支付记录  true-表示存在可重复提交记录 false-表示不存在（即需要重新添加支付记录） 默认值【false】
        $activePayLog = PayLogModel::findActivePayLog($userId, $loanId); // 查询用户的有效订单
        $time = date('Y-m-d H:i:s', time()- 60 *60 *2); // 前两个小时时间
        if ($activePayLog && $activePayLog->state == PayLogModel::STATE_PENDING && $activePayLog->created_at > $time) { // 状态为初始状态 且 订单时间在两个小时以内 的订单可以被重复使用
            $isReuseFlag = true;
        }

        $repayDetail = LoanService::caculateRepaymentAmountDetail($loanId); // 查询该订单的应还款明细信息
        $money_order = $repayDetail['term_amount']; // 交易金额
        if ($money_order <= 0) {
            return Json::encode([
                "status" => self::STATUS_FAILURE,
                "error_message" => '错误的交易金额 或 借款订单本期月供已还完',
            ]);
        }

        $params = $this->lianlianpayInit(); // 最基本的系统参数，如商户编号、版本号、支付签名方式等
        $params['user_id'] = sprintf('%s%s',LianlianpayApi::USER_ID_PREFIX, $userId); // 商户用户唯一编号
        $params['app_request'] = LianlianpayApi::APP_REQUEST_WAP; // 应用请求标识 1-Android 2-ios 3-WAP

        $params['busi_partner'] = LianlianpayApi::AUTHPAY_BUSI_PARTNER_VIRTUAL; // 商户业务类型 虚拟商品销售：101001 实物商品销售：109001
        $orderNo = $isReuseFlag ? $activePayLog->no_order : LoanService::buildOrderCode($userId); // 商户系统唯一标识该付款的流水号
        $params['no_order'] = $orderNo; // 商户系统唯一订单号,此处使用了借款订单编号的生成规则
        $params['dt_order'] = date('YmdHis'); // 订单时间
        $params['name_goods'] = LianlianpayApi::AUTHPAY_NAME_GOODS; // 商品名称
        $params['info_order'] = LianlianpayApi::AUTHPAY_INFO_ORDER; // 订单描述
        $params['money_order'] = $money_order; // 交易金额
        $params['notify_url'] = Yii::$app->params['llpay_authpay_notify']; // 服务器异步通知地址，连连支付支付平台在用户支付成功后通知商户服务端的地址
        $params['url_return'] =  Yii::$app->params['llpay_authpay_url_return']; // 支付结束后显示的合作商户系统页面地址
        $params['id_type'] = LianlianpayApi::AUTHPAY_ID_TYPE_IDENTIFICATION; // 证件类型，默认为0 0-身份证
        $params['id_no'] = $userIdentityNo; // 证件号码
        $params['acct_name'] = $userRealName; // 银行帐号姓名
        $params['back_url'] = Yii::$app->params['llpay_authpay_back_url']; // 左上角返回按钮，指定返回地址 不传默认 history.go(-1)
        $params['risk_item'] = '{"frms_ware_category":"2010","user_info_mercht_userno":"'. sprintf('%s%s',LianlianpayApi::USER_ID_PREFIX, $userId) . '","user_info_bind_phone":"' . $userMobile . '","user_info_dt_register":"' . date('YmdHis',strtotime($userRegistTime)) . '","user_info_identify_type":"1","user_info_identify_state":"1","user_info_full_name":"' . $userRealName . '","user_info_id_no":"' . $userIdentityNo . '"}';// 风险控制参数
        

        if (!empty(Yii::$app->params['llpay_authpay_back_url'])) {
            $params['back_url'] = Yii::$app->params['llpay_authpay_back_url']; // 左上角返回地址
        }

        $lianlianpayApi = new LianlianpayApi($this->lianlianpayConfig);
        $requestPara = $lianlianpayApi->buildRequestPara($params); // 返回所有的参数

        if (!$isReuseFlag) { // 不可重复利用时，添加新支付记录
            $planData['repayed_type'] = BudgetPlanModel::TYPE_NORMAL; // 还款方式
            $planData['plan_detail'] = $repayDetail['plan_detail']; // 计划详情
            // 将构造完成的订单存入数据库
            PayLogModel::addPayLog(array(
                'loan_id' => $loanId, // 借款ID
                'user_id' => $userId, // 用户ID
                'pay_type' => PayLogModel::TYPE_AUTHPAY, // 支付类型：认证支付
                'no_order' => $params['no_order'], // 商户系统唯一订单号
                'info_order' => $params['info_order'], // 商品名称
                'money_order' => $params['money_order'], // 交易金额
                'dt_order' => date('Y-m-d H:i:s', strtotime($params['dt_order'])), // 订单时间
                'plan_id' => implode(',', array_column($repayDetail['plan_detail'], 'id')), // 分期计划ID，多个ID 使用,隔开
                'plan_detail' => json_encode($planData), // 分期计划详情
            ));
        } else { // 订单可复用时
            PayLogModel::updatePayLogByNoOrder($activePayLog->no_order, ['money_order' => $money_order]); // 更新下当前交易金额 原因：逾期时，逾期罚金每日金额不同
        }

        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'results' => [['req_data'=>$requestPara]],
        ]);
    }

    /**
     * 认证支付回显
     */
    public function actionLianlianAuthPayUrlReturn()
    {
        $request = Yii::$app->request;
        $result = trim($request->post('res_data', '')); // 支付成功时，有此信息
        if ($result) { // 支付成功
            $data = Json::decode($result);

            $lianlianpayNotify = new LianlianpayNotify();

            $verifyResult = $lianlianpayNotify->verifyCallback($data); // 验签
            if (!$verifyResult) {
                Yii::error('连连支付-用户认证支付回显回调验签失败:' . $result, 'lianlianpay');

                $im = Yii::$app->companyim;
                $im->sendNotice('sys_error', $im::TECH_DEPT_CHAT_ID, '用户主动还款回显回调验签失败:' . $result);

                $reason = '验签失败';
                // 其余失败或异常情况 跳转至查单页面
                return $this->redirect(["/m/#/me/refund/failure?reason={$reason}"]);// 跳转至提示页面
            }
            $payLog = PayLogModel::findPayLogByNoOrder($data['no_order']);
            if (!$payLog) {
                Yii::error('连连支付-用户认证支付回显回调失败: 查无此用户订单记录' . $result, 'lianlianpay');

                $im = Yii::$app->companyim;
                $im->sendNotice('sys_error', $im::TECH_DEPT_CHAT_ID, '用户主动还款回显回调失败: 无订单为 ' . $data['no_order'] . '的记录 ' . $result);

                $reason = sprintf('无订单号为%s的记录', $data['no_order']);
                // 其余失败或异常情况 跳转至查单页面
                return $this->redirect(["/m/#/me/refund/failure?reason={$reason}"]);// 跳转至提示页面
            }
            $amount = $payLog->money_order; // 交易金额
            $bankCode = $payLog->bank_code ?? ''; // 银行编号
            // 无论是processing 或者 success 都视为成功，但此处不更改数据，以成功回调通知为准
            return $this->redirect(["/m/#/me/refund/success?amount={$amount}&bank_code={$bankCode}"]);// 跳转至提示支付成功页面
        }
        Yii::info('连连支付-用户认证支付回显失败或异常: 未接收到回显参数，等待查单验证支付结果' . $result, 'lianlianpay');
        $reason = '支付失败';
        // 其余失败或异常情况 跳转至查单页面
        return $this->redirect(["/m/#/me/refund/failure?reason={$reason}"]);// 跳转至提示页面
    }

    /**
     * WAP认证支付接口（客户结清借款）
     */
    public function actionLianlianAuthPaySettle()
    {
        $request = Yii::$app->request;
        $loanId = $request->post('loan_id'); // 借款ID
        if (!$loanId) {
            return Json::encode([
                "status" => self::STATUS_FAILURE,
                "error_message" => '参数错误',
            ]);
        }
        $loan = LoanModel::findLoanById($loanId);
        if ($loan['state'] != LoanModel::STATE_REPAYING && $loan['state'] != LoanModel::STATE_OVERDUE) {
            return Json::encode([
                "status" => self::STATUS_FAILURE,
                "error_message" => '非法的借款参数',
            ]);
        }
        //用户信息
        $userId = Yii::$app->user->identity->id;
        $userMobile = Yii::$app->user->identity->mobile;
        $userRegistTime = Yii::$app->user->identity->created_at;

        //获取身份认证
        $identityCard = UserIdentityCardModel::getIdentityCard($userId);
        if (!$identityCard || $identityCard->state == UserIdentityCardModel::STATE_NOPASS) {
            return Json::encode([
                "status" => self::STATUS_FAILURE,
                "error_message" => '请先完善个人信息认证',
            ]);
        }
        $userRealName = $identityCard->real_name;
        $userIdentityNo= $identityCard->identity_no;

        // 判断是否有逾期项，有逾期项 直接返回提示 归还逾期款项后
        $isOverdue = LoanService::isOverdue($loanId);
        if ($isOverdue) {
            return Json::encode([
                "status" => self::STATUS_FAILURE,
                "error_message" => '请先归还已逾期款项后重新操作',
            ]);
        }

        $isReuseFlag = false; // 标识是否存在可重复提交的支付记录  true-表示存在可重复提交记录 false-表示不存在（即需要重新添加支付记录） 默认值【false】
        $activePayLog = PayLogModel::findActivePayLog($userId, $loanId); // 查询用户的有效订单
        $time = date('Y-m-d H:i:s', time()- 60 *60 *2); // 前两个小时时间
        if ($activePayLog && $activePayLog->state == PayLogModel::STATE_PENDING && $activePayLog->created_at > $time) { // 状态为初始状态 且 订单时间在两个小时以内 的订单可以被重复使用
            $isReuseFlag = true;
        }

        $repayDetail = LoanService::caculateRepaymentAmountSettleDetail($loanId); // 查询该订单的应还款明细信息
        $money_order = $repayDetail['term_amount']; // 交易金额
        if ($money_order <= 0) {
            return Json::encode([
                "status" => self::STATUS_FAILURE,
                "error_message" => '错误的交易金额 或 借款订单本期月供已还完',
            ]);
        }

        $params = $this->lianlianpayInit(); // 最基本的系统参数，如商户编号、版本号、支付签名方式等
        $params['user_id'] = sprintf('%s%s',LianlianpayApi::USER_ID_PREFIX, $userId); // 商户用户唯一编号
        $params['app_request'] = LianlianpayApi::APP_REQUEST_WAP; // 应用请求标识 1-Android 2-ios 3-WAP

        $params['busi_partner'] = LianlianpayApi::AUTHPAY_BUSI_PARTNER_VIRTUAL; // 商户业务类型 虚拟商品销售：101001 实物商品销售：109001
        $orderNo = $isReuseFlag ? $activePayLog->no_order : LoanService::buildOrderCode($userId); // 商户系统唯一标识该付款的流水号
        $params['no_order'] = $orderNo; // 商户系统唯一订单号,此处使用了借款订单编号的生成规则
        $params['dt_order'] = date('YmdHis'); // 订单时间
        $params['name_goods'] = LianlianpayApi::AUTHPAY_NAME_GOODS; // 商品名称
        $params['info_order'] = LianlianpayApi::AUTHPAY_INFO_ORDER; // 订单描述
        $params['money_order'] = $money_order; // 交易金额
        $params['notify_url'] = Yii::$app->params['llpay_authpay_notify']; // 服务器异步通知地址，连连支付支付平台在用户支付成功后通知商户服务端的地址
        $params['url_return'] =  Yii::$app->params['llpay_authpay_url_return']; // 支付结束后显示的合作商户系统页面地址
        $params['id_type'] = LianlianpayApi::AUTHPAY_ID_TYPE_IDENTIFICATION; // 证件类型，默认为0 0-身份证
        $params['id_no'] = $userIdentityNo; // 证件号码
        $params['acct_name'] = $userRealName; // 银行帐号姓名
        $params['back_url'] = Yii::$app->params['llpay_authpay_back_url']; // 左上角返回按钮，指定返回地址 不传默认 history.go(-1)
        $params['risk_item'] = '{"frms_ware_category":"2010","user_info_mercht_userno":"'. sprintf('%s%s',LianlianpayApi::USER_ID_PREFIX, $userId) . '","user_info_bind_phone":"' . $userMobile . '","user_info_dt_register":"' . date('YmdHis',strtotime($userRegistTime)) . '","user_info_identify_type":"1","user_info_identify_state":"1","user_info_full_name":"' . $userRealName . '","user_info_id_no":"' . $userIdentityNo . '"}';// 风险控制参数

        if (!empty(Yii::$app->params['llpay_authpay_back_url'])) {
            $params['back_url'] = Yii::$app->params['llpay_authpay_back_url']; // 左上角返回地址
        }

        $lianlianpayApi = new LianlianpayApi($this->lianlianpayConfig);
        $requestPara = $lianlianpayApi->buildRequestPara($params); // 返回所有的参数

        if (!$isReuseFlag) { // 不可重复利用时，添加新支付记录
            $planData['repayed_type'] = BudgetPlanModel::TYPE_EARLY; // 还款方式
            $planData['prepayment_fee'] = $repayDetail['prepayment_fee']; // 提前还款手续费
            $planData['plan_detail'] = $repayDetail['plan_detail']; // 计划详情
            // 将构造完成的订单存入数据库
            PayLogModel::addPayLog(array(
                'loan_id' => $loanId, // 借款ID
                'user_id' => $userId, // 用户ID
                'pay_type' => PayLogModel::TYPE_AUTHPAY, // 支付类型：认证支付
                'no_order' => $params['no_order'], // 商户系统唯一订单号
                'info_order' => $params['info_order'], // 商品名称
                'money_order' => $params['money_order'], // 交易金额
                'dt_order' => date('Y-m-d H:i:s', strtotime($params['dt_order'])), // 订单时间
                'plan_id' => implode(',', array_column($repayDetail['plan_detail'], 'id')), // 分期计划ID，多个ID 使用,隔开
                'plan_detail' => json_encode($planData), // 分期计划详情
            ));
        } else { // 订单可复用时
            PayLogModel::updatePayLogByNoOrder($activePayLog->no_order, ['money_order' => $money_order]); // 更新下当前交易金额 原因：逾期时，逾期罚金每日金额不同
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'results' => [['req_data'=>$requestPara]],
        ]);
    }

    /**
     * 确认借款还清明细
     */
    public function actionConfirmSettle()
    {
        $request = Yii::$app->request;
        $loanId = $request->post('loan_id'); // 借款ID
        if (!$loanId) {
            return Json::encode([
                "status" => self::STATUS_FAILURE,
                "error_message" => '参数错误',
            ]);
        }
        $userId = Yii::$app->user->getId();
        $defaultBankCard = UserBankModel::getUserDefaultBankCard($userId);
        if (!$defaultBankCard) {
            return Json::encode([
                "status" => self::STATUS_FAILURE,
                "error_message" => '请先完善银行卡认证后继续',
            ]);
        }
        $repayDetail = LoanService::caculateRepaymentAmountSettleDetail($loanId); // 查询该订单的应还款明细信息
        $data = [
            'term_amount' => $repayDetail['term_amount'],
            'surplus_principal' => $repayDetail['loan_principal'], // 剩余借款本金（包含当期金额）
            'term_interest' => $repayDetail['term_interest'],
            'prepayment_fee' => $repayDetail['prepayment_fee'],
            'bank_code' => $defaultBankCard['bank_code'],
            'end_bank_no' => substr($defaultBankCard['bank_no'] ?? '', -4),
            'bank_name'=> $defaultBankCard['bank_name'],
        ];

        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'results' => $data,
        ]);
    }

}
