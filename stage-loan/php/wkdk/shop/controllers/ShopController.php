<?php
/**
 * Created by PhpStorm.
 * User: lzh
 * Date: 2018/1/8
 * Time: 15:26
 */

namespace shop\controllers;


use common\models\LoanModel;
use common\models\ShopAdminModel;
use Yii;
use yii\helpers\Json;
use common\models\CategoryModel;
use common\models\ShopModel;
use common\models\ShopProductModel;
use shop\bases\ShopBackendController;

class ShopController extends ShopBackendController
{
    /**
     * 保存店铺管理修改
     * @return string
     */
    public function actionDecorate()
    {
        $request = Yii::$app->request;
        $logo = $request->post('logo', '');
        if (empty($logo)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请上传店铺logo']);
        }
        $picArr = $request->post('picArr', '');
        if (!empty($picArr) && is_array($picArr)) {
            $pic = implode(',', $picArr);
        } else {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请上传1~5张店铺图片']);
        }
        $intro = $request->post('intro', '');
        $data = [
            'logo' => $logo,
            'shop_pic' => $pic ?? '',
            'intro' => $intro
        ];
        $result = ShopModel::update(Yii::$app->user->identity->shop_id, $data);
        if (!$result) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '保存失败']);
        }
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '保存成功']);
    }

    /**
     * 获取店铺管理详情
     * @return string
     */
    public function actionDetail()
    {
        $shop = ShopModel::findShopByShopId(Yii::$app->user->identity->shop_id);
        if (!$shop) {
            return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '商户不存在']);
        }

        $data = [
            'logo' => $shop->logo,
            'intro' => $shop->intro,
        ];
        // 图片
        $picArr = [];
        if ($shop->shop_pic) {
            $picArr = explode(',', $shop->shop_pic);
        }
        $data['picArr'] = $picArr;
        // 商品分类
        $categoryList = [];
        if ($shop->product_category) {
            $arr = explode(',', $shop->product_category);
            $categoryList = CategoryModel::getProductCategoryByIds($arr);
            if (!empty($categoryList)) {
                foreach ($categoryList as $row) {
                    $category[] = $row->title;
                }
            }
        }
        $data['category'] = $category ?? [];
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'result' => $data
        ]);
    }

    /**
     * 商户主页信息
     * @return string
     */
    public function actionIndex()
    {

        $shop = ShopModel::findShopByShopId(Yii::$app->user->identity->shop_id); // 获取商户
        // 商品统计
        $allCount = ShopProductModel::countByCondition(['shop_id' => $shop->id]); // 全部商品
        $onSaleCount = ShopProductModel::countByCondition(['shop_id' => $shop->id, 'on_sale' => ShopProductModel::IS_ON_SALE]); // 已上架

        // 商户订单统计 今日销售 笔数 额数 累计销售额数
        $statShopQuotaByDate = LoanModel::statShopQuotaByDate(date('Y-m-d'), $shop->id);
        $statShopTotalQuota = LoanModel::statShopTotalQuota($shop->id);

        // 商户用户数统计
        $userCount = LoanModel::countShopUser($shop->id);
        $data = [
            'shop_name' => $shop->shop_name,
            'state' => $shop->state,
            'shop_no' => $shop->shop_no,
            'total_quota' => $shop->total_quota, // 总授信额度
            'daily_limit_quota' => $shop->daily_limit_quota, // 今日限额
            'single_limit_quota' => $shop->single_limit_quota, // 单笔限额
            'today_sale' => $statShopQuotaByDate['quota'] ?? 0,//--今日销售金额
            'today_count' => $statShopQuotaByDate['count'] ?? 0,//--今日销售笔数
            'statistics_sale' => $statShopTotalQuota ?? 0,//--累计销售金额
            'all_count' => $allCount, // 全部商品
            'on_sale_count' => $onSaleCount, // 已上架
            'user_count' => $userCount // 用户统计
        ];
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'results' => $data
        ]);
    }

    /**
     * 商户修改管理密码
     * @return string
     */
    public function actionPassword()
    {
        $currentUser = Yii::$app->user->identity;
        $request = Yii::$app->request;
        $model = new ShopAdminModel();

        $oldPwd = trim($request->post('old_password', '')); // 当前密码
        $newPwd = trim($request->post('new_password', '')); // 新密码
        $rePwd = trim($request->post('repeat_password', '')); // 重复密码

        if (!$oldPwd || !$newPwd || !$rePwd) {
            return Json::encode(["status" => self::STATUS_FAILURE, "error_message" => "请填写当前密码,新密码,确认密码"]);
        }
        if (mb_strlen($newPwd) < 6 || mb_strlen($newPwd) > 15) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请使用6~15位数字、字母、特殊符号组合']);
        }
        if ($newPwd != $rePwd) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '新密码与确认密码不一致']);
        }
        if ( !$model->validatePassword($currentUser, $oldPwd)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '当前密码错误，请重试']);
        }
        $result = $model->update($currentUser->id, $rePwd); // 修改密码
        if ($result) {
            setcookie(session_name(),'',time()-1,'/'); // 清除登陆信息
            return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '密码修改成功']);
        }
        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '修改失败，请重试']);
    }

}