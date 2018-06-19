<?php
/**
 * Created by PhpStorm.
 * User: lzh
 * Date: 2017/12/22
 * Time: 10:29
 */

namespace backend\controllers;

use common\models\CityModel;
use common\models\ProductModel;
use common\models\ProvinceModel;
use common\models\ShopModel;
use common\models\ShopProductModel;
use common\models\ShopSettledModel;
use common\services\SearchService;
use common\services\SiteService;
use Yii;
use backend\bases\BackendController;
use common\models\CategoryModel;
use yii\helpers\Json;

class ShopController extends BackendController
{
    /**
     * 商户申请
     * @return string
     */
    public function actionApply()
    {
        $request = Yii::$app->request;

        $shopName = $request->post('shop_name', '');
        if (!$shopName) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '商户名称不能为空！']);
        }
        $legalPersonName = $request->post('legal_person_name', '');
        if (!$legalPersonName) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '企业法人姓名不能为空！']);
        }
        $legalPersonMobile = $request->post('legal_person_mobile', '');
        if (!preg_match('/0?(13|14|15|17|18)[0-9]{9}/', $legalPersonMobile)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '企业法人手机号填写有误！请重新填写再提交']);
        }

        $legalPersonIdNo = $request->post('legal_person_id_no', '');
        if (!preg_match('/\d{17}[\d|x|X]|\d{15}/', $legalPersonIdNo)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '企业法人身份证号填写有误！请重新填写再提交']);
        }

        $legalPersonIdCardsPicFront = $request->post('legal_person_id_card_pic_front', '');
        if (!$legalPersonIdCardsPicFront) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请上传法人身份证正面照！']);
        }
        $legalPersonIdCardsPicBack = $request->post('legal_person_id_card_pic_back', '');
        if (!$legalPersonIdCardsPicBack) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请上传法人身份证反面照！']);
        }

        $legalPersonIdCardsPic = $legalPersonIdCardsPicFront.','.$legalPersonIdCardsPicBack; // 身份证正反面

        $isEq = $request->post('is_eq', '');

        if ($isEq == 'false') { // 实际控制人与法人不相等
            $actualControllerName = $request->post('actual_controller_name', '');
            if (!$actualControllerName) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '实际控制人姓名不能为空！']);
            }
            $actualControllerMobile = $request->post('actual_controller_mobile', '');
            if (!preg_match('/0?(13|14|15|17|18)[0-9]{9}/', $actualControllerMobile)) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '实际控制人手机号填写有误！请重新填写再提交']);
            }
            $actualControllerIdNo = $request->post('actual_controller_id_no', '');
            if (!preg_match('/\d{17}[\d|x|X]|\d{15}/', $actualControllerIdNo)) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '实际控制人身份证号填写有误！请重新填写再提交']);
            }
            $actualControllerIdCardsPicFront = $request->post('actual_controller_id_card_pic_front', '');
            if (!$actualControllerIdCardsPicFront) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请上传实际控制人身份证正面照！']);
            }
            $actualControllerIdCardsPicBack = $request->post('actual_controller_id_card_pic_back', '');
            if (!$actualControllerIdCardsPicBack) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请上传法人身份证反面照！']);
            }
            $actualControllerIdCardsPic = $actualControllerIdCardsPicFront.','.$actualControllerIdCardsPicBack;
        } else { // 一致
            $actualControllerName = $legalPersonName;
            $actualControllerMobile = $legalPersonMobile;

            $actualControllerIdNo = $legalPersonIdNo;

            $actualControllerIdCardsPic = $legalPersonIdCardsPic;
        }
        $corporateContactsEmail = $request->post('corporate_contacts_email', ''); // 邮件格式
        if ($corporateContactsEmail) {
            if (!preg_match('/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/', $corporateContactsEmail))
            {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '企业联系人电子邮箱格式有误！请重新填写再提交']);
            }
        }

        $corporateContactsMobile = $request->post('corporate_contacts_mobile', ''); // 手机格式校验
        if ($corporateContactsMobile) {
            if (!preg_match('/0?(13|14|15|17|18)[0-9]{9}/', $corporateContactsMobile)) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '企业联系人手机号填写有误！请重新填写再提交']);
            }
        }

        $bankName = trim($request->post('bank_name', ''));
        if (!$bankName) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请填写银行卡名称']);
        }

        $bankNo = trim($request->post('bank_no', ''));
        if (!$bankNo) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请填写打款银行卡号']);
        }
        if (!preg_match('/^[0-9]{15,20}$/', $bankNo)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请填写正确的打款银行卡号']);
        }
        $shopAddr = $request->post('shop_addr', '');

        $threeCardsPic = $request->post('three_cards_pic', '');

        if ($threeCardsPic && is_array($threeCardsPic)) {
            if (count($threeCardsPic) > 3) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '企业三证最多只能上传3张图片']);
            }
            $threeCardsPics = implode(',', $threeCardsPic);
        }

        $registeredCapital = $request->post('registered_capital', 0); // 注册资金

        $registerAtYear = intval($request->post('registered_at_year', ''));
        $registerAtMonth = intval($request->post('registered_at_month', ''));
        if (!$registerAtYear || !$registerAtMonth) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请选择企业的注册时间']);
        }
        $registeredAt = $registerAtYear . '-' . $registerAtMonth;

        $officeArea = $request->post('office_area', 0); // 办公面积

        $staffNo = $request->post('staff_no', 0); // 职工人数

        $corporateOfficePic = $request->post('corporate_office_pic', ''); // 最多8
        if ($corporateOfficePic  && is_array($corporateOfficePic )) {
            if (count($corporateOfficePic ) > 8) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '企业办公场所照片及房产租赁协议最多只能上传8张图片']);
            }
            $corporateOfficePics = implode(',', $corporateOfficePic);
        }

        $salesmanLogoPic = $request->post('salesman_logo_pic', ''); // 1

        $qualificationPic = $request->post('qualification_pic', ''); // 1
        if ($qualificationPic  && is_array($qualificationPic)) {
            if (count($qualificationPic) > 3) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '企业相关资质照片最多只能上传3张图片']);
            }
            $qualificationPics = implode(',', $qualificationPic);
        }

        $holderNoRemark = $request->post('holder_no_remark', ''); // 1

        $protocolPic = $request->post('protocol_pic', ''); // 1

        $authorizationPic = $request->post('authorization_pic', ''); // 1

        $commitmentPic = $request->post('commitment_pic', ''); // 1

        $bankBillPic = $request->post('bank_bill_pic', ''); // 50
        if ($bankBillPic  && is_array($bankBillPic )) {
            if (count($bankBillPic ) > 50) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '网银流水图片最多只能上传50张图片']);
            }
            $bankBillPics = implode(',', $bankBillPic);
        }

        $category = $request->post('category', '');
        $shopCategory = $proCategory = '';
        if (empty($category)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请选择商户可分期商品类别']);
        }
        if ($category && is_array($category)) {
            if (count($category) != count($category,1)) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请确保分类已选择，再保存']);
            }
            // 获取商品分类的信息
            $categoryList = CategoryModel::getProductCategoryByIds($category);
            foreach ($categoryList as $row) {
                $proCategory[] = $row->id; // 商品分类
                $shopCategory[] = $row->parent_id; // 商户分类
            }
            $shopCategory = array_unique($shopCategory);
            sort($shopCategory);
            $proCategoryIds = implode(',', $proCategory);
            $shopCategoryIds = implode(',', $shopCategory);
        }
        $period= $request->post('period', '');
        if (empty($period)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请勾选商户可分期数']);
        }
        if ($period && is_array($period)) {
            sort($period);
            $period = implode(',', $period);
        }

        $cityId = $request->post('city_id', '');
        if (empty($cityId)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请选择商户所在地']);
        }
        $salesmanId = Yii::$app->user->id;

        $data = [
            'shop_name' => $shopName,
            'legal_person_name' => $legalPersonName,
            'legal_person_mobile' => $legalPersonMobile,
            'legal_person_id_no' => $legalPersonIdNo,
            'legal_person_id_card_pic' => $legalPersonIdCardsPic,
            'is_eq' => $isEq == 'true' ? 1 : 0,
            'actual_controller_name' => $actualControllerName ?? '',
            'actual_controller_mobile' => $actualControllerMobile ?? '',
            'actual_controller_id_no' => $actualControllerIdNo ?? '',
            'actual_controller_id_card_pic' => $actualControllerIdCardsPic ?? '',
            'corporate_contacts_email' => $corporateContactsEmail,
            'corporate_contacts_mobile' => $corporateContactsMobile,
            'bank_name' => $bankName,
            'bank_no' => $bankNo,
            'shop_addr' => $shopAddr,
            'three_cards_pic' => $threeCardsPics ?? '',
            'registered_capital' => $registeredCapital ?? 0,
            'registered_at' => $registeredAt,
            'office_area' => $officeArea,
            'staff_no' => $staffNo,
            'corporate_office_pic' => $corporateOfficePics ?? '',
            'salesman_logo_pic' => $salesmanLogoPic[0] ?? '',
            'qualification_pic' => $qualificationPics ?? '',
            'holder_no_remark' => $holderNoRemark,
            'protocol_pic' => $protocolPic[0] ?? '',
            'authorization_pic' => $authorizationPic[0] ?? '',
            'commitment_pic' => $commitmentPic[0] ?? '',
            'bank_bill_pic' => $bankBillPics ?? '',
            'shop_category' => $shopCategoryIds ?? '',
            'product_category' => $proCategoryIds ?? '',
            'city_id' => $cityId[1] ?? 0,
            'period' => $period ?? '',
            'salesman_id' => $salesmanId,
            'state' => ShopModel::STATE_AUDITING,
            'shop_no' => '',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $result = ShopModel::add($data);
        if (!$result) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '保存失败']);
        }
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    /**
     * 编辑商户信息
     * @return string
     */
    public function actionUpdate()
    {
        $request = Yii::$app->request;
        $shopId = $request->get('shop_id', 0);
        $shop = ShopModel::findShopByShopId($shopId);
        if (!$shop) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '您所编辑的商户不存在，请重试！']);
        }
        $shopName = $request->post('shop_name', '');
        if (!$shopName) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '商户名称不能为空！']);
        }
        $legalPersonName = $request->post('legal_person_name', '');
        if (!$legalPersonName) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '企业法人姓名不能为空！']);
        }
        $legalPersonMobile = $request->post('legal_person_mobile', '');
        if (!preg_match('/0?(13|14|15|17|18)[0-9]{9}/', $legalPersonMobile)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '企业法人手机号填写有误！请重新填写再提交']);
        }

        $legalPersonIdNo = $request->post('legal_person_id_no', '');
        if (!preg_match('/\d{17}[\d|x|X]|\d{15}/', $legalPersonIdNo)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '企业法人身份证号填写有误！请重新填写再提交']);
        }

        $legalPersonIdCardsPicFront = $request->post('legal_person_id_card_pic_front', '');
        if (!$legalPersonIdCardsPicFront) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请上传法人身份证正面照！']);
        }
        $legalPersonIdCardsPicBack = $request->post('legal_person_id_card_pic_back', '');
        if (!$legalPersonIdCardsPicBack) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请上传法人身份证反面照！']);
        }

        $legalPersonIdCardsPic = $legalPersonIdCardsPicFront.','.$legalPersonIdCardsPicBack; // 身份证正反面

        $isEq = $request->post('is_eq', '');

        if ($isEq == 'false') { // 实际控制人与法人不相等
            $actualControllerName = $request->post('actual_controller_name', '');
            if (!$actualControllerName) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '实际控制人姓名不能为空！']);
            }
            $actualControllerMobile = $request->post('actual_controller_mobile', '');
            if (!preg_match('/0?(13|14|15|17|18)[0-9]{9}/', $actualControllerMobile)) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '实际控制人手机号填写有误！请重新填写再提交']);
            }
            $actualControllerIdNo = $request->post('actual_controller_id_no', '');
            if (!preg_match('/\d{17}[\d|x|X]|\d{15}/', $actualControllerIdNo)) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '实际控制人身份证号填写有误！请重新填写再提交']);
            }
            $actualControllerIdCardsPicFront = $request->post('actual_controller_id_card_pic_front', '');
            if (!$actualControllerIdCardsPicFront) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请上传实际控制人身份证正面照！']);
            }
            $actualControllerIdCardsPicBack = $request->post('actual_controller_id_card_pic_back', '');
            if (!$actualControllerIdCardsPicBack) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请上传法人身份证反面照！']);
            }
            $actualControllerIdCardsPic = $actualControllerIdCardsPicFront.','.$actualControllerIdCardsPicBack;
        } else { // 一致
            $actualControllerName = $legalPersonName;
            $actualControllerMobile = $legalPersonMobile;

            $actualControllerIdNo = $legalPersonIdNo;

            $actualControllerIdCardsPic = $legalPersonIdCardsPic;
        }

        $corporateContactsEmail = $request->post('corporate_contacts_email', ''); // 邮件格式
        if ($corporateContactsEmail) {
            if (!preg_match('/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/', $corporateContactsEmail))
            {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '企业联系人电子邮箱格式有误！请重新填写再提交']);
            }
        }

        $corporateContactsMobile = $request->post('corporate_contacts_mobile', ''); // 手机格式校验

        if ($corporateContactsMobile) {
            if (!preg_match('/0?(13|14|15|17|18)[0-9]{9}/', $corporateContactsMobile)) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '企业联系人手机号填写有误！请重新填写再提交']);
            }
        }

        $bankName = trim($request->post('bank_name', ''));
        if (!$bankName) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请填写银行卡名称']);
        }

        $bankNo = trim($request->post('bank_no', ''));
        if (!$bankNo) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请填写打款银行卡号']);
        }
        if (!preg_match('/^[0-9]{15,20}$/', $bankNo)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请填写正确的打款银行卡号']);
        }
        $shopAddr = $request->post('shop_addr', '');

        $threeCardsPic = $request->post('three_cards_pic', '');

        if ($threeCardsPic && is_array($threeCardsPic)) {
            if (count($threeCardsPic) > 3) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '企业三证最多只能上传3张图片']);
            }
            $threeCardsPics = implode(',', $threeCardsPic);
        }

        $registeredCapital = $request->post('registered_capital', 0); // 注册资金

        $registerAtYear = intval($request->post('registered_at_year', ''));
        $registerAtMonth = intval($request->post('registered_at_month', ''));
        if (!$registerAtYear || !$registerAtMonth) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请选择企业的注册时间']);
        }
        $registeredAt = $registerAtYear . '-' . $registerAtMonth;

        $officeArea = $request->post('office_area', 0); // 办公面积

        $staffNo = $request->post('staff_no', 0); // 职工人数

        $corporateOfficePic = $request->post('corporate_office_pic', ''); // 最多8
        if ($corporateOfficePic  && is_array($corporateOfficePic )) {
            if (count($corporateOfficePic ) > 8) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '企业办公场所照片及房产租赁协议最多只能上传8张图片']);
            }
            $corporateOfficePics = implode(',', $corporateOfficePic);
        }

        $salesmanLogoPic = $request->post('salesman_logo_pic', ''); // 1

        $qualificationPic = $request->post('qualification_pic', ''); // 1
        if ($qualificationPic  && is_array($qualificationPic)) {
            if (count($qualificationPic) > 3) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '企业相关资质照片最多只能上传3张图片']);
            }
            $qualificationPics = implode(',', $qualificationPic);
        }

        $holderNoRemark = $request->post('holder_no_remark', ''); // 1

        $protocolPic = $request->post('protocol_pic', ''); // 1

        $authorizationPic = $request->post('authorization_pic', ''); // 1

        $commitmentPic = $request->post('commitment_pic', ''); // 1

        $bankBillPic = $request->post('bank_bill_pic', ''); // 50
        if ($bankBillPic  && is_array($bankBillPic )) {
            if (count($bankBillPic ) > 50) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '网银流水图片最多只能上传50张图片']);
            }
            $bankBillPics = implode(',', $bankBillPic);
        }

        $category = $request->post('category', '');
        $shopCategory = $proCategory = '';
        if (empty($category)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请选择商户可分期商品类别']);
        }
        if ($category && is_array($category)) {
            if (count($category) != count($category,1)) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请确保分类已选择，再保存']);
            }

            // 判断商品分类是否可以不选
            $oldProductCategory = ShopProductModel::getShopProductCategoryByShopId($shopId);
            foreach ($oldProductCategory as $row) {
                if ($row['category'] && !in_array($row['category_id'], $category)) {
                    return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '商户可分期商品类别:' . $row['category']['title'] . '为必选商户分期分类']);
                }
            }

            // 获取商品分类的信息
            $categoryList = CategoryModel::getProductCategoryByIds($category);
            foreach ($categoryList as $row) {
                $proCategory[] = $row->id; // 商品分类
                $shopCategory[] = $row->parent_id; // 商户分类
            }
            $shopCategory = array_unique($shopCategory);
            sort($shopCategory);
            $proCategoryIds = implode(',', $proCategory);
            $shopCategoryIds = implode(',', $shopCategory);
        }

        $period= $request->post('period', '');
        if (empty($period)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请勾选商户可分期数']);
        }
        if ($period && is_array($period)) {
            sort($period);
            $period = implode(',', $period);
        }

        $cityId = $request->post('city_id', '');
        if (empty($cityId)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请选择商户所在地']);
        }
        $data = [
            'shop_name' => $shopName,
            'legal_person_name' => $legalPersonName,
            'legal_person_mobile' => $legalPersonMobile,
            'legal_person_id_no' => $legalPersonIdNo,
            'legal_person_id_card_pic' => $legalPersonIdCardsPic,
            'is_eq' => $isEq == 'true' ? 1 : 0,
            'actual_controller_name' => $actualControllerName ?? '',
            'actual_controller_mobile' => $actualControllerMobile ?? '',
            'actual_controller_id_no' => $actualControllerIdNo ?? '',
            'actual_controller_id_card_pic' => $actualControllerIdCardsPic ?? '',
            'corporate_contacts_email' => $corporateContactsEmail,
            'corporate_contacts_mobile' => $corporateContactsMobile,
            'bank_name' => $bankName,
            'bank_no' => $bankNo,
            'shop_addr' => $shopAddr,
            'three_cards_pic' => $threeCardsPics ?? '',
            'registered_capital' => $registeredCapital ?? 0,
            'registered_at' => $registeredAt,
            'office_area' => $officeArea,
            'staff_no' => $staffNo,
            'corporate_office_pic' => $corporateOfficePics ?? '',
            'salesman_logo_pic' => $salesmanLogoPic[0] ?? '',
            'qualification_pic' => $qualificationPics ?? '',
            'holder_no_remark' => $holderNoRemark,
            'protocol_pic' => $protocolPic[0] ?? '',
            'authorization_pic' => $authorizationPic[0] ?? '',
            'commitment_pic' => $commitmentPic[0] ?? '',
            'bank_bill_pic' => $bankBillPics ?? '',
            'shop_category' => $shopCategoryIds ?? '',
            'product_category' => $proCategoryIds ?? '',
            'city_id' => $cityId[1] ?? 0,
            'period' => $period ?? '',
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $oldCityId = $shop->city_id;
        $result = ShopModel::update($shop->id, $data);

        if (!$result) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '保存失败']);
        }

        // 若所在城市有变更需开通该城市
        if (!empty($shop->city_id) && $result->city_id != $oldCityId) {
            $city = CityModel::findCityByCityId($result->city_id);
            if ($city) {
                CityModel::setOpenCityByCityId($result->city_id);
            }
        }
        // 更新xunsearch的索引，审核通过的商户才执行
        if ($result->state == ShopModel::STATE_AUDIT_PASS) {
            $shopCategory = '';
            if (isset($result->shop_category)) {
                $arr = explode(',', $result->shop_category);
                $shopCategory = SiteService::implodeBySpecialSign($arr);
            }
            try {
                SearchService::updateShopDocById($result->id, $result->shop_name, $result->city_id, $shopCategory);
            } catch (\XSErrorException $e) {
                return Json::encode([
                    'status' => self::STATUS_FAILURE,
                    "error_message" => "服务器异常，请检查xunSearch服务是否开启",
                ]);
            }
        }
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    /**
     * 商户详情信息
     * @return string
     */
    public function actionDetail()
    {
        $request = Yii::$app->request;
        $shopId = $request->get('shop_id', '');
        $result = ShopModel::findShopByShopId($shopId);
        if (!$result) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数有误']);
        }
        if ($result->legal_person_id_card_pic) {
            $legalPersonIdCardPic = explode(',', $result->legal_person_id_card_pic);
        }
        if ($result->actual_controller_id_card_pic) {
            $actualControllerIdCardPic = explode(',', $result->actual_controller_id_card_pic);
        }
        if (!empty($result->three_cards_pic)) {
            $threeCardsPic = explode(',', $result->three_cards_pic);
            foreach ($threeCardsPic as $v) {
                $threeCardsPics[] = [
                  'url' => $v,
                ];
            }
        }
        if ($result->registered_at) {
            $registeredAt = explode('-', $result->registered_at);
        }
        if (!empty($result->corporate_office_pic)) {
            $corporateOfficePic = explode(',', $result->corporate_office_pic);
            foreach ($corporateOfficePic as $v) {
                $corporateOfficePics[] = [
                    'url' => $v,
                ];
            }
        }
        if (!empty($result->bank_bill_pic)) {
            $bankBillPic = explode(',', $result->bank_bill_pic);
            foreach ($bankBillPic as $v) {
                $bankBillPics[] = [
                    'url' => $v,
                ];
            }
        }
        // 分类
        $categoryList = [];
        if ($result->shop_category) {
            $arr = explode(',', $result->shop_category);
            $proCategory = [];
            if ($result->product_category) {
                $proCategory = explode(',', $result->product_category);
            }
            $categoryList = CategoryModel::getShopCategoryWithChildrenByIds($arr);
            if (!empty($categoryList)) {
                $i = 0;
                foreach ($categoryList as $row) {
                    $category[$i] = [
                        'id' => (string)$row->id,
                        'parent_id' => 0,
                        'label' => $row->title,
                    ];
                    $i++;
                    if (!empty($row->children)) {
                        foreach ($row->children as $r) {
                            if (in_array($r->id, $proCategory)) {
                                $category[$i] = [
                                    'id' => (string)$r->id,
                                    'parent_id' => (string)$row->id,
                                    'label' => $r->title,
                                ];
                                $i++;
                            }
                        }
                    }
                }
            }
        }

        // 只需商品分类
        if ($result->shop_category && $result->product_category) {
           $currentCheckArr = $proCategory;
        }
        // 城市
        if ($result->city_id) {
            $city = CityModel::findCityByCityId($result->city_id);
            if ($city) {
                $cityId[0] = (string)$city->province_id; // 省
                $cityId[1] = (string)$city->id; // 市
                $shopArea = ($city->province->name ?? '') . '/' . ($city->name ?? '');
            }
        }
        // 商户已选分期
        $period = [];
        if ($result->period) {
            $period = explode(',', $result->period);
        }
        $data = [
            'shop_name' =>  $result->shop_name, // 商户名称
            'legal_person_name' => $result->legal_person_name, // 企业法人姓名
            'legal_person_mobile' => $result->legal_person_mobile, // 企业法人手机号
            'legal_person_id_no' => $result->legal_person_id_no, // 企业法人身份证号
            'legal_person_id_card_pic_front' => $legalPersonIdCardPic[0] ?? '',// 企业法身份证正面照
            'legal_person_id_card_pic_back' => $legalPersonIdCardPic[1] ?? '', // 企业法人身份证
            'is_eq' => $result->is_eq == 0 ? false : true, // 实际控制人与法人一致
            'actual_controller_name' => $result->actual_controller_name, // 企业法人姓名
            'actual_controller_mobile' => $result->actual_controller_mobile, // 企业法人手机号
            'actual_controller_id_no' =>$result->actual_controller_id_no, // 企业法人身份证号
            'actual_controller_id_card_pic_front' => $actualControllerIdCardPic[0] ?? '', // 企业法身份证正面照
            'actual_controller_id_card_pic_back' => $actualControllerIdCardPic[1] ?? '', // 企业法人身份证
            'corporate_contacts_email' => $result->corporate_contacts_email, // 企业联系邮箱
            'corporate_contacts_mobile' => $result->corporate_contacts_mobile, // 企业联系手机号
            'bank_name' => $result->bank_name, // 银行卡名称
            'bank_no' => $result->bank_no, // 打款银行卡号
            'shop_addr' => $result->shop_addr, // 企业详细地址
            'three_cards_pic' => $threeCardsPics ?? [], // 企业三证
            'registered_capital' => $result->registered_capital, // 注册资金
            'registered_at_year' => $registeredAt[0] ?? '', // 注册年
            'registered_at_month' => $registeredAt[1] ??'', // 注册月
            'office_area' => $result->office_area, // 办公面积
            'staff_no' => $result->staff_no, // 职工人数
            'corporate_office_pic' => $corporateOfficePics ?? [], // 企业办公环境以及房屋租赁
            'salesman_logo_pic' => $result->salesman_logo_pic ? [['url' => $result->salesman_logo_pic]] : [], // 业务经理与公司logo合照
            'qualification_pic' => $result->qualification_pic ? [['url' => $result->qualification_pic]] : [],
            'holder_no_remark' => $result->holder_no_remark,
            'protocol_pic' => $result->protocol_pic ? [['url' =>$result->protocol_pic]] : [],
            'authorization_pic' => $result->authorization_pic ? [['url' =>$result->authorization_pic]] :[],
            'commitment_pic' => $result->commitment_pic ? [['url' =>$result->commitment_pic]] : [],
            'bank_bill_pic' => $bankBillPics ?? [],
            'current_checked_category' => $category ?? [], // 当前的商户分类
            'city_id' => $cityId ?? [],
            'period' => $period,
            'shop_area' => $shopArea ?? '',
            'shop_no' => $result->shop_no ? $result->shop_no : ShopModel::SHOP_NO_BEGIN + $result->id,
            // 审核信息 审核结果 审核意见 总额度 单笔限额 单日限额
            'state' => $result->state ?? 0,
            'opinion' => $result->opinion ?? '',
            'total_quota' => $result->total_quota ? number_format($result->total_quota, 2) : 0,
            'single_limit_quota' => $result->single_limit_quota ? number_format($result->single_limit_quota, 2) : 0,
            'daily_limit_quota' => $result->daily_limit_quota ? number_format($result->daily_limit_quota, 2) : 0,
            'category' => $currentCheckArr ?? [] // 当前商户分类id
        ];
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'results' => $data
        ]);
    }

    /**
     * 商户审核操作
     * @return string
     */
    public function actionAudit()
    {
        $request = Yii::$app->request;

        $shopId = $request->get('shop_id', '');
        $state = $request->post('state', '');
        if (!$state) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请选择审核结果']);
        }
        $opinion = $request->post('opinion', '');
        $shop = ShopModel::findShopByShopId($shopId);

        $shop->state = $state;
        $shop->opinion = $opinion;
        $shop->auditor_id = Yii::$app->user->id; // 审核人
        $shop->audit_updated_at = date('Y-m-d H:i:s'); // 审核更新时间
        // 审核通过保存额度
        if ($state == ShopModel::STATE_AUDIT_PASS) {

            $totalQuota = $request->post('total_quota', '');
            if (!$totalQuota) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请填写总授信额度！']);
            }
            $singleLimitQuota = $request->post('single_limit_quota', '');
            if (!$singleLimitQuota) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请填单笔限额！']);
            }
            $dailyLimitQuota = $request->post('daily_limit_quota', '');
            if (!$dailyLimitQuota) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请填单日限额！']);
            }
            if ($singleLimitQuota > $totalQuota || $dailyLimitQuota > $totalQuota) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '每日受限总额和单笔受限总额填写均不能大于总授信额度！']);
            }
            if ($singleLimitQuota > $dailyLimitQuota) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '单笔限额填写不能大于每日限额！']);
            }
            $shop->shop_no = (string)(ShopModel::SHOP_NO_BEGIN + $shopId);
            $shop->total_quota = $shop->init_total_quota = $shop->available_quota = $totalQuota * 10000;
            $shop->single_limit_quota = $singleLimitQuota * 10000; // 单笔限额
            $shop->daily_limit_quota = $shop->daily_available_quota = $dailyLimitQuota * 10000; // 每日限额
        }

        $shopCategory = '';
        if (isset($shop->shop_category)) {
            $arr = explode(',', $shop->shop_category);
            $shopCategory = SiteService::implodeBySpecialSign($arr);
        }
        if (!$shop->save()) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '保存失败']);
        }
        // 开通所在城市
        if (!empty($shop->city_id)) {
            $city = CityModel::findCityByCityId($shop->city_id);
            if ($city) {
                CityModel::setOpenCityByCityId($city->id);
            }
        }
        // 插入全文检索数据
        // 商户id，商户名称，商户所在城市， 商户分类 [1],[2]
        try {
            SearchService::createShopDoc($shop->id, $shop->shop_name, $shop->city_id, $shopCategory);
        } catch (\XSErrorException $e) {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                "error_message" => "服务器异常，请检查xunSearch服务是否开启",
            ]);
        }
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    /**
     * 填写表单使用的接口数据 分类以及省份
     * @return string
     */
    public function actionApi()
    {
        $categoryList = CategoryModel::getCategoryList();
        // 分类数据
        $category = [];
        foreach ($categoryList as $row) {
            $category[$row->title]['label'] = $row->title;
            if ($row->children) {
                foreach ($row->children as $r) {
                    $category[$row->title]['options'][] = [
                        'id' => (string)$r->id,
                        'title' => $r->title,
                    ];
                }
            }
        }

        $province = ProvinceModel::getProvinces();
        foreach ($province as $row) {
            $pro[] = [
                'label' => $row->name,
                'value' => (string)$row->id,
                'cities' => [],
            ];
        }
        $product = ProductModel::getProductByCondition(['type' => ProductModel::TYPE_CONSUMPTION, 'active' => ProductModel::STATE_ACTIVE]); // 得到消费分期商品
        $period = [];
        if ($product && $product->period) {
            $period = explode(',', $product->period);
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'results' => [
                'category' => $category ?? [],
                'province' => $pro ?? [],
                'period' => $period,
            ],
        ]);
    }

    /**
     * 根据省获取市
     * @return string
     */
    public function actionCity()
    {
        $request = Yii::$app->request;
        $provinceId = $request->get('id', '');
        if (!$provinceId) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数有误']);
        }

        $result = CityModel::getCityByProvinceId($provinceId);
        foreach ($result as $row) {
            $data[] = [
                'label' => $row->name,
                'value' => (string)$row->id,
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'results' => $data ?? [],
        ]);
    }

    /**
     * 商户申请列表
     * @return string
     */
    public function actionList()
    {

        $results = $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $shopNo = $request->get('shop_no', '');
        $shopName = $request->get('shop_name', '');
        $state = $request->get('state', '');
        $results = ShopModel::getShopList($offset, $limit, $shopName, $shopNo, $state);

        foreach ($results['result'] as $key => $row) {
            $data[$key] = [
                'id' => $row->id,
                'shop_no' => $row->shop_no,
                'shop_name' => $row->shop_name,
                'created_at' => $row->created_at,
                'salesman' => $row->salesman->username ??'',
                'auditor' => $row->auditor->username ?? '',
                'state' => $row->state,
                'audit_updated_at' => $row->audit_updated_at,
                'total_quota' => $row->total_quota / 10000,
                'daily_limit_quota' => $row->daily_limit_quota / 10000,
                'single_limit_quota' => $row->single_limit_quota / 10000,
            ];
            if ($row->shop_category)  {
                $categoryList = CategoryModel::getShopCategoryByIds(explode(',', $row->shop_category));
                if ($categoryList) {
                    foreach ($categoryList as $list) {
                        $data[$key]['category'][] = $list->title;
                    }
                }
            }
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $results['count'],
            'results' => $data
        ]);
    }

    /**
     * 商户入驻
     * @return string
     */
    public function actionSettledList()
    {
        $results = $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $shopName = $request->get('shop_name', '');
        $results = ShopSettledModel::getShopSettledList($offset, $limit, $shopName);

        foreach ($results['result'] as $row) {
            $data[] = [
                'id' => $row->id,
                'shop_name' => $row->shop_name,
                'contacts_name' => $row->contacts_name,
                'contacts_mobile' => $row->contacts_mobile,
                'contacts_addr' => $row->contacts_addr,
                'created_at' => $row->created_at
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $results['count'],
            'results' => $data
        ]);
    }

    /**
     * 删除商户入驻记录
     * @return string
     */
    public function actionDelShopSettled()
    {
        $request = Yii::$app->request;
        $id = (int)$request->get('id', '');
        $shopSettled = ShopSettledModel::finShopSettledById($id);
        if (!$shopSettled) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        if (!ShopSettledModel::delShopSettled($id)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '删除失败']);
        }
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '删除成功']);
    }
}