<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
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
            'name' => 'onepieces-backend',
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
                ########### 配置管理 ##########
                # 案例中心配置
                [
                    'pattern' => 'set-params/<type:\w+>',
                    'route' => 'system/set-params',
                    'verb' => 'PUT'
                ],
                ########### 导航管理 ##########
                # 导航列表
                [
                    'pattern' => 'nav',
                    'route' => 'nav/index',
                    'verb' => 'GET'
                ],
                # 添加导航
                [
                    'pattern' => 'nav-add',
                    'route' => 'nav/add',
                    'verb' => 'POST'
                ],
                # 编辑导航
                [
                    'pattern' => 'nav-update',
                    'route' => 'nav/update',
                    'verb' => 'PUT',
                ],
                # 删除导航
                [
                    'pattern' => 'nav-del',
                    'route' => 'nav/del',
                    'verb' => 'POST',
                ],
                # 根据上级id获取导航
                [
                    'pattern' => 'get-nav/<pid:\d+>',
                    'route' => 'nav/get-nav',
                    'verb' => 'GET',
                ],
                ######## 上传 #########
                [
                    'pattern' => 'upload',
                    'route' => 'site/upload',
                    'verb' => 'POST'
                ],
                ############ 合作伙伴 ########
                # 合作伙伴列表
                [
                    'pattern' => 'partner',
                    'route' => 'partner/index',
                    'verb' => 'GET'
                ],
                # 添加合作伙伴
                [
                    'pattern' => 'partner-add',
                    'route' => 'partner/add',
                    'verb' => 'POST'
                ],
                # 编辑合作伙伴
                [
                    'pattern' => 'partner-update',
                    'route' => 'partner/update',
                    'verb' => 'PUT',
                ],
                # 删除合作伙伴
                [
                    'pattern' => 'partner-del',
                    'route' => 'partner/del',
                    'verb' => 'POST',
                ],
                ########### banner 管理 ############
                # 合作伙伴列表
                [
                    'pattern' => 'banner',
                    'route' => 'banner/index',
                    'verb' => 'GET'
                ],
                # 添加合作伙伴
                [
                    'pattern' => 'banner-add',
                    'route' => 'banner/add',
                    'verb' => 'POST'
                ],
                # 编辑合作伙伴
                [
                    'pattern' => 'banner-update',
                    'route' => 'banner/update',
                    'verb' => 'PUT',
                ],
                # 删除合作伙伴
                [
                    'pattern' => 'banner-del',
                    'route' => 'banner/del',
                    'verb' => 'POST',
                ],
                ############# 案例中心 ############
                # 案例中心列表
                [
                    'pattern' => 'example',
                    'route' => 'example/index',
                    'verb' => 'GET'
                ],
                # 添加案例
                [
                    'pattern' => 'example-add',
                    'route' => 'example/add',
                    'verb' => 'POST'
                ],
                # 编辑案例
                [
                    'pattern' => 'example-update',
                    'route' => 'example/update',
                    'verb' => 'PUT',
                ],
                # 删除案例
                [
                    'pattern' => 'example-del',
                    'route' => 'example/del',
                    'verb' => 'POST',
                ],
                ############# 文章管理 ############
                # 文章列表
                [
                    'pattern' => 'article',
                    'route' => 'article/index',
                    'verb' => 'GET'
                ],
                # 添加文章
                [
                    'pattern' => 'article-add',
                    'route' => 'article/add',
                    'verb' => 'POST'
                ],
                # 编辑文章
                [
                    'pattern' => 'article-update',
                    'route' => 'article/update',
                    'verb' => 'PUT',
                ],
                # 删除文章
                [
                    'pattern' => 'article-del',
                    'route' => 'article/del',
                    'verb' => 'POST',
                ],
                # 添加文章时下发内容
                [
                    'pattern' => 'article-need',
                    'route' => 'article/need',
                    'verb' => 'GET',
                ],
                # 添加时下发内容
                [
                    'pattern' => 'get-article/<id:\d+>',
                    'route' => 'article/get-article',
                    'verb' => 'GET',
                ],
            ],
        ]
    ],
    'params' => $params,
];
