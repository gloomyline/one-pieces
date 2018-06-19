<?php

namespace shop\models;

use Yii;
use common\bases\CommonModel;

class MenuModel extends CommonModel
{
    const IS_PARENT = 0; // 父类菜单
    public static function getAllMenus()
    {
        return [
            '商户管理' => [
                'icon' => 'fa fa-dashboard',
                'children' => [
                    'backend_menus_shop_index' => [
                        'title' => '商户概况',
                        'route' => '/shop/#/index',
                        'children' => []
                    ],
                    'backend_menus_shop_decorate' => [
                        'title' => '店铺管理',
                        'route' => '/shop/#/decorate',
                        'children' => []
                    ],
                ]
            ],
            '商品管理' => [
                'icon' => 'fa-list-ul',
                'children' => [
                    'backend_menus_product' => [
                        'title' => '商品列表',
                        'route' => '/shop/#/product-list',
                        'children' => []
                    ],
                ]
            ],
            '订单管理' => [
                'icon' => 'fa-cubes',
                'children' => [
                    'backend_menus_order' => [
                        'title' => '订单列表',
                        'route' => '/shop/#/order-list',
                        'children' => []
                    ],
                ]
            ],
            '账户管理' => [
                'icon' => 'fa-user',
                'children' => [
                    'backend_menus_pwd' => [
                        'title' => '修改密码',
                        'route' => '/shop/#/pwd',
                        'children' => []
                    ],
                ]
            ],
        ];
    }

    private static function getByPermission($menus)
    {
        $result = [];
        foreach ($menus as $key => $menu) {
            // 第二层或以下没有子菜单的话就是叶节点，检查菜单权限
            if (empty($menu['children'])) {
                $result[] = [
                    'title' => $menu['title'],
                    'route' => $menu['route']
                ];
            // 有子菜单检查所有子菜单的权限
            } else {
                $children = self::getByPermission($menu['children']);
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
        // $routes = Yii::$app->user->identity->getRoutes();
        return self::getByPermission(self::getAllMenus()/*, $routes*/);
    }
}