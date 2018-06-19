<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php'),
    require(__DIR__ . '/../../common/config/CreditConfig.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'backend\models\Admin',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'wkdk-backend',
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
                ##### 权限管理相关API #######################
                # 管理员列表
                [
                    'pattern' => 'admins',
                    'route' => 'admin/index',
                    'verb' => 'GET'
                ],
                # 管理员详情
                [
                    'pattern' => 'admin-detail/<id:\d+>',
                    'route' => 'admin/detail',
                    'verb' => 'GET'
                ],
                # 添加管理员
                [
                    'pattern' => 'add-admin',
                    'route' => 'admin/add',
                    'verb' => 'POST'
                ],
                # 编辑管理员
                [
                    'pattern' => 'update-admin/<id:\d+>',
                    'route' => 'admin/update',
                    'verb' => 'PUT'
                ],
                # 设置离职
                [
                    'pattern' => 'set-admin-leave',
                    'route' => 'admin/set-leave',
                    'verb' => 'PUT'
                ],
                # 设置新密码
                [
                    'pattern' => 'set-admin-password',
                    'route' => 'admin/set-password',
                    'verb' => 'PUT'
                ],
                # 角色列表
                [
                    'pattern' => 'roles',
                    'route' => 'role/index',
                    'verb' => 'GET'
                ],
                # 添加角色
                [
                    'pattern' => 'add-role',
                    'route' => 'role/add',
                    'verb' => 'POST'
                ],
                # 编辑角色
                [
                    'pattern' => 'update-role/<name:\w+>',
                    'route' => 'role/update',
                    'verb' => 'PUT'
                ],
                # 角色详情
                [
                    'pattern' => 'role-detail/<name:\w+>',
                    'route' => 'role/detail',
                    'verb' => 'GET'
                ],
                # 菜单列表
                [
                    'pattern' => 'menus',
                    'route' => 'menu/index',
                    'verb' => 'GET'
                ],
                # 菜单详情
                [
                    'pattern' => 'menu-detail/<id:\d+>',
                    'route' => 'menu/detail',
                    'verb' => 'GET'
                ],
                # 添加菜单
                [
                    'pattern' => 'add-menu',
                    'route' => 'menu/add',
                    'verb' => 'POST'
                ],
                # 编辑菜单
                [
                    'pattern' => 'update-menu/<id:\d+>',
                    'route' => 'menu/update',
                    'verb' => 'PUT'
                ],
                # 权限列表
                [
                    'pattern' => 'auths',
                    'route' => 'auth/index',
                    'verb' => 'GET'
                ],
                # 添加权限
                [
                    'pattern' => 'add-auth',
                    'route' => 'auth/add',
                    'verb' => 'POST'
                ],
                # 编辑权限
                [
                    'pattern' => 'update-auth',
                    'route' => 'auth/update',
                    'verb' => 'PUT'
                ],
                # 权限详情
                [
                    'pattern' => 'auth-detail/<name:\w+>',
                    'route' => 'auth/detail',
                    'verb' => 'GET'
                ],
                # 删除权限
                [
                    'pattern' => 'delete-auth',
                    'route' => 'auth/delete',
                    'verb' => 'POST'
                ],
                # 分配权限
                [
                    'pattern' => 'assign-auth',
                    'route' => 'auth/assign',
                    'verb' => 'POST'
                ],
                # 角色权限
                [
                    'pattern' => 'role-auths',
                    'route' => 'auth/role-auths',
                    'verb' => 'GET'
                ],
                ##### 用户管理相关API #######################
                # 用户列表
                [
                    'pattern' => 'users',
                    'route' => 'user/index',
                    'verb' => 'GET'
                ],
                #启用-禁用用户
                [
                    'pattern' => 'user-forbid',
                    'route' => 'user/forbid',
                    'verb' => 'PUT'
                ],
                #用户详情管理
                [
                    'pattern' => 'user-detail/<id:\d+>',
                    'route' => 'user/detail',
                    'verb' => 'GET'
                ],
                #借还款记录
                [
                    'pattern' => 'user-loan/<id:\d+>',
                    'route' => 'user/loan',
                    'verb' => 'GET'
                ],
                # 用户征信详情
                [
                    'pattern' => 'user-credit-info/<id:\d+>',
                    'route' => 'user/credit-info',
                    'verb' => 'GET'
                ],
                ##### 系统管理相关API #######################
                # 信用分表字段
                [
                    'pattern' => 'risk',
                    'route' => 'system/risk',
                    'verb' => 'GET'
                ],
                # 信用分规则配置
                [
                    'pattern' => 'risk-rule',
                    'route' => 'system/rule',
                    'verb' => 'GET'
                ],
                # 添加信用分规则
                [
                   'pattern' => 'add-risk',
                   'route' => 'system/add-risk-rule',
                   'verb' => 'POST'
                ],
                # 编辑信用分规则
                [
                    'pattern' => 'update-risk',
                    'route' => 'system/update-risk-rule',
                    'verb' => 'PUT'
                ],
                # 信用分规则启用-禁用
                [
                    'pattern' => 'update-risk-state/<id:\d+>',
                    'route' => 'system/update-state',
                    'verb' => 'PUT'
                ],
                # 删除信用分规则
                [
                    'pattern' => 'del-risk-rule/<id:\d+>',
                    'route' => 'system/del-risk-rule',
                    'verb' => 'PUT'
                ],
                # 信用分设置列表
                [
                    'pattern' => 'credit-set',
                    'route' => 'system/credit-set',
                    'verb' => 'GET'
                ],
                # 修改信用分等级
                [
                    'pattern' => 'credit-grade-update',
                    'route' => 'system/credit-grade-update',
                    'verb' => 'PUT',
                ],
                # 修改系统自动审核放款功能
                [
                    'pattern' => 'modify-sys-auto-review',
                    'route' => 'system/modify-sys-auto-review',
                    'verb' => 'PUT'
                ],
                ##### 征信管理相关API #######################
                # 功能模块
                [
                    'pattern' => 'credits',
                    'route' => 'credit/index',
                    'verb' => 'GET'
                ],
                # 手机归属地查询
                [
                    'pattern' => 'mobile-area',
                    'route' => 'credit/mobile-area',
                    'verb' => 'POST'
                ],
                # 央行征信查询
                [
                    'pattern' => 'central-bank',
                    'route' => 'credit/central-bank',
                    'verb' => 'POST'
                ],

                ##### 产品配置管理相关API  by @wang#######################
                # 产品配置列表
                [
                    'pattern' => 'products',
                    'route' => 'product/index',
                    'verb' => 'GET'
                ],
                 # 产品配置详情
                [
                    'pattern' => 'product-detail/<id:\d+>',
                    'route' => 'product/detail',
                    'verb' => 'GET'
                ],
                 # 添加产品配置详情
                [
                    'pattern' => 'add-product',
                    'route' => 'product/add',
                    'verb' => 'POST'
                ],
                 # 编辑产品配置
                [
                    'pattern' => 'update-product/<id:\d+>',
                    'route' => 'product/update',
                    'verb' => 'PUT'
                ],
                 ##### 借款管理相关API  #######################
                # 借款列表
                [
                    'pattern' => 'borrows',
                    'route' => 'loan/index',
                    'verb' => 'GET'
                ],
                # 初审管理
                [
                    'pattern' => 'checks',
                    'route' => 'loan/preliminary',
                    'verb' => 'GET'
                ],
                # 复审管理
                [
                    'pattern' => 'reviews',
                    'route' => 'loan/reviews',
                    'verb' => 'GET'
                ],
                # 放款管理
                [
                    'pattern' => 'grant',
                    'route' => 'loan/grant',
                    'verb' => 'GET'
                ],
                # 还款管理
                [
                    'pattern' => 'repayments',
                    'route' => 'loan/repayments',
                    'verb' => 'GET'
                ],
                # 设置还款状态
                [
                    'pattern' => 'set-loan-state/<id:\d+>',
                    'route' => 'loan/set-state',
                    'verb' => 'PUT'
                ],
                # 获取借款明细
                [
                    'pattern' => 'loan-detail/<id:\d+>',
                    'route' => 'loan/detail',
                    'verb' => 'GET'
                ],
                # 放款
                [
                    'pattern' => 'lending',
                    'route' => 'payment/lianlian-payment',
                    'verb' => 'POST'
                ],# end by @wang
                ##### 认证管理API  #######################
                #认证中心
                [
                    'pattern' => 'auth-center',
                    'route' => 'ident/index',
                    'verb' => 'GET'
                ],
                #身份认证
                [
                    'pattern' => 'identity',
                    'route' => 'ident/identity-auth',
                    'verb' => 'GET'
                ],
                #手机认证
                [
                    'pattern' => 'auth-mobile',
                    'route' => 'ident/mobile-auth',
                    'verb' => 'GET'
                ],
                #手机报告
                [
                    'pattern' => 'mobile-report/<id:\d+>',
                    'route' => 'ident/mobile-report',
                    'verb' => 'GET'
                ],
                #手机审核操作
                [
                    'pattern' => 'mobile-check',
                    'route' => 'ident/mobile-check',
                    'verb' => 'POST'
                ],
                #个人信息认证
                [
                    'pattern' => 'auth-profile',
                    'route' => 'ident/profile-auth',
                    'verb' => 'GET'
                ],
                #工作信息
                [
                    'pattern' => 'work-msg/<user_id:\d+>',
                    'route' => 'ident/work',
                    'verb' => 'GET'
                ],
                #人际关系
                [
                    'pattern' => 'relation-msg/<user_id:\d+>',
                    'route' => 'ident/relation',
                    'verb' => 'GET'
                ],
                #银行认证
                [
                    'pattern' => 'auth-bank',
                    'route' => 'ident/bank-auth',
                    'verb' => 'GET'
                ],
                 #京东认证
                [
                    'pattern' => 'auth-jd',
                    'route' => 'ident/jd-auth',
                    'verb' => 'GET'
                ],
                #京东、淘宝、学历、公积金、社保、央行征信详情
                [
                    'pattern' => 'limu-info/<id:\d+>',
                    'route' => 'ident/limu-info',
                    'verb' => 'GET'
                ],
                #淘宝认证
                [
                    'pattern' => 'auth-taobao',
                    'route' => 'ident/taobao-auth',
                    'verb' => 'GET'
                ],
                #学历认证
                [
                    'pattern' => 'auth-edu',
                    'route' => 'ident/edu-auth',
                    'verb' => 'GET'
                ],
                #信用卡账单
                [
                    'pattern' => 'auth-bill',
                    'route' => 'ident/bill-auth',
                    'verb' => 'GET'
                ],
                #网银流水
                [
                    'pattern' => 'auth-ebank',
                    'route' => 'ident/ebank-auth',
                    'verb' => 'GET'
                ],
                #公积金认证
                [
                    'pattern' => 'auth-house-fund',
                    'route' => 'ident/house-fund-auth',
                    'verb' => 'GET'
                ],
                #社保认证
                [
                    'pattern' => 'auth-social-security',
                    'route' => 'ident/social-security-auth',
                    'verb' => 'GET'
                ],
                #央行征信认证
                [
                    'pattern' => 'auth-credit',
                    'route' => 'ident/credit-auth',
                    'verb' => 'GET'
                ],
                # 获取地区名称
                [
                    'pattern' => 'area/<code:\d+>',
                    'route' => 'ident/area',
                    'verb' => 'GET'
                ],
                # 删除银行卡认证记录
                [
                    'pattern' => 'del-bank',
                    'route' => 'ident/del-user-bank-auth',
                    'verb' => 'POST'
                ],
                # 删除手机认证记录
                [
                    'pattern' => 'del-mobile',
                    'route' => 'ident/del-user-mobile-auth',
                    'verb' => 'POST'
                ],
                # 额度提升
                [
                    'pattern' => 'increase-quota',
                    'route' => 'ident/increase-quota',
                    'verb' => 'GET'
                ],
                # 删除淘宝，京东、学历等立木认证信息
                [
                    'pattern' => 'del-limu',
                    'route' => 'ident/del-user-limu',
                    'verb' => 'post'
                ],
                #### 逾期管理API #######################
                #逾期列表
                [
                    'pattern' => 'overdues',
                    'route' => 'overdue/index',
                    'verb' => 'GET'
                ],
                #催收员列表
                [
                    'pattern' => 'staffs',
                    'route' => 'overdue/staff',
                    'verb' => 'GET'
                ],
                #催收分配操作
                [
                    'pattern' => 'assign',
                    'route' => 'overdue/assign',
                    'verb' => 'POST',
                ],
                #催收列表
                [
                    'pattern' => 'urge-lists',
                    'route' => 'overdue/urge-list',
                    'verb' => 'GET'
                ],
                #逾期催收日记
                [
                    'pattern' => 'urge-log/<id:\d+>',
                    'route' => 'overdue/urge-log',
                    'verb' => 'GET'
                ],
                #我的催收
                [
                    'pattern' => 'urgency',
                    'route' => 'overdue/urgency',
                    'verb' => 'GET'
                ],
                #获取前十通话
                [
                    'pattern' => 'call-records/<budget_plan_id:\d+>',
                    'route' => 'overdue/user-call-records',
                    'verb' => 'GET'
                ],
                #逾期短信群发
                [
                    'pattern' => 'overdue-mass',
                    'route' => 'overdue/send-overdue-group-messages',
                    'verb' => 'POST'
                ],
                #催收操作
                [
                    'pattern' => 'urge',
                    'route' => 'overdue/urge',
                    'verb' => 'POST'
                ],
                #### 内容管理 #########################
                #文章管理
                [
                    'pattern' => 'article',
                    'route' => 'content/article',
                    'verb' => 'GET'
                ],
                #添加文章
                [
                    'pattern' => 'article-add',
                    'route' => 'content/article-add',
                    'verb' => 'POST'
                ],
                #编辑文章
                [
                    'pattern' => 'article-update/<id:\d+>',
                    'route' => 'content/article-update',
                    'verb' => 'PUT'
                ],
                #获取文章
                [
                    'pattern' => 'get-article/<id:\d+>',
                    'route' => 'content/get-article',
                    'verb' => 'GET'
                ],
                #删除文章
                [
                    'pattern' => 'del-article',
                    'route' => 'content/article-del',
                    'verb' => 'POST'
                ],
                #文章图片上传
                [
                    'pattern' => 'upload',
                    'route' => 'site/upload',
                    'verb' => 'POST'
                ],
                #七牛图片上传
                [
                    'pattern' => 'qiniu-upload',
                    'route' => 'site/qiniu-upload',
                    'verb' => 'POST'
                ],
                # 编辑器图片上传七牛
                [
                    'pattern' => 'qiniu',
                    'route' => 'site/qiniu',
                    'verb' => 'POST'
                ],
                #短信管理
                [
                    'pattern' => 'messages',
                    'route' => 'content/message',
                    'verb' => 'GET'
                ],
                #意见反馈
                [
                    'pattern' => 'feedback',
                    'route' => 'content/feedback',
                    'verb' => 'GET'
                ],
                #删除反馈意见
                [
                    'pattern' => 'del-feedback',
                    'route' => 'content/del-feedback',
                    'verb' => 'POST'
                ],
                #### 支付管理 #########################
                #借款确认列表
                [
                    'pattern' => 'confirm-list',
                    'route' => 'payment/lianlian-confirm-list',
                    'verb' => 'GET'
                ],
                #借款确认
                [
                    'pattern' => 'payment-confirm',
                    'route' => 'payment/lianlian-payment-confirm',
                    'verb' => 'POST'
                ],
                #### 统计管理 #############
                # 今日统计-控制面板
                [
                    'pattern' => 'statistic',
                    'route' => 'statistic/index',
                    'verb' => 'GET'
                ],
                # 累计统计-控制面板
                [
                    'pattern' => 'accumulate-statistic',
                    'route' => 'statistic/accumulate-statistic',
                    'verb' => 'GET'
                ],
                # 每日统计
                [
                    'pattern' => 'daily',
                    'route' => 'statistic/daily-statistics',
                    'verb' => 'GET'
                ],
                # 借款统计
                [
                    'pattern' => 'loan-statistics',
                    'route' => 'statistic/loan-statistics',
                    'verb' => 'GET'
                ],
                # 还款统计
                [
                    'pattern' => 'repayment-statistics',
                    'route' => 'statistic/repayment-statistics',
                    'verb' => 'GET'
                ],
                # 逾期统计
                [
                    'pattern' => 'overdue-statistics',
                    'route' => 'statistic/overdue-statistics',
                    'verb' => 'GET'
                ],
                # 催收统计
                [
                    'pattern' => 'urge-statistics',
                    'route' => 'statistic/urge-statistics',
                    'verb' => 'GET'
                ],
                # 导出借款统计excel
                [
                    'pattern' => 'exp-excel',
                    'route' => 'statistic/export-loan-excel',
                    'verb' => 'GET'
                ],
                ##### 额度管理 ##############
                # 额度列表
                [
                    'pattern' => 'quotas',
                    'route' => 'quota/index',
                    'verb' => 'GET'
                ],
                # 添加额度
                [
                    'pattern' => 'quota-apply',
                    'route' => 'quota/quota-apply',
                    'verb' => 'POST'
                ],
                # 额度审核列表
                [
                    'pattern' => 'quota-audit',
                    'route'=> 'quota/audit-list',
                    'verb' => 'GET'
                ],
                # 审核额度申请
                [
                    'pattern' => 'audit-quota',
                    'route' => 'quota/audit-quota',
                    'verb' => 'POST'
                ],
                # 额度记录列表
                [
                    'pattern' => 'quota-log',
                    'route' => 'quota/audit-quota-log',
                    'verb' => 'GET'
                ],
                ##### 商户额度管理 #######
                # 商户额度列表
                [
                    'pattern' => 'shop-quota-list',
                    'route' => 'quota/shop-quota',
                    'verb' => 'GET'
                ],
                # 添加商户额度
                [
                    'pattern' => 'shop-quota-apply/<shop_id:\d+>',
                    'route' => 'quota/shop-quota-apply',
                    'verb' => 'POST'
                ],
                # 商户额度审核列表
                [
                    'pattern' => 'shop-quota-audit-list',
                    'route' => 'quota/shop-quota-audit-list',
                    'verb' => 'GET'
                ],
                # 商户额度审核
                [
                    'pattern' => 'shop-quota-audit/<id:\d+>',
                    'route' => 'quota/shop-quota-audit',
                    'verb' => 'PUT'
                ],
                # 商户额度审核记录
                [
                    'pattern' => 'shop-quota-log-list',
                    'route' => 'quota/shop-quota-log-list',
                    'verb' => 'GET'
                ],
                #### 分类管理 ############
                # 商户分类
                [
                    'pattern' => 'category-shop',
                    'route' => 'category/shop-category',
                    'verb' => 'GET'
                ],
                # 商品分类
                [
                    'pattern' => 'category-pro',
                    'route' => 'category/pro-category',
                    'verb' => 'GET'
                ],
                # 添加分类
                [
                    'pattern' => 'category-add',
                    'route' => 'category/create',
                    'verb' => 'POST'
                ],
                # 删除分类
                [
                    'pattern' => 'category-del',
                    'route' => 'category/del',
                    'verb' => 'POST'
                ],
                # 编辑分类
                [
                    'pattern' => 'category-update',
                    'route' => 'category/update',
                    'verb' => 'POST'
                ],
                ######### 商户申请管理 #########
                # 商户申请
                [
                    'pattern' => 'shop-apply',
                    'route' => 'shop/apply',
                    'verb' => 'POST'
                ],
                # 商户详情
                [
                    'pattern' => 'shop-detail/<shop_id:\d+>',
                    'route' => 'shop/detail',
                    'verb' => 'GET'
                ],
                # 商户申请填写表单获取数据接口
                [
                    'pattern' => 'need-msg',
                    'route' => 'shop/api',
                    'verb' => 'GET',
                ],
                # 获取某省的所有城市
                [
                    'pattern' => 'get-cities/<id:\d+>',
                    'route' => 'shop/city',
                    'verb' => 'GET',
                ],
                #商户申请详情
                [
                    'pattern' => 'shop-detail/<id:\d+>',
                    'route' => 'shop/detail',
                    'verb' => 'GET'
                ],
                # 编辑商户
                [
                    'pattern' => 'shop-update/<shop_id:\d+>',
                    'route' => 'shop/update',
                    'verb' => 'PUT'
                ],
                # 商户列表
                [
                    'pattern' => 'shop-list',
                    'route' => 'shop/list',
                    'verb' => 'GET'
                ],
                # 商户审核
                [
                    'pattern' => 'shop-audit/<shop_id:\d+>',
                    'route' => 'shop/audit',
                    'verb' => 'PUT'
                ],
                # 商户入驻列表
                [
                    'pattern' => 'shop-settled',
                    'route' => 'shop/settled-list',
                    'verb' => 'GET'
                ],
                # 删除商户入驻记录
                [
                    'pattern' => 'del-shop-settled/<id:\d+>',
                    'route' => 'shop/del-shop-settled',
                    'verb' => 'PUT'
                ],
                ###### 商户管理员 ############
                # 商户管理员详情
                [
                    'pattern' => 'shop-admin/<shop_id:\d+>',
                    'route' => 'shop-admin/detail',
                    'verb' => 'GET'
                ],
                # 添加商户管理员
                [
                    'pattern' => 'shop-admin-add',
                    'route' => 'shop-admin/add',
                    'verb' => 'POST'
                ],
                # 修改密码
                [
                    'pattern' => 'shop-admin-pwd',
                    'route' => 'shop-admin/set-password',
                    'verb' => 'PUT'
                ],
                ##### 反欺诈相关API #######################
                # 获取用户腾讯反欺诈信息
                [
                    'pattern' => 'anti-fraud/<user_id:\d+>',
                    'route' => 'user/get-anti-fraud',
                    'verb' => 'GET'
                ],
                # 创建用户腾讯反欺诈信息
                [
                    'pattern' => 'create-anti-fraud',
                    'route' => 'tencent/create-anti-fraud',
                    'verb' => 'POST'
                ],
                # 更新用户腾讯反欺诈信息
                [
                    'pattern' => 'update-anti-fraud',
                    'route' => 'tencent/update-anti-fraud',
                    'verb' => 'POST'
                ],
            ],
        ],
    ],
    'params' => $params,
];
