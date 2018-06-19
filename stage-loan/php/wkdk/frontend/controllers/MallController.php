<?php
namespace frontend\controllers;

use common\models\CategoryModel;
use common\models\ProductModel;
use common\models\ShopModel;
use common\models\ShopProductModel;
use common\models\ShopProSpecModel;
use common\services\LoanService;
use common\services\SearchService;
use frontend\bases\FrontendController;
use frontend\services\MallService;
use Yii;
use yii\helpers\Json;

class MallController extends FrontendController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        // 访问控制
        $behaviors['access']['rules'] = [
            // 允许访客用户访问的Action
            [
                'actions' => [],
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
     * 商户列表
     * @return string
     */
    public function actionShops()
    {
        $request = Yii::$app->request;
        $categoryId = $request->get('category_id', 0); // 分类ID
        $cityId = $request->get('city_id', 0); // 城市ID
        $shopName = $request->get('shop_name'); // 商户名称
        $limit = $request->get('limit', 10); // 查询的记录数
        $offset = $request->get('offset', 0); // 查询的基准数
        $condition = $keyWord = [];
        if ($shopName && trim($shopName) != '') {
            $keyWord[] = $shopName;
        } else if ($shopName !== null && trim($shopName) === '') {
            return Json::encode(['status' => self::STATUS_SUCCESS, "error_message" => "", 'results' => [], 'has_more' => false]);
        }
        if ($cityId && $cityId != 0 && $cityId != '') {
            $condition['city_id'] = $cityId;
        }
        if ($categoryId && $categoryId != 0 && $categoryId != '') {
            $condition['category_id'] = $categoryId;
        }
        try {
            $shopIds = SearchService::searchShopDoc($keyWord, $condition, $limit, ($offset / $limit + 1));
        } catch (\XSErrorException $e) {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                "error_message" => "服务器异常，请检查xunSearch服务是否开启",
            ]);
        }
        $shop = ShopModel::findShopByCondition(['id' => $shopIds['ids']]);
        $data = [];
        foreach ($shop as $v) {
            // 不必判断审核状态的原因：仅审核通过后的商户信息加入 xunsearch 服务
            $data[] = [
                'id' => $v['id'] ?? 0, // 商户ID
                'shop_name' => $v['shop_name'] ?? '', // 商户名称
                'shop_addr' => $v['shop_addr'] ?? '', // 商户地址
                'logo' => $v['logo'] ?? '', // 商户logo
            ];
        }

        if (($offset + $limit) < $shopIds['count']) {
            $hasMore = true; // 标识是否存在更多数据，true 时标识存在， false 标识无更多数据
        } else {
            $hasMore = false; // 标识是否存在更多数据，true 时标识存在， false 标识无更多数据
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            "error_message" => "",
            'results' => $data ?? [],
            'has_more' => $hasMore
        ]);
    }

    public function actionShopDetail()
    {
        $request = Yii::$app->request;
        $shopId = $request->get('shop_id');
        if (empty($shopId)) {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                "error_message" => "参数错误",
            ]);
        }
        $shop = ShopModel::findShopByShopId($shopId);
        $data = [
            'banner' => isset($shop['shop_pic']) ? explode(',', $shop['shop_pic']) : [], // banner图
            'shop_name' => $shop['shop_name'] ?? '', // 商户名称
            'logo' => $shop['logo'] ?? '', // 商家logo
            'intro' => $shop['intro'] ?? '', // 商家介绍
            'shop_addr' => $shop['shop_addr'] ?? '', // 商家地址
            'corporate_contacts_mobile' => $shop['corporate_contacts_mobile'] ?? '', // 企业联系人手机号
        ];
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            "error_message" => "",
            'results' => $data,
        ]);

    }

    /**
     * 通过商户号查询商户信息
     * @return string
     */
    public function actionGetShop()
    {
        $request = Yii::$app->request;
        $shopNo = $request->get('shop_no', '');
        if ($shopNo == '') {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                "error_message" => "商户号不能为空",
            ]);
        }
        $shop = ShopModel::findShopByShopNo($shopNo);
        if ($shop) {
            // 商户审核通过
            if ($shop['state'] == ShopModel::STATE_AUDIT_PASS) {
                $data = [
                    'shop_name' => $shop['shop_name']
                ];
            }
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            "error_message" => "",
            'results' => $data ?? [],
        ]);
    }

    /**
     * 商户基本信息
     */
    public function actionShopBase()
    {
        $request = Yii::$app->request;
        $shopNo = $request->get('shop_no', ''); // 商户号
        if ($shopNo == '') {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                "error_message" => "商户号不能为空",
            ]);
        }
        $shop = ShopModel::findShopByShopNo($shopNo); // 商户信息

        if ($shop) {
            if ($shop['state'] == ShopModel::STATE_AUDITING) {
                return Json::encode([
                    'status' => self::STATUS_FAILURE,
                    "error_message" => "该商户正在审核中，请耐心等待",
                ]);
            } else if ($shop['state'] == ShopModel::STATE_AUDIT_NOPASS) {
                return Json::encode([
                    'status' => self::STATUS_FAILURE,
                    "error_message" => "该商户尚未通过平台审核，请联系商户",
                ]);
            }
            $data = [
                'shop_id' => $shop['id'] ?? '', // 商户ID
                'logo' => $shop['logo'] ?? '', // 商家logo
                'shop_pic' => explode(',', $shop['shop_pic'] ?? ''), // 店铺图片
                'shop_name' => $shop['shop_name'], // 店铺名称
                'intro' => $shop['intro'], // 店铺介绍
            ];
        } else {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                "error_message" => "无此商户信息",
            ]);
        }

        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            "error_message" => "",
            'results' => $data ?? [],
        ]);
    }

    /**
     * 商家分类
     */
    public function actionShopCategory()
    {
        $request = Yii::$app->request;
        $shopNo = $request->get('shop_no', ''); // 商户号
        if ($shopNo == '') {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                "error_message" => "商户号不能为空",
            ]);
        }
        $data['category'][] = [
            'id' => 0, // 分类ID
            'title' => '全部', // 分类名称
        ];
        $shop = ShopModel::findShopByShopNo($shopNo); // 商户信息
        if ($shop) {
            $category = CategoryModel::getChildrenCategoryByIds(explode(',', $shop['product_category']));
            if ($category) {
                foreach ($category as $v) {
                    $data['category'][] = [
                        'id' => $v['id'], // 分类ID
                        'title' => $v['title'], // 分类名称
                    ];
                }
            }
        } else {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                "error_message" => "无此商户信息",
            ]);
        }

        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            "error_message" => "",
            'results' => $data ?? [],
        ]);
    }

    /**
     * 商家产品
     */
    public function actionShopProduct()
    {
        $request = Yii::$app->request;
        $categoryId = $request->get('category_id', 0); // 分类ID
        $shopId = (integer)trim($request->get('shop_id', '')); // 商户ID
        $offset = $request->get('offset', 0); // 查询的基准数
        $limit = $request->get('limit', 10); // 查询的记录数
        if (!$shopId) {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                "error_message" => "商户ID不能为空",
            ]);
        }
        $shopProduct = ShopProductModel::getShopProductByCategoryId($categoryId, $shopId, $offset, $limit); // 商户产品
        $condition = [
            'active' => ProductModel::STATE_ACTIVE, // 上线
            'type' => ProductModel::TYPE_CONSUMPTION, // 消费分期
        ];
        $product = ProductModel::getProductByCondition($condition); // 当前上线的消费分期产品详情
        if (!$product) {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                "error_message" => "获取消费产品配置失败，请联系客服",
            ]);
        }
        $period = explode(',', $product['period']); // 期限
        rsort($period); // 数组倒序
        if (is_array($period)) {
            $maxTerm = $period[0]; // 最大期数
        } else {
            $maxTerm = $period;// 最大期数
        }
        if ($shopProduct['detail']) {
            foreach ($shopProduct['detail'] as $v) {
                $minPrice = ShopProSpecModel::findShopProSpecByMinPrice($v['id']) ? ShopProSpecModel::findShopProSpecByMinPrice($v['id'])->price : 0; // 最低价格
                $interest = LoanService::caculateFee($minPrice, (integer)$maxTerm, $product['annualized_interest_rate'], $product['trial_rate'], $product['service_rate'], $product['poundage'], 'month');// 总息费
                $data[] = [
                    'id' => $v['id'], // 商户产品ID
                    'title' => $v['title'], // 商品名称
                    'pic' => $v['pic'] ? (explode(',', $v['pic'])[0] ?? '') : '', // 图片,第一张主图
                    'monthly' => round((($minPrice + $interest) / (integer)$maxTerm), 2), // 月供 按照产品配置的最大值计算
                ];
            }
        }
        if (($offset + $limit) < $shopProduct['count']) {
            $hasMore = true; // 标识是否存在更多数据，true 时标识存在， false 标识无更多数据
        } else {
            $hasMore = false; // 标识是否存在更多数据，true 时标识存在， false 标识无更多数据
        }

        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            "error_message" => "",
            'results' => $data ?? [],
            'has_more' => $hasMore
        ]);
    }

    /**
     * 商家商品详情
     */
    public function actionShopProductDetail()
    {
        $request = Yii::$app->request;
        $shopProductId = (integer)trim($request->get('shop_product_id', ''));
        if (!$shopProductId) {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                "error_message" => "参数错误",
            ]);
        }
        $shopProduct = ShopProductModel::findProductById($shopProductId); // 查询指定商品
        if ($shopProduct) {
            $shop = ShopModel::findShopByShopId($shopProduct['shop_id']); // 查找商家信息
            $data = [
                'shop_id' => $shopProduct['shop_id'] ?? 0, // 商家ID
                'banner' => $shopProduct['pic'] ? explode(',', $shopProduct['pic']) : [], // 产品图
                'title' => $shopProduct['title'], // 产品名称
                'term' => $shop['period'] ? explode(',', $shop['period']) : [], // 借款期数

                'intro' => $shopProduct['intro'], // 产品介绍
                'spec' => $shopProduct['spec'], // 规格参数
                'service' => $shopProduct['service'], // 售后
            ];
            foreach ($shopProduct['proSpec'] as $v) {
                $data['sku'][] = [
                    'spec_id' => $v['id'], // 规格ID
                    'spec' => $v['spec'], // 规格描述
                    'stock' => $v['stock'], // 库存
                    'price' => $v['price'], // 单价
                ];
            }
        }

        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            "error_message" => "",
            'results' => $data ?? [],
        ]);
    }

    /**
     * 获取还款金额明细
     */
    public function actionGetAmount()
    {
        $request = Yii::$app->request;
        $term = (integer)$request->post('term', ''); // 分期期数
        $amount = $request->post('amount', ''); // 商品总金额

        if (!$term || $amount == '') {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                "error_message" => "参数错误",
            ]);
        }
        $amount = round((float)$amount, 2);
        $product = ProductModel::getProductByCondition(['active' => ProductModel::STATE_ACTIVE, 'type' => ProductModel::TYPE_CONSUMPTION]); // 获取上线的消费分期产品
        $totalInterest = LoanService::caculateInstallmentFee(date('Y-m-d'), $amount, $term, $product['annualized_interest_rate'], $product['trial_rate'], $product['service_rate'], $product['poundage']); // 总息费
        $data = [
            'total_amount' => $amount + $totalInterest, // 合计应还总金额
            'monthly' => round(($amount + $totalInterest) / $term, 2), // 每期还款金额
        ];
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            "error_message" => "",
            'results' => $data ?? [],
        ]);
    }
}