<?php

namespace api\controllers;

use api\bases\ApiController;
use common\bases\CommonService;
use common\config\SmsConfig;
use common\extend\sms\AlidayuSms;
use common\extend\sms\MsgTemplate;
use common\extend\Tool;
use common\models\MobileLogModel;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use Yii;

/**
 * Site Controller
 */
class SiteController extends ApiController
{
    /**
     * 绑定访问控制过滤器
     *
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        // 访问控制
        $behaviors['access']['rules'] = [
            // 允许访客用户访问的Action
            [
                'actions' => ['index', 'send-mobile-code-new-user', 'send-mobile-code-forget-password', 'check-mobile-code',
                ],
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
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    

    /**
     * 用户注册:请求验证码
     *
     * @return json
     */
    public function actionSendMobileCodeNewUser()
    {
        $request = Yii::$app->request;
        $mobile = trim($request->post('mobile'));
        $code = Tool::getRandomNum(6);
        $service = new CommonService();
        $type = MobileLogModel::TYPE_AUTHENTICATION_CODE; // 短信验证码
        $result = $service->sendSmsCode(AlidayuSms::Sign, MsgTemplate::SIGN_UP, $mobile, ['code' => $code], $type);
        if ($result['code'] == '1000') {
            return Json::encode([
                'status' => self::STATUS_SUCCESS,
                'error_message' => $result['message']
            ]);
        } else {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                'error_message' => $result['message'],
            ]);
        }
    }

    /**
     * 忘记密码:请求验证码
     *
     * @return json
     */
    public function actionSendMobileCodeForgetPassword()
    {
        $request = Yii::$app->request;
        $mobile = trim($request->post('mobile'));
        $code = Tool::getRandomNum(6);
        $service = new CommonService();
        $type = MobileLogModel::TYPE_AUTHENTICATION_CODE; // 短信验证码
        $result = $service->sendSmsCode(AlidayuSms::Sign, MsgTemplate::FORGET_PASSWORD, $mobile, ['code' => $code], $type);
        if ($result['code'] == '1000') {
            return Json::encode([
                'status' => self::STATUS_SUCCESS,
                'error_message' => $result['message']
            ]);
        } else {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                'error_message' => $result['message'],
            ]);
        }
    }
}