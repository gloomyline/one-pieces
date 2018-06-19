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
            '控制面板' => [
                'icon' => 'fa fa-dashboard',
                'children' => [
                    'backend_menus_statistics' => [
                        'title' => '统计信息',
                        'route' => '/vue-dist/#/statistics',
                        'children' => []
                    ],
                ]
            ],
            '用户管理' => [
                'icon' => 'fa-address-book-o',
                'children' => [
                    'backend_menus_members' => [
                        'title' => '用户列表',
                        'route' => '/vue-dist/#/users',
                        'children' => []
                    ],
                ]
            ],
            '借款管理' => [
                'icon' => 'fa-money',
                'children' => [
                    'backend_menus_loan_all' => [
                        'title' => '全部借款',
                        'route' => '/vue-dist/#/borrows',
                        'children' => []
                    ],
                    'backend_menus_loan_trials' => [
                        'title' => '初审管理',
                        'route' => '/vue-dist/#/checks',
                        'children' => []
                    ],
                    'backend_menus_loan_reviews' => [
                        'title' => '复审管理',
                        'route' => '/vue-dist/#/reviews',
                        'children' => []
                    ],
                    'backend_menus_loan_repayments' => [
                        'title' => '还款管理',
                        'route' => '/vue-dist/#/repayments',
                        'children' => []
                    ],
                ]
            ],
            '额度管理' => [
                'icon' => 'fa-cubes',
                'children' => [
                    'backend_menus_quota_all' => [
                        'title' => '额度列表',
                        'route' => '/vue-dist/#/quotas',
                        'children' => []
                    ],
                    'backend_menus_quota_apply' => [
                        'title' => '额度申请审核列表',
                        'route' => '/vue-dist/#/quota-audit',
                        'children' => []
                    ],
                    'backend_menus_quota_log' => [
                        'title' => '额度记录',
                        'route' => '/vue-dist/#/quota-log',
                        'children' => []
                    ],
                ]
            ],
            '认证管理' => [
                'icon' => 'fa-id-card',
                'children' => [
                    'backend_menus_ident_center' => [
                        'title' => '认证中心',
                        'route' => '/vue-dist/#/auth-center',
                        'children' => [],
                    ],
                    'backend_menus_ident_auth' => [
                        'title' => '身份认证',
                        'route' => '/vue-dist/#/identity',
                        'children' => [],
                    ],
                    'backend_menus_ident_profile' => [
                        'title' => '个人信息',
                        'route' => '/vue-dist/#/auth-profile',
                        'children' => [],
                    ],
                    'backend_menus_ident_bank' => [
                        'title' => '银行卡认证',
                        'route' => '/vue-dist/#/auth-bank',
                        'children' => [],
                    ],
                    'backend_menus_ident_mobile' => [
                        'title' => '手机认证',
                        'route' => '/vue-dist/#/auth-mobile',
                        'children' => [],
                    ],
                    'backend_menus_jd_auth' => [
                        'title' => '京东认证',
                        'route' => '/vue-dist/#/auth-jd',
                        'children' => [],
                    ],
                    'backend_menus_taobao_auth' => [
                        'title' => '淘宝认证',
                        'route' => '/vue-dist/#/auth-taobao',
                        'children' => [],
                    ],
                    'backend_menus_education_auth' => [
                        'title' => '学历认证',
                        'route' => '/vue-dist/#/auth-edu',
                        'children' => [],
                    ],
                    'backend_menus_bill_auth' => [
                        'title' => '信用卡账单',
                        'route' => '/vue-dist/#/auth-bill',
                        'children' => [],
                    ],
                    'backend_menus_ebank_auth' => [
                        'title' => '网银流水',
                        'route' => '/vue-dist/#/auth-ebank',
                        'children' => [],
                    ],
                    'backend_menus_ident_inc_amount' => [
                        'title' => '提升额度',
                        'route' => '/vue-dist/#/increase-quota',
                        'children' => [],
                    ],
                ],
            ],
            '逾期管理' => [
                'icon' => 'fa-minus-square',
                'children' => [
                    'backend_menus_overdue_all' => [
                        'title' => '逾期列表',
                        'route' => '/vue-dist/#/overdues',
                        'children' => []
                    ],
                    'backend_menus_overdue_urgency' => [
                        'title' => '我的催收',
                        'route' => '/vue-dist/#/urgency',
                        'children' => []
                    ],
                ],
            ],
            '内容管理' => [
                'icon' => 'fa fa-newspaper-o',
                'children' => [
                    'backend_menus_article_all' => [
                        'title' => '文章管理',
                        'route' => '/vue-dist/#/article',
                        'children' => []
                    ],
                    'backend_menus_msg_all' => [
                        'title' => '短信管理',
                        'route' => '/vue-dist/#/messages',
                        'children' => []
                    ],
                    'backend_menus_feedback_all' => [
                        'title' => '意见反馈',
                        'route' => '/vue-dist/#/feedback',
                        'children' => []
                    ],
                ],
            ],
            '代金券管理' => [
                'icon' => 'fa fa-jpy',
                'children' => [
                    'backend_menus_coupon_all' => [
                        'title' => '代金券管理',
                        'route' => '/vue-dist/#/coupon',
                        'children' => []
                    ],
                    'backend_menus_coupon_log' => [
                        'title' => '使用记录',
                        'route' => '/vue-dist/#/coupon-log',
                        'children' => []
                    ],
                    'backend_menus_coupon_assign' => [
                        'title' => '手动发放',
                        'route' => '/vue-dist/#/coupon-assign',
                        'children' => []
                    ],
                ],
            ],
            '产品配置' => [
                'icon' => 'fa-hdd-o',
                'children' => [
                    'backend_menus_product_all' => [
                        'title' => '产品配置列表',
                        'route' => '/vue-dist/#/products',
                        'children' => []
                    ],
                ],
            ],
            '统计管理' => [
                'icon' => 'fa-line-chart',
                'children' => [
                    'backend_menus_statistics_daily' => [
                        'title' => '每日统计',
                        'route' => '/vue-dist/#/daily',
                        'children' => []
                    ],
                    'backend_menus_statistics_loan' => [
                        'title' => '借款统计',
                        'route' => '/vue-dist/#/loan-statistics',
                        'children' => []
                    ],
                    'backend_menus_statistics_repayment' => [
                        'title' => '还款统计',
                        'route' => '/vue-dist/#/repayment-statistics',
                        'children' => []
                    ],
                    'backend_menus_statistics_overdue' => [
                        'title' => '逾期统计',
                        'route' => '/vue-dist/#/overdue-statistics',
                        'children' => []
                    ],
                    'backend_menus_statistics_urge' => [
                        'title' => '催收统计',
                        'route' => '/vue-dist/#/urge-statistics',
                        'children' => []
                    ],
                ],
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
            '系统管理' => [
                'icon' => 'fa-th-large',
                'children' => [
                    'backend_menus_risk_credit' => [
                        'title' => '信用分设置',
                        'route' => '/vue-dist/#/credit-set',
                        'children' => []
                    ],
                    'backend_menus_risk' => [
                        'title' => '信用分表字段',
                        'route' => '/vue-dist/#/risk',
                        'children' => []
                    ],
                    'backend_menus_risk_rule' => [
                        'title' => '信用分规则配置',
                        'route' => '/vue-dist/#/risk-rule',
                        'children' => []
                    ],
                ]
            ],
            '支付管理' => [
                'icon' => 'fa-cny',
                'children' => [
                    'backend_menus_payment_confirm' => [
                        'title' => '付款确认',
                        'route' => '/vue-dist/#/confirm-list',
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

    public function add($data)
    {
        $model = new Menu();
        $model->setAttributes($data);
        $model->created_at = date('Y-m-d H:i:s');
        $model->updated_at = date('Y-m-d H:i:s');
        $result = $model->save();
        if ($result) {
            return $model->id;
        }
        return false;
    }
}