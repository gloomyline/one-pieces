<?php
namespace frontend\controllers;
use common\models\CategoryModel;
use common\models\CityModel;
use common\models\LimuAreaModel;
use common\models\ShopModel;
use frontend\bases\FrontendController;
use Yii;
use yii\helpers\Json;

class StaticConfigController extends FrontendController
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
    public function actionGetStaticConfig()
    {
        $request = Yii::$app->request;
        // type 取值【limuHouseFundAreas】、【shopAreas】、【shops】、【category】、【limuSocialSecurityAreas】
        $range = ['limuHouseFundAreas', 'shopAreas', 'shops', 'category', 'limuSocialSecurityAreas'];
        $configType = $request->get('type', '');
        if (empty($configType)) {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                'error_message' => '查询的类型不能为空',
            ]);
        }
        if (!in_array($configType, $range)) {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                'error_message' => '非法的类型',
            ]);
        }

        return $this->$configType();
    }

    // 公积金地区列表
    private function limuHouseFundAreas()
    {
        $result = LimuAreaModel::getActiveLimuArea(['state' =>LimuAreaModel::STATE_ACTIVE]);
        foreach ($result as $k => $v) {
            $data[] = [
                'area_code' => $v['area_code'],
                'area_name' => $v['area_name'],
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'error_message' => '',
            "results" => $data ?? []
        ]);
    }

    // 商户所在地
    private function shopAreas()
    {
        $result = CityModel::getOpenCity();

        $data[] = [
            'id' => 0,
            'name' => '全部',
        ];
        foreach ($result as $k => $v) {
            $data[] = [
                'id' => $v['id'],
                'name' => $v['name'],
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'error_message' =>'',
            "results" => $data ?? []
        ]);
}

    // 商户名称
    private function shops()
    {
        $result = ShopModel::getAllShop(0, -1);
        foreach($result as $k => $v) {
            if ($v['state'] == ShopModel::STATE_AUDIT_PASS) {
                $data[] = [
                    'shop_no' => $v['shop_no'], // 商户号
                    'shop_name' => $v['shop_name'], // 商户名称
                ];
            }
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'error_message' =>'',
            "results" => $data ?? []
        ]);
    }

    /**
     * 获取所有分类
     * @return string
     */
    private function category()
    {
        $data = [];
        $category = CategoryModel::getChildrenList((integer)0, (integer)-1, (integer)0); // 获取所有一级分类
        foreach ($category['result'] as $v) {
            // 分类是否显示
            if ($v['is_show']) {
                $data[] = [
                    'id' => $v['id'],
                    'title' => $v['title'],
                ];
            }
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            "error_message" => "",
            'results' => $data ?? [],
        ]);
    }

    // 社保地区列表
    private function limuSocialSecurityAreas()
    {
        $result = LimuAreaModel::getActiveLimuArea(['is_social_security' => LimuAreaModel::SOCIAL_SECURITY_SUPPORT]);
        foreach ($result as $k => $v) {
            $data[] = [
                'area_code' => $v['area_code'],
                'area_name' => $v['area_name'],
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'error_message' => '',
            "results" => $data ?? []
        ]);
    }

}