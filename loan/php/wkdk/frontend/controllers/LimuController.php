<?php
namespace frontend\controllers;

use common\models\UserMobileReportModel;
use common\models\UserModel;
use common\models\UserLimuModel;
use Yii;
use yii\helpers\Json;
use common\models\UserIdentityCardModel;
use common\services\LimuService;
use common\extend\Tool;
use frontend\bases\FrontendController;

class LimuController extends FrontendController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        // 访问控制
        $behaviors['access']['rules'] = [
            // 允许访客用户访问的Action
            [
                'actions' => ['limu-mobile'],
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

    public function actionMobileArea()
    {
        $request = Yii::$app->request;
        $mobile = $request->post('mobile', '');
        if (empty($mobile)) {
           return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请输入手机号']); 
        }

        $content = LimuService::getMobileArea($mobile);
        if ($content) {
            $result = json_decode($content);
            if ($result->code == LimuService::API_SUCCESS_CODE) {
                return Json::encode([
                    'status' => self::STATUS_SUCCESS,
                    'error_message' => '',
                    'results' => $result->data
                ]);
            }
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => $result->msg]);
        }
        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
    }

    public function actionLimuIdentity()
    {
        $user = Yii::$app->user->identity;

        $request = Yii::$app->request;
        $realName = $request->post('username', '');
        if (empty($realName)) {
           return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请输入用户名']); 
        }
        $identityNo = $request->post('identityno', '');
        if (empty($identityNo)) {
           return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请输入身份证号']); 
        }

        $identity = UserIdentityCardModel::getIdentityCard($user->id);
        if ($identity && $identity->state == UserIdentityCardModel::STATE_PASS) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '不能重复认证']);
        } 

        $result = LimuService::queryIdentity($realName, $identityNo);
        if ($result) {
            if ($result->code == LimuService::API_SUCCESS_CODE) {
                if ($result->data->resultCode == 'R001') {//认证一致
                    $state = UserIdentityCardModel::STATE_PASS;
                    UserModel::updateUserIdentity($user->id, $realName);
                } else {
                    $state = UserIdentityCardModel::STATE_NOPASS;
                }

                if ($identity) {
                    UserIdentityCardModel::updateIdentityCard($user->id, $realName, $identityNo, $state);
                } else {
                    UserIdentityCardModel::addIdentityCard($user->id, $realName, $identityNo, $state);
                }
                return Json::encode([
                    'status' => self::STATUS_SUCCESS,
                    'error_message' => $state,
                ]);
            }
            Yii::error($result, 'limu');
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '查询失败']);
        }
        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
    }

    /**
     * 获取身份认证信息
     * @return string 返回查询的结果
     */
    public function actionLimuGetIdentity()
    {
        $userTdentityCard = new UserIdentityCardModel();

        $result = $userTdentityCard->getIdentityCard(Yii::$app->user->getId()); // 获取身份信息
        $data[] = [
            'real_name' => $result->real_name ?? '', // 真实姓名
            'identity_no' => $result->identity_no ?? '', // 身份证号
            'state' => $result->state ?? '', // 状态 pass：验证通过 nopass：验证不通过
        ];

        return Json::encode([
            "status" => self::STATUS_SUCCESS,
            "error_message" => '',
            "results" => $data
        ]);
    }

    // 运营商报告采集任务提交
    public function actionLimuMobileReportTask()
    {
        ini_set('max_execution_time','600');

        $user = Yii::$app->user->identity;

        $request = Yii::$app->request;
        if (empty($user->mobile)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '手机号不存在']);
        }
        $serviceCode = $request->post('servicecode', '');
        if (empty($serviceCode)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请输入服务密码']);
        }

        // 判断当天是否有认证成功的记录
        $latestCredit = UserMobileReportModel::getLatestCredit($user->mobile);
        if ($latestCredit && $latestCredit->state == UserMobileReportModel::STATE_PASS) {
            $updatedDay = date('Y-m-d', strtotime($latestCredit->updated_at));
            $today = date('Y-m-d');
            if ($today == $updatedDay) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '已经认证成功']);
            }
        }

        // 获取身份证号码、真实姓名
        $userIdentity = UserIdentityCardModel::getIdentityCard($user->id);
        if (!$userIdentity || $userIdentity->state == UserIdentityCardModel::STATE_NOPASS) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '身份信息未成功认证，请先认证身份后继续']);
        }

        $result = LimuService::queryMobileReport($user->mobile, $serviceCode, $userIdentity->identity_no, $userIdentity->real_name);

        if ($result) {
            //受理成功
            if ($result->code == LimuService::API_ACCEPT_SUCCESS_CODE) {
                UserMobileReportModel::addUserMobileReport($user->id, $user->mobile, UserMobileReportModel::STATE_BUSY, $result->token);
                $count = 200;
                $sleepCount = 3;
                do {
                    $result = LimuService::queryMobileReportTaskStatus($result->token);
                    if (LimuService::API_WAIT_INPUT_CODE == $result->code) { // 等待输入验证码
                        Yii::info('立木征信报告查询手机运营商提示输入验证码:token->' . $result->token, 'limu');
                        return Json::encode([
                            'status' => self::STATUS_SUCCESS,
                            'error_message' => $result->code,
                            "results" => [['token' => $result->token]]
                        ]);
                        break;
                    } elseif (LimuService::API_ACCEPT_SUCCESS_CODE == $result->code) { // 无需输入验证码
                        Yii::info('立木征信报告查询手机运营商登入成功，退出轮询:token->' . $result->token, 'limu');
                        return Json::encode([
                            'status' => self::STATUS_SUCCESS,
                            'error_message' => $result->code,
                            "results" => [['token' => $result->token]]
                        ]);
                    } elseif (LimuService::API_SUCCESS_CODE == $result->code) { // 成功，无需输入验证码
                        Yii::info('立木征信报告查询手机运营商成功，退出轮询:token->' . $result->token, 'limu');
                        return Json::encode([
                            'status' => self::STATUS_FAILURE,
                            'error_message' => '认证成功',
                            "results" => [['token' => $result->token]]
                        ]);
                    } elseif (!empty($result->code) && !Tool::startWith($result->code, '0')) { // 不是以0开头的，视为错误，退出轮询
                        return Json::encode([
                            'status' => self::STATUS_FAILURE,
                            'error_message' => $result->msg . '[' . $result->code . ']',
                        ]);
                        break;
                    }
                    sleep($sleepCount);
                    if (($count-- <= 0) || !$result) {
                        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '获取数据失败']);
                        break;
                    }
                } while(true);
            }
        }
        return Json::encode([
            'status' => self::STATUS_FAILURE,
            'error_message' => $result->msg . '[' . $result->code . ']',
            "results" => [['token' => $result->token]]
        ]);
    }
    // 运营商报告验证码输入
    public function actionLimuMobileReportInput()
    {
        $request = Yii::$app->request;
        $token = $request->post('token', '');
        $smscode = $request->post('smscode', '');
        if (empty($smscode)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请输入验证码']);
        }
        if (empty($token)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => 'token不能为空']);
        }

        $result = LimuService::mobileReportSendSms($token, $smscode);
        if ($result) {
            Yii::info('立木征信报告查询手机运营商用户输入验证码成功:token->' . $token, 'limu');
            return Json::encode([
                'status' => self::STATUS_SUCCESS,
                'error_message' => $result->code,
                "results" => ['token' => $result->token]
            ]);
        }
        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => $result->msg]);
    }
    // 运营商报告采集状态查询
    public function actionLimuMobileReportContinue()
    {
        ini_set('max_execution_time','1600');

        $request = Yii::$app->request;
        $token = $request->post('token', '');

        $count = 200;
        $sleepCount = 3;
        do {
            $result = LimuService::queryMobileReportTaskStatus($token);
            if (LimuService::API_ACCEPT_SUCCESS_CODE == $result->code || LimuService::API_LOGIN_SUCCESS_CODE == $result->code) {
                UserModel::setUserPhoneAuth(Yii::$app->user->getId(), UserModel::HAS_PHONE_AUTH_SUBMIT); // 变更手机认证状态为已提交
                Yii::info('立木征信报告查询手机运营商登入成功，轮询结束，轮询次数->'.$count.':token->' . $token, 'limu');
                return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => $result->code]);
                break;
            } elseif(LimuService::API_SUCCESS_CODE == $result->code) { // 成功
                Yii::info('立木征信报告查询手机运营商成功，轮询结束，轮询次数->'.$count.':token->' . $token, 'limu');
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '认证成功']);
                break;
            } elseif(LimuService::API_WAIT_INPUT_CODE == $result->code) { // 等待输入验证码
                Yii::info('立木征信报告查询手机运营商提示输入验证码:token->' . $result->token, 'limu');
                return Json::encode([
                    'status' => self::STATUS_FAILURE,
                    'error_message' => $result->code,
                    "results" => [['token' => $result->token]]
                ]);
                break;
            } elseif (!empty($result->code) && !Tool::startWith($result->code, '0')) { // 不是以0开头的，视为错误，退出轮询
                return Json::encode([
                    'status' => self::STATUS_FAILURE,
                    'error_message' => $result->msg . '[' . $result->code . ']',
                ]);
                break;
            }
            sleep($sleepCount);
            if (($count-- <= 0) || !$result) {
                Yii::info('立木征信报告查询手机运营商轮询结束，轮询次数->'.$count.':token->' . $token, 'limu');
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '获取数据失败']);
                break;
            }
        } while(true);
    }

    // 京东认证
    public function actionLimuJdAuth()
    {
        ini_set('max_execution_time','600');
        $request = Yii::$app->request;
        $username = $request->post('username', ''); // 京东用户帐号
        $password = $request->post('password', ''); // 密码

        if (empty($username)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '用户帐号不能为空']);
        }
        if (empty($password)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '密码不能为空']);
        }

        $user = Yii::$app->user->identity;
        // 判断当天是否有认证成功的记录
        $latestCredit = UserLimuModel::getLatestCredit($user->mobile, UserLimuModel::TYPE_JD);
        if ($latestCredit && $latestCredit->state == UserLimuModel::STATE_PASS) {
            $updatedDay = date('Y-m-d', strtotime($latestCredit->updated_at));
            $today = date('Y-m-d');
            if ($today == $updatedDay) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '已经认证成功']);
            }
        }

        $result = LimuService::queryJd($user->mobile, $username, $password);
        if ($result) {
            // 受理成功
            if ($result->code == LimuService::API_ACCEPT_SUCCESS_CODE) {
                UserLimuModel::addUserLimu($user->id, $user->mobile, UserLimuModel::STATE_BUSY, $result->token, UserLimuModel::TYPE_JD);  // 添加数据至数据库
                $count = 200;
                $sleepCount = 3;
                do {
                    $result = LimuService::commonStatusGet($result->token, LimuService::API_BIZ_JD); // 查询状态
                    if (LimuService::API_WAIT_INPUT_CODE == $result->code) { // 等待输入验证码
                        Yii::info('立木征信京东查询提示输入验证码:token->' . $result->token, 'limu');
                        return Json::encode([
                            'status' => self::STATUS_SUCCESS,
                            'error_message' => $result->code,
                            "results" => [['token' => $result->token]]
                        ]);
                        break;
                    } elseif (LimuService::API_ACCEPT_SUCCESS_CODE == $result->code) { // 受理成功，无需输入验证码
                        Yii::info('立木征信京东查询登入成功，退出轮询:token->' . $result->token, 'limu');
                        return Json::encode([
                            'status' => self::STATUS_SUCCESS,
                            'error_message' => $result->code,
                            "results" => [['token' => $result->token]]
                        ]);
                    } elseif (LimuService::API_SUCCESS_CODE == $result->code) { // 成功，无需输入验证码
                        Yii::info('立木征信京东查询成功，退出轮询:token->' . $result->token, 'limu');
                        return Json::encode([
                            'status' => self::STATUS_SUCCESS,
                            'error_message' => '认证成功',
                            "results" => [['token' => $result->token]]
                        ]);
                    } elseif (!empty($result->code) && !Tool::startWith($result->code, '0')) { // 不是以0开头的，视为错误，退出轮询
                        return Json::encode([
                            'status' => self::STATUS_FAILURE,
                            'error_message' => $result->msg . '[' . $result->code . ']',
                        ]);
                        break;
                    }
                    sleep($sleepCount);
                    if (($count-- <= 0) || !$result) {
                        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '获取数据失败']);
                        break;
                    }
                } while(true);
            }
        }
        return Json::encode([
            'status' => self::STATUS_FAILURE,
            'error_message' => $result->msg . '[' . $result->code . ']',
            "results" => [['token' => $result->token]]
        ]);
    }
    // 京东验证码输入
    public function actionLimuJdAuthInput()
    {
        $request = Yii::$app->request;
        $token = $request->post('token', '');
        $smscode = $request->post('smscode', '');
        if (empty($smscode)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请输入验证码']);
        }
        if (empty($token)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => 'token不能为空']);
        }

        $result = LimuService::commonSendSms($token, $smscode);
        if ($result) {
            Yii::info('立木征信京东查询用户输入验证码成功:token->' . $token, 'limu');
            return Json::encode([
                'status' => self::STATUS_SUCCESS,
                'error_message' => $result->code,
                "results" => ['token' => $result->token]
            ]);
        }
        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => $result->msg]);
    }
    // 京东查询
    public function actionLimuJdAuthContinue()
    {
        ini_set('max_execution_time','1600');

        $request = Yii::$app->request;
        $token = $request->post('token', '');

        $count = 200;
        $sleepCount = 3;
        do {
            $result = LimuService::commonStatusGet($token, LimuService::API_BIZ_JD);
            if (LimuService::API_ACCEPT_SUCCESS_CODE == $result->code || LimuService::API_LOGIN_SUCCESS_CODE == $result->code) {
                Yii::info('立木征信京东查询登入成功，轮询结束，轮询次数->'.$count.':token->' . $token, 'limu');
                return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => $result->code]);
                break;
            } elseif (LimuService::API_SUCCESS_CODE == $result->code) { // 成功，无需输入验证码
                Yii::info('立木征信京东查询成功，退出轮询:token->' . $result->token, 'limu');
                return Json::encode([
                    'status' => self::STATUS_SUCCESS,
                    'error_message' => '认证成功',
                    "results" => [['token' => $result->token]]
                ]);
            } elseif (!empty($result->code) && !Tool::startWith($result->code, '0')) { // 不是以0开头的，视为错误，退出轮询
                return Json::encode([
                    'status' => self::STATUS_FAILURE,
                    'error_message' => $result->msg . '[' . $result->code . ']',
                ]);
                break;
            }
            sleep($sleepCount);
            if (($count-- <= 0) || !$result) {
                Yii::info('立木征信京东查询轮询结束，轮询次数->'.$count.':token->' . $token, 'limu');
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '获取数据失败']);
                break;
            }
        } while(true);
    }

    // 淘宝认证
    public function actionLimuTaobaoAuth()
    {
        ini_set('max_execution_time','600');

        $user = Yii::$app->user->identity;
        // 判断当天是否有认证成功的记录
        $latestCredit = UserLimuModel::getLatestCredit($user->mobile, UserLimuModel::TYPE_TAOBAO);
        if ($latestCredit && $latestCredit->state == UserLimuModel::STATE_PASS) {
            $updatedDay = date('Y-m-d', strtotime($latestCredit->updated_at));
            $today = date('Y-m-d');
            if ($today == $updatedDay) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '已经认证成功']);
            }
        }

        $result = LimuService::queryTaobao($user->mobile);
        if ($result) {
            // 受理成功
            if ($result->code == LimuService::API_ACCEPT_SUCCESS_CODE) {
                UserLimuModel::addUserLimu($user->id, $user->mobile, UserLimuModel::STATE_BUSY, $result->token, UserLimuModel::TYPE_TAOBAO);  // 添加数据至数据库
                $count = 200;
                $sleepCount = 3;
                do {
                    $result = LimuService::commonStatusGet($result->token, LimuService::API_BIZ_TAOBAO); // 查询状态
                    if (LimuService::API_WAIT_INPUT_CODE == $result->code) { // 等待输入验证码
                        Yii::info('立木征信淘宝查询提示扫码登录:token->' . $result->token, 'limu');
                        return Json::encode([
                            'status' => self::STATUS_SUCCESS,
                            'error_message' => $result->code,
                            "results" => [['token' => $result->token, 'input'=>'data:image/jpg;base64,' . $result->input->value]]
                        ]);
                        break;
                    } elseif (LimuService::API_SUCCESS_CODE == $result->code) { // 成功，无需输入验证码
                        Yii::info('立木征信淘宝查询成功，退出轮询:token->' . $result->token, 'limu');
                        return Json::encode([
                            'status' => self::STATUS_SUCCESS,
                            'error_message' => '认证成功',
                            "results" => [['token' => $result->token]]
                        ]);
                    } elseif (LimuService::API_ACCEPT_SUCCESS_CODE == $result->code) { // 受理成功，无需输入验证码
                        Yii::info('立木征信淘宝查询登入成功，退出轮询:token->' . $result->token, 'limu');
                        return Json::encode([
                            'status' => self::STATUS_SUCCESS,
                            'error_message' => $result->code,
                            "results" => [['token' => $result->token]]
                        ]);
                    } elseif (!empty($result->code) && !Tool::startWith($result->code, '0')) { // 不是以0开头的，视为错误，退出轮询
                        return Json::encode([
                            'status' => self::STATUS_FAILURE,
                            'error_message' => $result->msg . '[' . $result->code . ']',
                        ]);
                        break;
                    }
                    sleep($sleepCount);
                    if (($count-- <= 0) || !$result) {
                        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '获取数据失败']);
                        break;
                    }
                } while(true);
            }
        }
        return Json::encode([
            'status' => self::STATUS_FAILURE,
            'error_message' => $result->msg . '[' . $result->code . ']',
            "results" => [['token' => $result->token]]
        ]);

    }
    // 淘宝查询
    public function actionLimuTaobaoAuthContinue()
    {
        ini_set('max_execution_time','1600');

        $request = Yii::$app->request;
        $token = $request->post('token', '');

        $count = 200;
        $sleepCount = 3;
        do {
            $result = LimuService::commonStatusGet($token, LimuService::API_BIZ_TAOBAO);
            if (LimuService::API_ACCEPT_SUCCESS_CODE == $result->code || LimuService::API_LOGIN_SUCCESS_CODE == $result->code) {
                Yii::info('立木征信淘宝查询登入成功，轮询结束，轮询次数->'.$count.':token->' . $token, 'limu');
                return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => $result->code]);
                break;
            } elseif (LimuService::API_SUCCESS_CODE == $result->code) { // 成功，无需输入验证码
                Yii::info('立木征信淘宝查询成功，退出轮询:token->' . $result->token, 'limu');
                return Json::encode([
                    'status' => self::STATUS_SUCCESS,
                    'error_message' => '认证成功',
                    "results" => [['token' => $result->token]]
                ]);
            } elseif (!empty($result->code) && !Tool::startWith($result->code, '0')) { // 不是以0开头的，视为错误，退出轮询
                return Json::encode([
                    'status' => self::STATUS_FAILURE,
                    'error_message' => $result->msg . '[' . $result->code . ']',
                ]);
                break;
            }
            sleep($sleepCount);
            if (($count-- <= 0) || !$result) {
                Yii::info('立木征信淘宝查询轮询结束，轮询次数->'.$count.':token->' . $token, 'limu');
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '获取数据失败']);
                break;
            }
        } while(true);
    }

    // 学历查询
    public function actionLimuEduAuth()
    {
        ini_set('max_execution_time','600');

        $request = Yii::$app->request;
        $username = $request->post('username', '');
        $password = $request->post('password', '');
        if (empty($username) || empty($password)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }

        $user = Yii::$app->user->identity;
        // 判断当天是否有认证成功的记录
        $latestCredit = UserLimuModel::getLatestCredit($user->mobile, UserLimuModel::TYPE_EDUCATION);
        if ($latestCredit && $latestCredit->state == UserLimuModel::STATE_PASS) {
            $updatedDay = date('Y-m-d', strtotime($latestCredit->updated_at));
            $today = date('Y-m-d');
            if ($today == $updatedDay) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '已经认证成功']);
            }
        }

        $result = LimuService::queryEdu($user->mobile, $username, $password);
        if ($result) {
            // 受理成功
            if ($result->code == LimuService::API_ACCEPT_SUCCESS_CODE) {
                UserLimuModel::addUserLimu($user->id, $user->mobile, UserLimuModel::STATE_BUSY, $result->token, UserLimuModel::TYPE_EDUCATION);  // 添加数据至数据库
                $count = 200;
                $sleepCount = 3;
                do {
                    $result = LimuService::commonStatusGet($result->token, LimuService::API_BIZ_EDUCATION); // 查询状态
                    if (LimuService::API_WAIT_INPUT_CODE == $result->code) { // 等待输入验证码
                        Yii::info('立木征信学历查询提示输入验证码:token->' . $result->token, 'limu');
                        return Json::encode([
                            'status' => self::STATUS_SUCCESS,
                            'error_message' => $result->code,
                            "results" => [['token' => $result->token, 'input'=>'data:image/jpg;base64,' . $result->input->value]]
                        ]);
                        break;
                    } elseif (LimuService::API_SUCCESS_CODE == $result->code) { // 成功，无需输入验证码
                        Yii::info('立木征信学历查询成功，退出轮询:token->' . $result->token, 'limu');
                        return Json::encode([
                            'status' => self::STATUS_SUCCESS,
                            'error_message' => '认证成功',
                            "results" => [['token' => $result->token]]
                        ]);
                    } elseif (LimuService::API_ACCEPT_SUCCESS_CODE == $result->code) { // 受理成功，无需输入验证码
                        Yii::info('立木征信学历查询登入成功，退出轮询:token->' . $result->token, 'limu');
                        return Json::encode([
                            'status' => self::STATUS_SUCCESS,
                            'error_message' => $result->code,
                            "results" => [['token' => $result->token]]
                        ]);
                    } elseif (!empty($result->code) && !Tool::startWith($result->code, '0')) { // 不是以0开头的，视为错误，退出轮询

                        return Json::encode([
                            'status' => self::STATUS_FAILURE,
                            'error_message' => $result->msg . '[' . $result->code . ']',
                        ]);
                        break;
                    }
                    sleep($sleepCount);
                    if (($count-- <= 0) || !$result) {
                        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '获取数据失败']);
                        break;
                    }
                } while(true);
            } elseif (!empty($result->code) && !Tool::startWith($result->code, '0')) { // 不是以0开头的，视为错误，退出轮询

                return Json::encode([
                    'status' => self::STATUS_FAILURE,
                    'error_message' => $result->msg . '[' . $result->code . ']',
                ]);
            }
        }
        return Json::encode([
            'status' => self::STATUS_FAILURE,
            'error_message' => $result->msg . '[' . $result->code . ']',
            "results" => [['token' => $result->token]]
        ]);
    }

    // 信用卡账单查询
    public function actionLimuCreditCardBill()
    {
        ini_set('max_execution_time','600');

        $request = Yii::$app->request;
        $username = $request->post('username', '');
        $password = $request->post('password', '');
        if (empty($username)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        // 验证邮箱帐号
        $suffix = explode('@', $username)[1] ?? ''; // 获取登录邮箱后缀
        if (!in_array($suffix, LimuService::LIMU_BILL_SUPPORT_EMAIL)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '不支持的邮箱类型']);
        }
        // qq邮箱
        if ($suffix == 'qq.com') {
            $loginType = LimuService::API_LOGIN_TYPE_QR . '|qq'; // 登录方式 QQ邮箱使用扫码登录
        } else {
            if (empty($password)) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
            }
            $loginType = LimuService::API_LOGIN_TYPE_NORMAL; // 常规登录
        }

        $user = Yii::$app->user->identity;
        // 判断当天是否有认证成功的记录
        $latestCredit = UserLimuModel::getLatestCredit($user->mobile, UserLimuModel::TYPE_BILL);
        if ($latestCredit && $latestCredit->state == UserLimuModel::STATE_PASS) {
            $updatedDay = date('Y-m-d', strtotime($latestCredit->updated_at));
            $today = date('Y-m-d');
            if ($today == $updatedDay) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '已经认证成功']);
            }
        }

        $result = LimuService::queryCreditCardBill($user->mobile, $username, $password, $loginType);
        if ($result) {
            // 受理成功
            if ($result->code == LimuService::API_ACCEPT_SUCCESS_CODE) {
                UserLimuModel::addUserLimu($user->id, $user->mobile, UserLimuModel::STATE_BUSY, $result->token, UserLimuModel::TYPE_BILL);  // 添加数据至数据库
                $count = 200;
                $sleepCount = 3;
                do {
                    $result = LimuService::commonStatusGet($result->token, LimuService::API_BIZ_BILL); // 查询状态
                    if (LimuService::API_WAIT_INPUT_CODE == $result->code) { // 等待输入验证码
                        Yii::info('立木征信信用卡账单查询提示输入验证码:token->' . $result->token, 'limu');
                        return Json::encode([
                            'status' => self::STATUS_SUCCESS,
                            'error_message' => $result->code,
                            "results" => [['token' => $result->token, 'input'=>'data:image/jpg;base64,' . $result->input->value]]
                        ]);
                        break;
                    } elseif (LimuService::API_SUCCESS_CODE == $result->code) { // 成功，无需输入验证码
                        Yii::info('立木征信信用卡账单查询成功，退出轮询:token->' . $result->token, 'limu');
                        return Json::encode([
                            'status' => self::STATUS_SUCCESS,
                            'error_message' => '认证成功',
                            "results" => [['token' => $result->token]]
                        ]);
                    } elseif (LimuService::API_ACCEPT_SUCCESS_CODE == $result->code) { // 受理成功，无需输入验证码
                        Yii::info('立木征信学历查询登入成功，退出轮询:token->' . $result->token, 'limu');
                        return Json::encode([
                            'status' => self::STATUS_SUCCESS,
                            'error_message' => $result->code,
                            "results" => [['token' => $result->token]]
                        ]);
                    } elseif (!empty($result->code) && !Tool::startWith($result->code, '0')) { // 不是以0开头的，视为错误，退出轮询

                        return Json::encode([
                            'status' => self::STATUS_FAILURE,
                            'error_message' => $result->msg . '[' . $result->code . ']',
                        ]);
                        break;
                    }
                    sleep($sleepCount);
                    if (($count-- <= 0) || !$result) {
                        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '获取数据失败']);
                        break;
                    }
                } while(true);
            } elseif (!empty($result->code) && !Tool::startWith($result->code, '0')) { // 不是以0开头的，视为错误，退出轮询

                return Json::encode([
                    'status' => self::STATUS_FAILURE,
                    'error_message' => $result->msg . '[' . $result->code . ']',
                ]);
            }
        }
        return Json::encode([
            'status' => self::STATUS_FAILURE,
            'error_message' => $result->msg . '[' . $result->code . ']',
            "results" => [['token' => $result->token]]
        ]);
    }

    // 信用卡账单后续轮询
    public function actionLimuCreditCardBillContinue()
    {
        ini_set('max_execution_time','1600');

        $request = Yii::$app->request;
        $token = $request->post('token', '');

        $count = 200;
        $sleepCount = 3;
        do {
            $result = LimuService::commonStatusGet($token, LimuService::API_BIZ_BILL);
            if (LimuService::API_ACCEPT_SUCCESS_CODE == $result->code || LimuService::API_LOGIN_SUCCESS_CODE == $result->code) {
                Yii::info('立木征信信用卡账单查询登入成功，轮询结束，轮询次数->'.$count.':token->' . $token, 'limu');
                return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => $result->code]);
                break;
            } elseif (LimuService::API_WAIT_INPUT_CODE == $result->code) { // 等待输入验证码
                Yii::info('立木征信信用卡账单查询提示输入验证码:token->' . $result->token, 'limu');
                return Json::encode([
                    'status' => self::STATUS_SUCCESS,
                    'error_message' => $result->code,
                    "results" => [['token' => $result->token, 'input'=>'data:image/jpg;base64,' . $result->input->value]]
                ]);
                break;
            } elseif (LimuService::API_SUCCESS_CODE == $result->code) { // 成功，无需输入验证码
                Yii::info('立木征信信用卡账单查询成功，退出轮询:token->' . $result->token, 'limu');
                return Json::encode([
                    'status' => self::STATUS_SUCCESS,
                    'error_message' => '认证成功',
                    "results" => [['token' => $result->token]]
                ]);
            } elseif (!empty($result->code) && !Tool::startWith($result->code, '0')) { // 不是以0开头的，视为错误，退出轮询
                return Json::encode([
                    'status' => self::STATUS_FAILURE,
                    'error_message' => $result->msg . '[' . $result->code . ']',
                ]);
                break;
            }
            sleep($sleepCount);
            if (($count-- <= 0) || !$result) {
                Yii::info('立木征信信用卡账单查询轮询结束，轮询次数->'.$count.':token->' . $token, 'limu');
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '获取数据失败']);
                break;
            }
        } while(true);
    }

    // 网银流水查询
    public function actionLimuEbank()
    {
        ini_set('max_execution_time','600');

        $request = Yii::$app->request;
        $bank = $request->post('bank', '');
        $username = trim($request->post('username', ''));
        $password = trim($request->post('password', ''));
        if (empty($username) || empty($password) || empty($bank)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }

        $bankCode = LimuService::getBankCode($bank); // 获取银行简称
        if (empty($bankCode)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '暂不支持该银行']);
        }

        $user = Yii::$app->user->identity;
        // 判断当天是否有认证成功的记录
        $latestCredit = UserLimuModel::getLatestCredit($user->mobile, UserLimuModel::TYPE_EBANK);
        if ($latestCredit && $latestCredit->state == UserLimuModel::STATE_PASS) {
            $updatedDay = date('Y-m-d', strtotime($latestCredit->updated_at));
            $today = date('Y-m-d');
            if ($today == $updatedDay) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '已经认证成功']);
            }
        }

        $result = LimuService::queryEbank($user->mobile, $username, $password, $bankCode);
        if ($result) {
            // 受理成功
            if ($result->code == LimuService::API_ACCEPT_SUCCESS_CODE) {
                UserLimuModel::addUserLimu($user->id, $user->mobile, UserLimuModel::STATE_BUSY, $result->token, UserLimuModel::TYPE_EBANK);  // 添加数据至数据库
                $count = 200;
                $sleepCount = 3;
                do {
                    $result = LimuService::commonStatusGet($result->token, LimuService::API_BIZ_EBANK); // 查询状态
                    if (LimuService::API_WAIT_INPUT_CODE == $result->code) { // 等待输入验证码
                        Yii::info('立木征信网银流水查询提示输入验证码:token->' . $result->token, 'limu');
                        return Json::encode([
                            'status' => self::STATUS_SUCCESS,
                            'error_message' => $result->code,
                            "results" => [['token' => $result->token, 'input'=>'data:image/jpg;base64,' . $result->input->value]]
                        ]);
                        break;
                    } elseif (LimuService::API_SUCCESS_CODE == $result->code) { // 成功，无需输入验证码
                        Yii::info('立木征信网银流水查询成功，退出轮询:token->' . $result->token, 'limu');
                        return Json::encode([
                            'status' => self::STATUS_SUCCESS,
                            'error_message' => '认证成功',
                            "results" => [['token' => $result->token]]
                        ]);
                    } elseif (LimuService::API_ACCEPT_SUCCESS_CODE == $result->code) { // 受理成功，无需输入验证码
                        Yii::info('立木征信网银流水查询登入成功，退出轮询:token->' . $result->token, 'limu');
                        return Json::encode([
                            'status' => self::STATUS_SUCCESS,
                            'error_message' => $result->code,
                            "results" => [['token' => $result->token]]
                        ]);
                    } elseif (!empty($result->code) && !Tool::startWith($result->code, '0')) { // 不是以0开头的，视为错误，退出轮询

                        return Json::encode([
                            'status' => self::STATUS_FAILURE,
                            'error_message' => $result->msg . '[' . $result->code . ']',
                        ]);
                        break;
                    }
                    sleep($sleepCount);
                    if (($count-- <= 0) || !$result) {
                        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '获取数据失败']);
                        break;
                    }
                } while(true);
            } elseif (!empty($result->code) && !Tool::startWith($result->code, '0')) { // 不是以0开头的，视为错误，退出轮询

                return Json::encode([
                    'status' => self::STATUS_FAILURE,
                    'error_message' => $result->msg . '[' . $result->code . ']',
                ]);
            }
        }
        return Json::encode([
            'status' => self::STATUS_FAILURE,
            'error_message' => $result->msg . '[' . $result->code . ']',
            "results" => [['token' => $result->token]]
        ]);
    }
}