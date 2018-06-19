<?php
/**
 * Created by PhpStorm.
 * User: lzh
 * Date: 2018/1/3
 * Time: 10:43
 */

namespace shop\controllers;


use common\models\CategoryModel;
use common\models\ShopModel;
use common\models\ShopProductModel;
use common\models\ShopProSpecModel;
use Yii;
use shop\bases\ShopBackendController;
use yii\helpers\Json;

class ProductController extends  ShopBackendController
{

    /**
     * 添加商品
     * @return string
     */
    public function actionAdd()
    {
        $request = Yii::$app->request;
        $title = trim($request->post('title', ''));
        if (empty($title)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请填写商品名称']);
        }
        $category = intval($request->post('category')); // 只保存商品分类id
        if (empty($category)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请选择商品分类']);
        }
        $no = trim($request->post('no', ''));
        if (empty($no)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请填写商品货号']);
        }
        // 总库存
        $totalStock = 0;
        $specArr = [];
        $specList = $request->post('specList', '');

        $shop = ShopModel::findShopByShopId(Yii::$app->user->identity->shop_id);

        if (empty($specList)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请填写商品规格，价格，库存']);
        } else {
            foreach ($specList as $list) {
                if (empty($list['spec']) || empty($list['price']) || $list['stock'] == '') {
                    return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '规格，价格，库存均不能留空']);
                }
                if (($shop->single_limit_quota) <  $list['price']) {
                    return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '价格填写不能大于'.$shop->single_limit_quota.'万元']);
                }
                if (!is_numeric($list['price']) || !is_numeric($list['stock'])) {
                    return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '价格，库存只能输入合法的数值']);
                }
                $specArr[] = ['spec' => $list['spec'], 'price' => $list['price'], 'stock' => $list['stock']];
                $totalStock += $list['stock'];
            }
        }

        $picArr = $request->post('picArr', '');
        if (!empty($picArr) && is_array($picArr)) {
            $pic = implode(',', $picArr);
        }
        $sort = intval($request->post('sort', 0));
        $intro = $request->post('intro', '');
        $spec = $request->post('spec', '');
        $service = $request->post('service', '');

        $data = [
            'title' => $title,
            'category_id' => $category ?? 0,
            'no' => $no,
            'pic' => $pic ?? '',
            'sort' => $sort ?? 0,
            'intro' => $intro,
            'spec' => $spec,
            'service' => $service,
            'total_stock' => $totalStock,
            'shop_id' => Yii::$app->user->identity->shop_id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $transaction =Yii::$app->db->beginTransaction();
        try{

            // 添加商品表记录

            $productId = ShopProductModel::add($data);
            if (!$productId) {
                throw new \Exception('商品保存失败');
            }

            // 规格记录
            if (!ShopProSpecModel::batchInsert($specArr, $productId)) {
                throw new \Exception('商品规格保存失败');
            }

            $transaction->commit();
            return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
        }catch (\Exception $e){
            $transaction->rollBack();
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => $e->getMessage()]);
        }

    }

    /**
     * 编辑商品
     * @return string
     */
    public function actionUpdate()
    {
        $request = Yii::$app->request;
        $proId = $request->get('id', '');
        $product = ShopProductModel::findProductById($proId);
        if (!$product) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        $title = trim($request->post('title', ''));
        if (empty($title)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请填写商品名称']);
        }
        $category = intval($request->post('category')); // 只保存商品分类id
        if (empty($category)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请选择商品分类']);
        }

        $no = trim($request->post('no', ''));
        if (empty($no)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请填写商品货号']);
        }

        $picArr = $request->post('picArr', '');
        if (!empty($picArr) && is_array($picArr)) {
            $pic = implode(',', $picArr);
        }
        $sort = intval($request->post('sort', 0));
        $intro = $request->post('intro', '');
        $spec = $request->post('spec', '');
        $service = $request->post('service', '');

        $data = [
            'title' => $title,
            'category_id' => $category ?? 0,
            'no' => $no,
            'pic' => $pic ?? '',
            'sort' => $sort ?? 0,
            'intro' => $intro,
            'spec' => $spec,
            'service' => $service,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if (!ShopProductModel::update($proId, $data)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '保存失败']);
        }
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    /**
     * 商品审核
     * @return string
     */
    public function actionAudit()
    {
        $request = Yii::$app->request;
        $proId = $request->get('id', '');
        $product = ShopProductModel::findProductById($proId);
        if (!$product) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        $title = trim($request->post('title', ''));
        if (empty($title)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请填写商品名称']);
        }
        $category = intval($request->post('category')); // 只保存商品分类
        if (empty($category)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请选择商品分类']);
        }

        $no = trim($request->post('no', ''));
        if (empty($no)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请填写商品货号']);
        }

        $picArr = $request->post('picArr', '');
        if (!empty($picArr) && is_array($picArr)) {
            $pic = implode(',', $picArr);
        }
        $sort = intval($request->post('sort', 0));
        $intro = $request->post('intro', '');
        $spec = $request->post('spec', '');
        $service = $request->post('service', '');

        // 审核信息
        $state = $request->post('state', '');
        if (empty($state)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请填写审核结果']);
        }

        if ($state == ShopProductModel::STATE_AUDIT_PASS) {
            $onSale = $request->post('on_sale', '');
            if (empty($onSale)) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请填写商品的上架状态']);
            }
        }
        $opinion = $request->post('opinion', '');
        $data = [
            'title' => $title,
            'category_id' => $category ?? 0,
            'no' => $no,
            'pic' => $pic ?? '',
            'sort' => $sort ?? 0,
            'intro' => $intro,
            'spec' => $spec,
            'service' => $service,
            'state' => $state,
            'on_sale' => $onSale ?? ShopProductModel::IS_NOT_ON_SALE,
            'opinion' => $opinion,
            'auditor_id' => Yii::$app->user->id,
            'updated_at' => date('Y-m-d H:i:s'),
            'audited_at' => date('Y-m-d H:i:s'),
        ];

        if (!ShopProductModel::update($proId, $data)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '保存失败']);
        }
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    /**
     * 获取商户添加商品所需的数据
     * @return string
     */
    public function actionNeed()
    {
        $data = [];

        $shop = ShopModel::findShopByShopId(Yii::$app->user->identity->shop_id);
        if (!empty($shop->shop_category)) {
            $ids = explode(',', $shop->shop_category);
            $category = CategoryModel::getShopCategoryWithChildrenByIds($ids);
            if (!empty($shop->product_category)) {
                $productCategoryIds = explode(',', $shop->product_category);
            }
            if ($category) {
                foreach ($category as $list) {
                    $data[$list->title]['label'] = $list->title;
                    if (!empty($list->children)) {
                        foreach ($list->children as $lt) {
                            if (in_array($lt->id, $productCategoryIds)) {
                                $data[$list->title]['options'][] = [
                                    'id' => (int)$lt->id,
                                    'title' => $lt->title,
                                ];
                            }
                        }
                     }
                }
            }
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'result' => $data ?? [],
            'shop_name' => $shop->shop_name
        ]);

    }

    /**
     * 商品详情
     * @return string
     */
    public function actionDetail()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $result = ShopProductModel::findProductByIdWithSpecAndCategory($id);
        if (!$result) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        $data = [
            'id' =>$result->id,
            'title' => $result->title,
            'no' => $result->no,
            'sort' => $result->sort,
            'intro' => $result->intro,
            'spec' => $result->spec,
            'service' => $result->service,
            'state' => $result->state,
            'opinion' => $result->opinion,
            'on_sale' => $result->on_sale,
            'category' => $result->category_id,
            'category_name' => $result->category->title ?? ''
        ];
        // 图片
        $picArr = [];
        if ($result->pic) {
            $picArr = explode(',', $result->pic);
        }
        $data['picArr'] = $picArr;
        $specList = [];
        if ($result->proSpec) {
            foreach ($result->proSpec as $list) {
                $specList[] = [
                    'id' => $list->id,
                    'spec' => $list->spec,
                    'price' => $list->price,
                    'stock' => $list->stock,
                ];
            }
        }
         $data['specList'] = $specList;

        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'result' => $data
        ]);
    }

    /**
     * 编辑商品时新增规格
     * @return string
     */
    public function actionAddSpec()
    {
        $request = Yii::$app->request;
        $productId = $request->post('product_id');
        $product = ShopProductModel::findProductById($productId);
        if (!$product) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        $spec = $request->post('spec');
        if ($spec == '') {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '规格不能为空']);
        }
        $price = (float)$request->post('price');
        $shop = ShopModel::findShopByShopId(Yii::$app->user->identity->shop_id);
        if ($price === '') {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '价格不能为空']);
        }
        if (($shop->single_limit_quota) <  $price) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '价格填写不能大于'.$shop->single_limit_quota.'万元']);
        }
        $stock = (int)$request->post('stock');
        if ($stock === '' || $stock < 0) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '库存不能为空，且为大于等于零的整数']);
        }

        $data = [
            'product_id' => $productId,
            'spec' => $spec,
            'price' => $price,
            'stock' => $stock,
        ];

        $transaction =Yii::$app->db->beginTransaction();
        try{

            // 添加规格
            if (!ShopProSpecModel::add($data)) {
                throw new \Exception('保存失败');
            }

            // 更新库存
            if (!ShopProductModel::updateTotalStock($productId, ShopProSpecModel::calculateStockById($productId))) {
                throw new \Exception('库存更新失败');
            }
            $transaction->commit();

            $result = ShopProductModel::findProductByIdWithSpecAndCategory($productId);
            $specList = [];
            if ($result->proSpec) {
                foreach ($result->proSpec as $list) {
                    $specList[] = [
                        'id' => $list->id,
                        'spec' => $list->spec,
                        'price' => $list->price,
                        'stock' => $list->stock,
                    ];
                }
            }
            return Json::encode([
                'status' => self::STATUS_SUCCESS,
                'error_message' => '添加成功',
                'result' => $specList,
            ]);
        }catch (\Exception $e){
            $transaction->rollBack();
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => $e->getMessage()]);
        }
    }

    /**
     * 编辑商品时修改规格
     * @return string
     */
    public function actionUpdateSpec()
    {
        $request = Yii::$app->request;
        $proSpecId= $request->get('spec_id');
        $proSpec = ShopProSpecModel::findShopProSpecById($proSpecId);
        if (!$proSpec) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        $spec = $request->post('spec');
        if ($spec == '') {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '规格不能为空']);
        }
        $price = (float)$request->post('price');

        $shop = ShopModel::findShopByShopId(Yii::$app->user->identity->shop_id);
        if ($price === '') {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '价格不能为空']);
        }
        if (($shop->single_limit_quota) <  $price) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '价格填写不能大于'.$shop->single_limit_quota.'万元']);
        }
        $stock = (int)$request->post('stock');
        if ($stock === '' || $stock < 0) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '库存不能为空，且为大于等于零的整数']);
        }
        $data = [
            'spec' => $spec,
            'price' => $price,
            'stock' => $stock,
        ];
        $transaction =Yii::$app->db->beginTransaction();
        try{

            // 编辑规格
            if (!ShopProSpecModel::update($proSpecId,$data)) {
                throw new \Exception('编辑失败');
            }

            // 更新库存
            if ($proSpec->stock !== $stock) {
                if (!ShopProductModel::updateTotalStock($proSpec->product_id, ShopProSpecModel::calculateStockById($proSpec->product_id))) {
                    throw new \Exception('库存更新失败');
                }
            }

            $transaction->commit();

            return Json::encode([
                'status' => self::STATUS_SUCCESS,
                'error_message' => '编辑成功'
            ]);
        }catch (\Exception $e){
            $transaction->rollBack();
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => $e->getMessage()]);
        }
    }

    /**
     * 删除商品的一条规格
     * @return string
     */
    public function actionDelSpec()
    {
        $request = Yii::$app->request;
        $proSpecId = $request->post('pro_spec_id');


        $proSpec = ShopProSpecModel::findShopProSpecById($proSpecId);
        if (!$proSpecId) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        $count = ShopProSpecModel::countProSpecByProId($proSpec->product_id);
        if ($count < 2) { // 当前规格为最后一条数据不给与删除操作
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '不允许执行删除操作']);
        }
        $transaction =Yii::$app->db->beginTransaction();
        try{

            // 删除规格
            if (!ShopProSpecModel::delById($proSpecId)) {
                throw new \Exception('删除失败');
            }

            // 更新库存
            if (!ShopProductModel::updateTotalStock($proSpec->product_id, ShopProSpecModel::calculateStockById($proSpec->product_id))) {
                throw new \Exception('库存更新失败');
            }

            $transaction->commit();
            return Json::encode([
                'status' => self::STATUS_SUCCESS,
                'error_message' => '删除成功'
            ]);
        }catch (\Exception $e){
            $transaction->rollBack();
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => $e->getMessage()]);
        }
    }

    /**
     * 商户商品列表
     * @return string
     */
    public function actionList()
    {
        $result = $data = [];
        $request = Yii::$app->request;
        $shopId = Yii::$app->user->identity->shop_id;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $state = $request->get('state',''); // 审核状态
        $title = $request->get('title',''); // 商品名称
        $categoryId = (int)$request->get('category',''); // 商品分类id
        $no = $request->get('no',''); // 商品货号
        $onSale = $request->get('on_sale',''); // 上下架
        $beginAt = $request->get('start_at', ''); // 添加始时间
        $endAt = $request->get('end_at', ''); // 截止时间
        $result = ShopProductModel::getShopProductListByShopId($offset, $limit, $shopId, $title, $no, $categoryId, $beginAt, $endAt, $state, $onSale);
        foreach ($result['result'] as $row) {
            $data[] = [
                'id' => $row->id,
                'category' => $row->category->title ?? '',
                'title' => $row->title,
                'no' => $row->no,
                'sort' => $row->sort,
                'on_sale' => $row->on_sale,
                'sale' => $row->sale,
                'total_stock' => $row->total_stock,
                'state' => $row->state,
                'audited_at' => $row->audited_at,
                'created_at' => $row->created_at,
            ];
        }
        // 统计信息 全部商品 ，已上架， 未上架 ，待审核，不通过
        $allCount = ShopProductModel::countByCondition(['shop_id' => $shopId]);
        $onSaleCount = ShopProductModel::countByCondition(['shop_id' => $shopId, 'on_sale' => ShopProductModel::IS_ON_SALE]);
        $waitCount = ShopProductModel::countByCondition(['shop_id' => $shopId, 'state' => ShopProductModel::STATE_AUDITING]);
        $noPassCount = ShopProductModel::countByCondition(['shop_id' => $shopId, 'state' => ShopProductModel::STATE_AUDIT_NOPASS]);
        $count = [
            'allCount' =>$allCount ?? 0,
            'onSaleCount' => $onSaleCount ?? 0,
            'waitCount' => $waitCount ?? 0,
            'noPassCount' => $noPassCount ?? 0,
        ];
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $result['count'],
            'results' => $data,
            'countList' => $count
        ]);
    }


    /**
     * 商品上下架
     * @return string
     */
    public function actionOnSale()
    {
        $request = Yii::$app->request;
        $proId = $request->get('id', '');
        $pro = ShopProductModel::findProductById($proId);
        if (!$pro) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        if ($pro->state != ShopProductModel::STATE_AUDIT_PASS) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '商品未通过审核不能进行上下架操作']);
        }

        if ($pro->on_sale == ShopProductModel::IS_ON_SALE) {
            $pro->on_sale = ShopProductModel::IS_NOT_ON_SALE;
        } else {
            $pro->on_sale = ShopProductModel::IS_ON_SALE;
        }
        if (!$pro->save()) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '操作失败']);
        }
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '操作成功']);
    }

}