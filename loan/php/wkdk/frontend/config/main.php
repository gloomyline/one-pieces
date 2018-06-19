<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'MOBILE',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => false,
            'enableSession' =>false,
            'loginUrl' => null
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'wkdk-visitor',
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
                    'levels' => ['error', 'warning', 'info'],
                    'categories' => ['limu'],
                    'logVars' => ['_GET', '_POST'],
                    'logFile' => '@frontend/runtime/logs/limu.log',
                    'maxFileSize' => 51200, // 50M
                    'maxLogFiles' => 10
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'categories' => ['lianlianpay'],
                    'logVars' => ['_GET', '_POST'],
                    'logFile' => '@frontend/runtime/logs/lianlianpay.log',
                    'maxFileSize' => 51200, // 50M
                    'maxLogFiles' => 10,
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'categories' => ['ding_talk_im'],
                    'logVars' => ['_GET', '_POST'],
                    'logFile' => '@frontend/runtime/logs/ding_talk_im.log',
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
            'showScriptName' => false,
            'rules' => [
                ##### 用户相关API #######################

                ##### 首页相关API #######################
                # 首页
                [
                    'pattern' => 'home',
                    'route' => 'site/home',
                    'verb' => 'GET'
                ],
                ##### 借款相关API #######################
                # 借款确认
                [
                    'pattern' => 'loan-confirm',
                    'route' => 'loan/confirm',
                    'verb' => 'POST'
                ],
                # 提交确认借款
                [
                    'pattern' => 'loan-submit',
                    'route' => 'loan/submit',
                    'verb' => 'POST'
                ],
                # 借款记录
                [
                    'pattern' => 'loan-record',
                    'route' => 'loan/loan-record',
                    'verb' => 'GET'
                ],
                # 还款记录
                [
                    'pattern' => 'repayments-record',
                    'route' => 'loan/repayment-record',
                    'verb' => 'GET'
                ],
                # 借款详情
                [
                    'pattern' => 'loan-record-detail',
                    'route' => 'loan/record-detail',
                    'verb' => 'GET'
                ],
                ##### 通用功能API #######################
                # 用户注册
                [
                    'pattern' => 'signup',
                    'route' => 'site/signup',
                    'verb' => 'POST'
                ],
                # 用户登入
                [
                    'pattern' => 'login',
                    'route' => 'site/login',
                    'verb' => 'POST'
                ],
                # 退出登入
                [
                    'pattern' => 'logout',
                    'route' => 'site/logout',
                    'verb' => 'GET'
                ],
                # 意见反馈
                [
                    'pattern' => 'feedback',
                    'route' => 'site/feedback',
                    'verb' => 'POST'
                ],
                # 修改密码
                [
                    'pattern' => 'password',
                    'route' => 'user/password',
                    'verb' => 'PUT'
                ],
                # 忘记密码
                [
                    'pattern' => 'forget-password',
                    'route' => 'user/forget-password',
                    'verb' => 'PUT'
                ],
                # 代金券 @wang
                [
                    'pattern' => 'cash-coupon-get',
                    'route' => 'cash-coupon/cash-coupon-get',
                    'verb' => 'GET'
                ],
                # 借款合同
                [
                    'pattern' => 'loan-contract',
                    'route' => 'site/loan-contract',
                    'verb' => 'GET'
                ],
                ##### 认证功能API #######################
                # 身份认证
                [
                    'pattern' => 'lm-identity',
                    'route' => 'limu/limu-identity',
                    'verb' => 'POST'
                ],
                # 获取身份认证信息
                [
                    'pattern' => 'lm-get-identity',
                    'route' => 'limu/limu-get-identity',
                    'verb' => 'GET'
                ],
                # 手机认证(获取运营商报告)
                [
                    'pattern' => 'lm-mobile',
                    'route' => 'limu/limu-mobile-report-task',
                    'verb' => 'POST'
                ],
                # 手机认证验证码(获取运营商报告)
                [
                    'pattern' => 'lm-mobile-input',
                    'route' => 'limu/limu-mobile-report-input',
                    'verb' => 'POST'
                ],
                # 手机认证后续轮询(获取运营商报告)
                [
                    'pattern' => 'lm-mobile-continue',
                    'route' => 'limu/limu-mobile-report-continue',
                    'verb' => 'POST'
                ],
                # 京东认证
                [
                    'pattern' => 'lm-jd',
                    'route' => 'limu/limu-jd-auth',
                    'verb' => 'POST'
                ],
                # 京东认证验证码
                [
                    'pattern' => 'lm-jd-input',
                    'route' => 'limu/limu-jd-auth-input',
                    'verb' => 'POST'
                ],
                # 京东认证后续轮询
                [
                    'pattern' => 'lm-jd-continue',
                    'route' => 'limu/limu-jd-auth-continue',
                    'verb' => 'POST'
                ],
                # 淘宝认证
                [
                    'pattern' => 'lm-taobao',
                    'route' => 'limu/limu-taobao-auth',
                    'verb' => 'POST'
                ],
                # 淘宝认证后续轮询
                [
                    'pattern' => 'lm-taobao-continue',
                    'route' => 'limu/limu-taobao-auth-continue',
                    'verb' => 'POST'
                ],
                # 学历认证
                [
                    'pattern' => 'lm-education',
                    'route' => 'limu/limu-edu-auth',
                    'verb' => 'POST'
                ],
                # 信用卡账单
                [
                    'pattern' => 'lm-bill',
                    'route' => 'limu/limu-credit-card-bill',
                    'verb' => 'POST'
                ],
                # 信用卡账单后续轮询
                [
                    'pattern' => 'lm-bill-continue',
                    'route' => 'limu/limu-credit-card-bill-continue',
                    'verb' => 'POST'
                ],
                # 网银流水
                [
                    'pattern' => 'lm-ebank',
                    'route' => 'limu/limu-ebank',
                    'verb' => 'POST'
                ],
                ##### 认证中心 - 个人信息 API @wang #######################
                # 获取认证状态
                [
                    'pattern' => 'user-auth-state',
                    'route' => 'user/get-auth-state',
                    'verb' => 'GET'
                ],
                # 获取个人信息
                [
                    'pattern' => 'user-profile',
                    'route' => 'user/profile',
                    'verb' => 'GET'
                ],
                # 保存个人信息（个人信息认证）
                [
                    'pattern' => 'user-save-profile',
                    'route' => 'user/save-profile',
                    'verb' => 'POST'
                ],
                # 获取工作信息
                [
                    'pattern' => 'user-work',
                    'route' => 'user/work',
                    'verb' => 'GET'
                ],
                # 保存工作信息（工作信息认证）
                [
                    'pattern' => 'user-save-work',
                    'route' => 'user/save-work',
                    'verb' => 'POST'
                ],
                # 获取人际关系
                [
                    'pattern' => 'user-relation',
                    'route' => 'user/relation',
                    'verb' => 'GET'
                ],
                # 保存人际关系（人际关系认证）
                [
                    'pattern' => 'user-save-relation',
                    'route' => 'user/save-relation',
                    'verb' => 'POST'
                ],# end add by @wang

                ##### 连连支付相关API #######################
                # 绑卡获取参数
                [
                    'pattern' => 'llpay-sign',
                    'route' => 'payment/lianlianpay-sign',
                    'verb' => 'GET'
                ],
                # 绑卡回调地址
                [
                    'pattern' => 'llpay-sign-return',
                    'route' => 'payment/lianlianpay-sign-return',
                    'verb' => 'GET'
                ],
                # 用户主动还款（连连认证支付）
                [
                    'pattern' => 'llpay-auth-pay',
                    'route' => 'payment/lianlian-auth-pay',
                    'verb' => 'POST'
                ],
                # 连连认证支付回显
                [
                    'pattern' => 'llpay-auth-url-return',
                    'route' => 'payment/lianlian-auth-pay-url-return',
                    'verb' => 'POST'
                ],
                # 连连cardbin查询
                [
                    'pattern' => 'llpay-cardbin',
                    'route' => 'payment/lianlianpay-cardbin',
                    'verb' => 'GET'
                ],
                
                ##### 认证中心 - 提升额度 API @wang #######################
                # 更多认证（QQ、微信、银行卡）
                [
                    'pattern' => 'auth-common',
                    'route' => 'auth/common-auth',
                    'verb' => 'POST'
                ],
                 # 通用认证查询（QQ、微信、银行卡、淘宝、京东、信用卡账单、网银流水）
                [
                    'pattern' => 'auth-common-get',
                    'route' => 'auth/get-common-auth',
                    'verb' => 'GET'
                ],
                # 查询用户已认证银行卡信息
                [
                    'pattern' => 'auth-bankcard-get',
                    'route' => 'auth/get-bankcard',
                    'verb' => 'GET'
                ],
                # 设置默认银行卡
                [
                    'pattern' => 'set-bankcard-default',
                    'route' => 'auth/set-bankcard-default',
                    'verb' => 'PUT'
                ],
                # 提升额度
                [
                    'pattern' => 'quota-apply',
                    'route' => 'auth/quota-apply',
                    'verb' => 'GET'
                ],
            ],
        ],
    ],
    'params' => $params,
];
