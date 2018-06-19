<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/30
 * Time: 15:33
 */
namespace api\controllers;

include "../../common/extend/zmop/ZmopClient.php";
include "../../common/extend/zmop/request/ZhimaAuthInfoAuthorizeRequest.php";
include "../../common/extend/zmop/request/ZhimaCreditScoreGetRequest.php";

use api\bases\ApiController;
use common\models\UserIdentityCardModel;
use common\services\ZhimaService;
use Yii;
use yii\helpers\Json;

class ZhimaController extends ApiController
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
                'actions' => ['zhima-credit-auth-callback'],
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
     * 芝麻信用认证授权
     */
   public function actionZhimaCreditAuth()
   {
       $userIdentityCardModel = UserIdentityCardModel::getIdentityCard(Yii::$app->user->getId()); // 获取登陆就用户的身份证信息

       $realName = $userIdentityCardModel->real_name ?? ''; // 真实姓名
       $identityNo= $userIdentityCardModel->identity_no ?? ''; // 身份证号

       if (!$identityNo) { return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '用户尚未进行身份认证，请先认证后授权']); }
       if ($userIdentityCardModel->state == 'nopass') { return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '用户身份认证未通过，请重新认证身份']); }

       $zmop = new \ZmopClient(Yii::$app->params['zhimaGatewayUrl'], Yii::$app->params['zhimaCreditAppId'], Yii::$app->params['zhimaCharset'], Yii::$app->params['zhimaPrivateKeyFilePath'], Yii::$app->params['zhimaPublicKeyFilePath']);

       $zhimaAuthInfoAuthorizeRequest = new \ZhimaAuthInfoAuthorizeRequest();
       $bizParams = [
           'auth_code' => 'M_H5', // 接入方式
           'channelType' => 'app', // 渠道类型
           'state' => Yii::$app->user->getId(), // 当前登录用户ID
       ]; // 业务扩展字段
       $zhimaAuthInfoAuthorizeRequest->setBizParams(Json::encode($bizParams)); // 业务扩展字段
       $zhimaAuthInfoAuthorizeRequest->setPlatform(Yii::$app->params['zhimaPlatform']); // 来源平台，默认为zmop
       $zhimaAuthInfoAuthorizeRequest->setIdentityType(2);  // 身份标识类型， 1：按照手机号进行授权 2：按照身份证+姓名进行授权
       $identityParams = [
           'certNo' => $identityNo,
           'name' => $realName,
           'certType' => 'IDENTITY_CARD'
       ]; // 不同身份类型的传入参数列表
       $zhimaAuthInfoAuthorizeRequest->setIdentityParam(Json::encode($identityParams)); // 不同身份类型的传入参数列表
       $response = $zmop->generatePageRedirectInvokeUrl($zhimaAuthInfoAuthorizeRequest);
       return $response; // 返回处理后的结果
   }

    /**
     * 获取芝麻信用分数
     */
    public function actionZhimaCreditScoreGet()
    {
        // 通过商户标识 获取 芝麻会员在商户端的唯一行标志
        $zmop = new \ZmopClient(Yii::$app->params['zhimaGatewayUrl'], Yii::$app->params['zhimaCreditAppId'], Yii::$app->params['zhimaCharset'], Yii::$app->params['zhimaPrivateKeyFilePath'], Yii::$app->params['zhimaPublicKeyFilePath']);

        $zhimaCreditScoreRequest = new \ZhimaCreditScoreGetRequest(); //

        $zhimaCreditScoreRequest->setPlatform(Yii::$app->params['zhimaPlatform']);
        $zhimaCreditScoreRequest->setTransactionId(ZhimaService::BuildTransactionId(Yii::$app->user->getId())); // 业务流水号
        $zhimaCreditScoreRequest->setProductCode(Yii::$app->params['zhimaProductCode']); // 产品码
        $zhimaCreditScoreRequest->setOpenId('268810000007909449496');

        $response = $zmop->execute($zhimaCreditScoreRequest);
        var_dump($response);exit;

    }

    /**
     * 芝麻信用授权回调
     */
    public function actionZhimaCreditAuthCallback()
    {
        echo 'SUCCESS';
//        $request = Yii::$app->request;
//        $params = trim($request->get('params')); // 从回调URL中获取params参数
//        $sign = trim($request->get('sign')); // 从回调URL中获取sign参数
//
//        $params = strstr($params, '%') ? urldecode($params) : $params; // 判断是否含有 % ，有则需要urldecode
//        $sign = strstr($sign, '%') ? urldecode($sign) : $sign; // 判断是否含有 % ，有则需要urldecode
//
//        $zmop = new \ZmopClient(Yii::$app->params['zhimaGatewayUrl'], Yii::$app->params['zhimaCreditAppId'], Yii::$app->params['zhimaCharset'], Yii::$app->params['zhimaPrivateKeyFilePath'], Yii::$app->params['zhimaPublicKeyFilePath']);
//        $result = $zmop->decryptAndVerifySign($params, $sign); // 返回解密后的字符串
//        echo $result; // 将字符串中的 openid 以及 state中的用户Id 存入表中
//        Yii::error($result);

    }
}
