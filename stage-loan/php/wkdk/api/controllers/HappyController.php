<?php

namespace api\controllers;

use api\bases\ApiController;
use common\models\RiskRule;
use common\models\RiskRuleModel;
use common\services\RuleService;
use common\services\QiniuService;
use common\services\SearchService;
use common\services\TencentService;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use Yii;
use Qiniu;
/**
 * Debug Controller
 */
class HappyController extends ApiController
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
                'actions' => ['qiniu-upload', 'search', 'anti-fraud'],
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

    public function actionQiniuUpload()
    {
        $result = QiniuService::qiniuImageUpload(QiniuService::BUCKET_FENQI);
        if ($result['code'] == QiniuService::STATUS_SUCCESS) {
            return Json::encode([
                'status' => self::STATUS_SUCCESS,
                'error_message' => $result['url'],
            ]);
        } else {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                'error_message' => $result['message'],
            ]);
        }
    }

    public function actionRuleTest()
    {
        $userId = Yii::$app->user->identity->id;
        $rules = RiskRule::find()->where(['state' => RiskRuleModel::STATE_ENABLE])->all();
        $ruleService = new RuleService($userId);
        foreach ($rules as $rule) {
            $ruleService->rule = $rule;
            $func = $rule->item;
            $pattern = $rule->pattern;
            if ($func != 'idCardVerify') continue;
            $ruleService->$func($pattern);
        }
    }

    public function actionSearch()
    {
        //SearchService::createShopDoc(2, '厦门时尚潮牌店');
        $ids = SearchService::searchShopDoc('');
        var_dump($ids);
    }

    public function actionAntiFraud()
    {
        $request = Yii::$app->request;
        $region = $request->post('region', 'gz');
        $idno = $request->post('idno', '');
        $mobile = $request->post('mobile', '');
        $params = ['idNumber' => $idno, 'phoneNumber' => $mobile];

        $url = TencentService::makeURL('GET', 'AntiFraud', $region, TencentService::SECRET_ID, TencentService::SECRET_KEY, $params);
        $result = TencentService::sendRequest($url);

        var_dump($result);
    }
}