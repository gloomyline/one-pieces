<?php

namespace api\controllers;

use api\bases\ApiController;
use common\models\UserLimuModel;
use common\services\LimuService;
use common\services\UserService;
use yii\helpers\Json;
use Yii;

/**
 * Limu Controller
 */
class LimuController extends ApiController
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
                'actions' => ['limu-callback'],
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

    

    /**
     * 立木征信回调
     * @param string $bizType 业务类型
     * @param string $code 状态码
     * @param string $msg 状态 述
     * @param string $token 流程唯一标 记
     *
     * @return json
     */
    public function actionLimuCallback()
    {
        $result = false;
        $request = Yii::$app->request;
        $bizType = trim($request->post('bizType'));
        $code = trim($request->post('code'));
        $msg = trim($request->post('msg'));
        $token = trim($request->post('token'));

        if ($code == LimuService::API_SUCCESS_CODE) {
            switch ($bizType) {
                //手机运营商
                case LimuService::API_BIZ_MOBILE:
                    $jsonResult = LimuService::commonResultGet($token, $bizType);
                    $jsonObj = json_decode($jsonResult);
                    if ($jsonObj->code == LimuService::API_SUCCESS_CODE) {
                        $result = UserService::setUserMobileAuth($token, $jsonResult);
                    } else {
                        Yii::error("\r\n" . '立木回调失败（手机运营商）:' . "\r\n" . 'code:' . $jsonObj->code . "\r\n" . $jsonObj->msg, 'limu');

                        $im = Yii::$app->companyim;
                        $im->sendNotice('sys_error', $im::TECH_DEPT_CHAT_ID, '立木回调失败（手机运营商）:' . "\r\n" . 'code:' . $jsonObj->code . "\r\n" . $jsonObj->msg);
                    }
                    break;
                case LimuService::API_BIZ_MOBILE_REPORT:
                    $ReportContent = LimuService::mobileReportGet($token); // 运营商报告结果查询
                    $DataContent = LimuService::mobileReportBaseDataGet($token); // 运营商报告原始数据查询

                    if ($ReportContent->code == LimuService::API_SUCCESS_CODE && $DataContent->code == LimuService::API_SUCCESS_CODE) {
                        $result = UserService::setUserMobileReportAuth($token, $ReportContent->data, $DataContent->data);
                    } else {
                        Yii::error("\r\n" . '立木征信报告回调获取运营商报告结果（手机运营商）:' . "\r\n" . 'code:' . $ReportContent->code . "\r\n" . $ReportContent->msg, 'limu');
                        Yii::error("\r\n" . '立木征信报告回调获取运营商原始数据结果（手机运营商）:' . "\r\n" . 'code:' . $DataContent->code . "\r\n" . $DataContent->msg, 'limu');

                        $im = Yii::$app->companyim;
                        $im->sendNotice('sys_error', $im::TECH_DEPT_CHAT_ID, '立木征信报告回调获取运营商报告结果（手机运营商）:' . "\r\n" . 'code:' . $ReportContent->code . "\r\n" . $ReportContent->msg);
                        $im->sendNotice('sys_error', $im::TECH_DEPT_CHAT_ID, '立木征信报告回调获取运营商原始数据结果（手机运营商）:' . "\r\n" . 'code:' . $DataContent->code . "\r\n" . $DataContent->msg);
                    }

                    break;
                //京东查询回调
                case LimuService::API_BIZ_JD:
                    $jsonResult = LimuService::commonResultGet($token, $bizType);
                    $jsonObj = json_decode($jsonResult);
                    if ($jsonObj->code == LimuService::API_SUCCESS_CODE) {
                        $result = UserService::setUserLimuAuth($token, $jsonObj->data, UserLimuModel::TYPE_JD);
                    } else {
                        Yii::error("\r\n" . '立木回调失败（京东查询）:' . "\r\n" . 'code:' . $jsonObj->code . "\r\n" . $jsonObj->msg, 'limu');

                        $im = Yii::$app->companyim;
                        $im->sendNotice('sys_error', $im::TECH_DEPT_CHAT_ID, '立木回调失败（京东查询）:' . "\r\n" . 'code:' . $jsonObj->code . "\r\n" . $jsonObj->msg);
                    }
                    break;
                //淘宝查询回调
                case LimuService::API_BIZ_TAOBAO:
                    $jsonResult = LimuService::commonResultGet($token, $bizType);
                    $jsonObj = json_decode($jsonResult);
                    if ($jsonObj->code == LimuService::API_SUCCESS_CODE) {
                        $result = UserService::setUserLimuAuth($token, $jsonObj->data, UserLimuModel::TYPE_TAOBAO);
                    } else {
                        Yii::error("\r\n" . '立木回调失败（淘宝查询）:' . "\r\n" . 'code:' . $jsonObj->code . "\r\n" . $jsonObj->msg, 'limu');

                        $im = Yii::$app->companyim;
                        $im->sendNotice('sys_error', $im::TECH_DEPT_CHAT_ID, '立木回调失败（淘宝查询）:' . "\r\n" . 'code:' . $jsonObj->code . "\r\n" . $jsonObj->msg);
                    }
                    break;
                //学历查询回调
                case LimuService::API_BIZ_EDUCATION:
                    $jsonResult = LimuService::commonResultGet($token, $bizType);
                    $jsonObj = json_decode($jsonResult);
                    if ($jsonObj->code == LimuService::API_SUCCESS_CODE) {
                        $result = UserService::setUserLimuAuth($token, $jsonObj->data, UserLimuModel::TYPE_EDUCATION);
                    } else {
                        Yii::error("\r\n" . '立木回调失败（学历查询）:' . "\r\n" . 'code:' . $jsonObj->code . "\r\n" . $jsonObj->msg, 'limu');

                        $im = Yii::$app->companyim;
                        $im->sendNotice('sys_error', $im::TECH_DEPT_CHAT_ID, '立木回调失败（学历查询）:' . "\r\n" . 'code:' . $jsonObj->code . "\r\n" . $jsonObj->msg);
                    }
                    break;
                //信用卡账单查询回调
                case LimuService::API_BIZ_BILL:
                    $jsonResult = LimuService::commonResultGet($token, $bizType);
                    $jsonObj = json_decode($jsonResult);
                    if ($jsonObj->code == LimuService::API_SUCCESS_CODE) {
                        $result = UserService::setUserLimuAuth($token, $jsonObj->data, UserLimuModel::TYPE_BILL);
                    } else {
                        Yii::error("\r\n" . '立木回调失败（信用卡账单查询）:' . "\r\n" . 'code:' . $jsonObj->code . "\r\n" . $jsonObj->msg, 'limu');

                        $im = Yii::$app->companyim;
                        $im->sendNotice('sys_error', $im::TECH_DEPT_CHAT_ID, '立木回调失败（信用卡账单查询）:' . "\r\n" . 'code:' . $jsonObj->code . "\r\n" . $jsonObj->msg);
                    }
                    break;
                //网银流水查询回调
                case LimuService::API_BIZ_EBANK:
                    $jsonResult = LimuService::commonResultGet($token, $bizType);
                    $jsonObj = json_decode($jsonResult);
                    if ($jsonObj->code == LimuService::API_SUCCESS_CODE) {
                        $result = UserService::setUserLimuAuth($token, $jsonObj->data, UserLimuModel::TYPE_EBANK);
                    } else {
                        Yii::error("\r\n" . '立木回调失败（网银流水查询）:' . "\r\n" . 'code:' . $jsonObj->code . "\r\n" . $jsonObj->msg, 'limu');

                        $im = Yii::$app->companyim;
                        $im->sendNotice('sys_error', $im::TECH_DEPT_CHAT_ID, '立木回调失败（网银流水查询）:' . "\r\n" . 'code:' . $jsonObj->code . "\r\n" . $jsonObj->msg);
                    }
                    break;
                default:
                    break;
            }
        }
        
        if ($result) {
            exit('success');
        } else {
            Yii::warning("\r\n" . '立木回调失败:' . "\r\n" . 'bizType:' . $bizType . '&code:' . $code . '&msg:' . $msg . '&token:' . $token, 'limu');

            $im = Yii::$app->companyim;
            $im->sendNotice('sys_error', $im::TECH_DEPT_CHAT_ID, '立木回调失败:' . "\r\n" . 'bizType:' . $bizType . '&code:' . $code . '&msg:' . $msg . '&token:' . $token);
            exit('failure');
        }
    }
}