<?php

namespace api\controllers;

use api\bases\ApiController;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use Yii;

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
                'actions' => [''],
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

    public function actionRedisTest()
    {
        $key = 'hello';
        if (!Yii::$app->redis->exists($key)) {
            $result = (bool) Yii::$app->redis->set($key, 'world');
            Yii::$app->redis->expire($key, 10);
            exit('end');
        }
    }
}