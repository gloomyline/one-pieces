<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'language' =>'zh-CN',//切换语言包
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                ##### 通用相关API #######################
                # 主页
                [
                    'pattern' => 'index',
                    'route' => 'site/index',
                    'verb' => 'GET'
                ],
                # 解决方案
                [
                    'pattern' => 'resolution',
                    'route' => 'site/resolution',
                    'verb' => 'GET'
                ],
                # 案例中心
                [
                    'pattern' => 'example',
                    'route' => 'site/example',
                    'verb' => 'GET'
                ],
                # 关于我们
                [
                    'pattern' => 'about-us',
                    'route' => 'site/about-us',
                    'verb' => 'GET'
                ],
                # 新闻中心
                [
                    'pattern' => 'news',
                    'route' => 'site/news',
                    'verb' => 'GET',
                ],
                # 产品中心
                [
                    'pattern' => 'product',
                    'route' => 'product/index',
                    'verb' => 'GET',
                ]
            ],
        ],

    ],
    'params' => $params,
];
