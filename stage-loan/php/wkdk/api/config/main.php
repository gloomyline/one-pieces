<?php

use yii\web\GroupUrlRule;

$params = array_merge(
        require(__DIR__ . '/../../common/config/params.php'),
        require(__DIR__ . '/../../common/config/params-local.php'),
        require(__DIR__ . '/params.php'),
        require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'API',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'api\controllers',
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => false,
            'enableSession' =>false,
            'loginUrl' => null
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'categories' => ['limu'],
                    'logVars' => ['_GET', '_POST'],
                    'logFile' => '@api/runtime/logs/limu.log',
                    'maxFileSize' => 51200, // 50M
                    'maxLogFiles' => 10
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'categories' => ['sms'],
                    'logVars' => ['_GET', '_POST'],
                    'logFile' => '@api/runtime/logs/sms.log',
                    'maxFileSize' => 51200, // 50M
                    'maxLogFiles' => 10
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info', 'error', 'warning'],
                    'categories' => ['lianlianpay'],
                    'logVars' => ['_GET', '_POST'],
                    'logFile' => '@api/runtime/logs/lianlianpay.log',
                    'maxFileSize' => 51200, // 50M
                    'maxLogFiles' => 10,
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'categories' => ['ding_talk_im'],
                    'logVars' => ['_GET', '_POST'],
                    'logFile' => '@api/runtime/logs/ding_talk_im.log',
                    'maxFileSize' => 51200, // 50M
                    'maxLogFiles' => 10
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'rules' => [
                new GroupUrlRule([
                    'prefix' => '1',
                    'routePrefix' => '',
                    'rules' => [
                        ##### debug相关API #######################
                        # redis test
                        [
                            'pattern' => 'redis-test',
                            'route' => 'happy/redis-test',
                            'verb' => 'GET'
                        ],
                        # qiniu upload test
                        [
                            'pattern' => 'qiniu-upload',
                            'route' => 'happy/qiniu-upload',
                            'verb' => 'POST'
                        ],
                        # xunsearch
                        [
                            'pattern' => 'search',
                            'route' => 'happy/search',
                            'verb' => 'GET'
                        ],
                        # AntiFraud
                        [
                            'pattern' => 'anti-fraud',
                            'route' => 'happy/anti-fraud',
                            'verb' => 'POST'
                        ],
                        ##### 通用功能相关API  #######################
                        # 用户注册：请求动态密码
                        [
                            'pattern' => 'send-mobile-code/new-user',
                            'route' => 'site/send-mobile-code-new-user',
                            'verb' => 'POST'
                        ],
                        # 忘记密码：请求验证码
                        [
                            'pattern' => 'send-mobile-code/forget-password',
                            'route' => 'site/send-mobile-code-forget-password',
                            'verb' => 'POST'
                        ],
                        ##### 立木征信功能相关API  #######################
                        # 立木回调
                        [
                            'pattern' => 'lm-callback',
                            'route' => 'limu/limu-callback',
                            'verb' => 'POST'
                        ],
                        ##### 芝麻信用功能相关API  #######################
                        # 芝麻信用评分回调
                        [
                            'pattern' => 'zhima-callback',
                            'route' => 'zhima/zhima-credit-auth-callback',
                            'verb' => 'GET'
                        ],
                        # 芝麻信用认证授权
                        [
                            'pattern' => 'zhima-auth',
                            'route' => 'zhima/zhima-credit-auth',
                            'verb' => 'GET'
                        ],
                        # 获取芝麻信用评分
                        [
                            'pattern' => 'zhima-credit-score',
                            'route' => 'zhima/zhima-credit-score-get',
                            'verb' => 'GET'
                        ],
                        ##### 连连支付功能相关API  #######################
                        # 实时支付（平台放款）通知回调地址
                        [
                            'pattern' => 'llpay-payment-notify',
                            'route' => 'payment/lianlianpay-notify',
                            'verb' => 'POST'
                        ],
                        # 认证支付支付结果通知接口
                        [
                            'pattern' => 'llpay-auth-pay-notify',
                            'route' => 'payment/lianlian-auth-pay-notify',
                            'verb' => 'POST'
                        ],
                        # 分期支付银行卡还款代扣结果通知接口
                        [
                            'pattern' => 'llpay-bankcard-repayment-notify',
                            'route' => 'payment/lianlian-bankcard-repayment-notify',
                            'verb' => 'POST'
                        ],
                    ],
                ]),
            ]
        ],
    ],
    'params' => $params,
];
