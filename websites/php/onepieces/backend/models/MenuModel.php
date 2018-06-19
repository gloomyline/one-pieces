<?php

namespace backend\models;

use Yii;
use common\bases\CommonModel;

class MenuModel extends CommonModel
{
    const IS_PARENT = 0; // 父类菜单
    public static function getAllMenus()
    {
        return [
            '导航管理' => [
                'icon' => 'fa fa-dashboard',
                'children' => [
                    'backend_menus_nav' => [
                        'title' => '导航列表',
                        'route' => '/vue-dist/#/nav',
                        'children' => []
                    ],
                ]
            ],
            'banner' => [
                'icon' => 'fa fa-picture-o',
                'children' => [
                    'backend_menus_banner' => [
                        'title' => 'banner列表',
                        'route' => '/vue-dist/#/banner',
                        'children' => []
                    ],
                ]
            ],
            '合作伙伴' => [
                'icon' => 'fa fa-users',
                'children' => [
                    'backend_menus_partner' => [
                        'title' => '合作伙伴',
                        'route' => '/vue-dist/#/partner',
                        'children' => []
                    ],
                ]
            ],
            '文章管理' => [
                'icon' => 'fa fa-newspaper-o',
                'children' => [
                    'backend_menus_article' => [
                        'title' => '文章列表',
                        'route' => '/vue-dist/#/article',
                        'children' => []
                    ],
                ]
            ],
            '案例中心' => [
                'icon' => 'fa fa-folder',
                'children' => [
                    'backend_menus_example' => [
                        'title' => '案例列表',
                        'route' => '/vue-dist/#/example',
                        'children' => []
                    ],
                ]
            ],
            '权限管理' => [
                'icon' => 'fa-lock',
                'children' => [
                    'backend_menus_permissions_admins' => [
                        'title' => '管理员列表',
                        'route' => '/vue-dist/#/admins',
                        'children' => []
                    ],
                    'backend_menus_permissions_roles' => [
                        'title' => '角色管理',
                        'route' => '/vue-dist/#/roles',
                        'children' => []
                    ],
                    'backend_menus_permissions_auths' => [
                        'title' => '权限管理',
                        'route' => '/vue-dist/#/auths',
                        'children' => []
                    ],
                ]
            ],
        ];
    }

    private static function getByPermission($menus, $routes)
    {
        $result = [];
        foreach ($menus as $key => $menu) {
            // 第二层或以下没有子菜单的话就是叶节点，检查菜单权限
            if (empty($menu['children'])) {
                if (in_array($key, $routes)) {
                    $result[] = [
                        'title' => $menu['title'],
                        'route' => $menu['route']
                    ];
                }
            // 有子菜单检查所有子菜单的权限
            } else {
                $children = self::getByPermission($menu['children'], $routes);
                if (!empty($children)) {
                    $result[] = [
                        'title' => $key,
                        'icon' => $menu['icon'],
                        'children' => $children
                    ];
                }
            }
        }
        return $result;
    }

    public static function getMenus()
    {
        $routes = Yii::$app->user->identity->getRoutes();
        return self::getByPermission(self::getAllMenus(), $routes);
    }
}