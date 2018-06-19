<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-shop',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'shop\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-shop',
        ],
        'user' => [
            'identityClass' => 'common\models\ShopAdmin',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-shop', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'wkdk-shop',
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
                    'levels' => ['info', 'error', 'warning'],
                    'categories' => ['lianlianpay'],
                    'logVars' => ['_GET', '_POST'],
                    'logFile' => '@backend/runtime/logs/lianlianpay.log',
                    'maxFileSize' => 51200, // 50M
                    'maxLogFiles' => 10,
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'categories' => ['ding_talk_im'],
                    'logVars' => ['_GET', '_POST'],
                    'logFile' => '@backend/runtime/logs/ding_talk_im.log',
                    'maxFileSize' => 51200, // 50M
                    'maxLogFiles' => 10
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        // 'authManager' => [
        //     'class' => 'yii\rbac\DbManager',
        // ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                ##### 商品管理 #############
                # 添加修改商品所需要的数据
                [
                    'pattern' => 'need',
                    'route' => 'product/need',
                    'verb' => 'GET'
                ],
                # 添加商品
                [
                    'pattern' => 'add-pro',
                    'route' => 'product/add',
                    'verb' => 'POST'
                ],
                # 编辑商品
                [
                   'pattern' => 'update-pro/<id:\d+>',
                   'route' => 'product/update',
                   'verb' => 'PUT'
                ],
                # 图片上传
                [
                    'pattern' => 'upload',
                    'route' => 'site/upload',
                    'verb' => 'POST'
                ],
                # 七牛图片上传
                [
                    'pattern' => 'qiniu-upload/<q:\w+>',
                    'route' => 'site/qiniu-upload',
                    'verb' => 'POST'
                ],
                # 编辑器图片上传七牛
                [
                    'pattern' => 'qiniu',
                    'route' => 'site/qiniu',
                    'verb' => 'POST'
                ],
                # 商品详情
                [
                    'pattern' => 'detail/<id:\d+>',
                    'route' => 'product/detail',
                    'verb' => 'GET'
                ],
                # 添加商品规格
                [
                    'pattern' => 'add-spec',
                    'route' => 'product/add-spec',
                    'verb' => 'POST'
                ],
                # 编辑商品规格
                [
                    'pattern' => 'update-spec/<spec_id:\d+>',
                    'route' => 'product/update-spec',
                    'verb' => 'PUT'
                ],
                # 删除商品规格
                [
                    'pattern' => 'del-spec',
                    'route' => 'product/del-spec',
                    'verb' => 'POST'
                ],
                # 商品列表
                [
                    'pattern' => 'pro-list',
                    'route' => 'product/list',
                    'verb' => 'GET'
                ],
                # 商品审核
                [
                    'pattern' => 'pro-audit/<id:\d+>',
                    'route' => 'product/audit',
                    'verb' => 'PUT',
                ],
                # 商品上下架
                [
                    'pattern' => 'pro-on-sale/<id:\d+>',
                    'route' => 'product/on-sale',
                    'verb' => 'PUT'
                ],
                ###### 商户管理 ########
                # 商户相关详情
                [
                    'pattern' => 'shop-detail',
                    'route' => 'shop/detail',
                    'verb' => 'GET'
                ],
                # 店铺管理
                [
                    'pattern' => 'decorate',
                    'route' => 'shop/decorate',
                    'verb' => 'PUT'
                ],
                # 修改密码
                [
                    'pattern' => 'pwd',
                    'route' => 'shop/password',
                    'verb' => 'PUT',
                ],
                # 商户概况
                [
                    'pattern' => 'index',
                    'route' => 'shop/index',
                    'verb' => 'GET'
                ],
                ##### 商户订单管理 ########
                # 商户订单列表
                [
                    'pattern' => 'order-list',
                    'route' => 'order/list',
                    'verb' => 'GET'
                ],
                # 商户确认订单
                [
                    'pattern' => 'confirm-order/<loan_id:\d+>',
                    'route' => 'order/confirm-order',
                    'verb' => 'POST'
                ],
                # 商户确认收货收货
                [
                    'pattern' => 'receiving/<loan_id:\d+>',
                    'route' => 'order/receiving-confirm',
                    'verb' => 'PUT'
                ],
                # 订单详情
                [
                    'pattern' => 'order-detail/<loan_id:\d+>',
                    'route' => 'order/detail',
                    'verb' => 'GET'
                ],
            ],
        ],
    ],
    'params' => $params,
];
