<?php

namespace backend\controllers;

use Yii;
use yii\helpers\Json;
use common\models\Product;
use common\models\ProductModel;
use backend\bases\BackendController;

class ProductController extends BackendController
{
    /**
     * 查询所有产品配置信息
     * @param integer $offset 查询的基准数 默认【0】
     * @param integer $limit 查询的记录数 默认【20】
     * @return string 返回记录信息
     */
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $productModel = new ProductModel();
        $results = $productModel->getAllProduct($offset, $limit); // 查询产品记录
        $count = Product::find()->count(); // 记录的总条数

        $data = [];
        if ($results) {
            foreach ($results as $row) {
                $data[] = [
                    'id' => $row->id,
                    'title' => $row->title, # 产品名称
                    'quota_range' => $row->min_quota . ' - ' . $row->max_quota, // 借款金额范围
                    'periods' => $row->period, // 借款期数
                    'annualized_interest_rate' => $row->annualized_interest_rate * 100, // 年化利率
                    'repayment_way' => ($row->repayment_way == 1)?'等本等息':'其他方式', // 还款方式 默认【1】 1：到期本息
                    'trial_rate' => $row->trial_rate * 100, // 信审费率
                    'service_rate' => $row->service_rate * 100, // 服务费率
                    'poundage' => $row->poundage * 100, // 手续费率
                    'overdue_rate' => $row->overdue_rate * 100, // 逾期费率
                    'prepayment_poundage' => $row->prepayment_poundage * 100, // 提前还款手续费率
                    'active' => $row->active, // 上线状态 默认【0】 0：关闭 1：开启
                    'updated_at' => $row->updated_at, // 添加时间
                ];
            }
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int)$count,
            'results' => $data
        ]);
    }


    /**
     * 修改产品配置信息
     */
    public function actionUpdate()
    {
        $request = Yii::$app->request;

        $productId = $request->get('id');
        $title = trim($request->post('title'));
        if (empty($title)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请输入借款名称']);
        }
        if (mb_strlen($title) > 10) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '借款名称不能超过10个字']);
        }
        $minQuota = (int)($request->post('min_quota'));
        $maxQuota = (int)($request->post('max_quota'));
        if ($minQuota > $maxQuota) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '借款最低额度不得高于借款最高额度']);
        }

        $periods = $request->post('periods');
        if (empty($periods)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '至少选择一项借款期数']);
        }
        sort($periods);
        $periods = implode(',', $periods);

        $repaymentWay = (int)($request->post('repayment_way'));
        if ($repaymentWay <= 0) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请选择还款方式']);
        }


        $annualizedInterestRate = (double)($request->post('annualized_interest_rate')); // 年化利率
        $trialRate = (double)($request->post('trial_rate')); // 信审费率
        $serviceRate = (double)($request->post('service_rate')); // 服务费率
        $poundage = (double)($request->post('poundage')); // 手续费率
        $prepayment_poundage = (double)($request->post('prepayment_poundage')); // 提前还款手续费率
        $prepayment_poundage_max = (int)($request->post('prepayment_poundage_max')); // 提前还款手续费上限
        $overdueRate = (double)($request->post('overdue_rate')); // 逾期费率
        $limitOverdueDays = (int)($request->post('limit_overdue_days')); // 逾期最大天数限制

        $use = $request->post('use', '');
        if (!empty($use) && (int)count($use) > 20) { // 用途最多添加二十个
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '用途目前最多能添加二十个']);
        }
        if (!empty($use)) {
            $use = implode(',', $use); // 用途
        }


        $active = intval($request->post('active', 0)); // 线上状态


        $result = ProductModel::saveProduct($title, $minQuota, $maxQuota, $periods, $annualizedInterestRate, $repaymentWay, $trialRate, $serviceRate, $poundage, $prepayment_poundage, $prepayment_poundage_max, $overdueRate, $limitOverdueDays, Yii::$app->user->id, $productId, $use, $active);
        return $result;
    }

    /**
     * 获取产品明细
     * @param integer $id 产品ID
     * @return string 返回明细数据
     */
    public function actionDetail()
    {
        $request = Yii::$app->request;
        $productModel = new ProductModel();

        $productId = $request->get('id');
        if (empty($productId)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        $result = $productModel->getProductDetail($productId);
        if (isset($result['use']) && !empty($result['use'])) {
            $result['use'] = explode(',', $result['use']);
        } else {
            $result['use'] = [];
        }
        if (isset($result['period']) && !empty($result['period'])) {
            $result['period'] = explode(',', $result['period']);
        } else {
            $result['period'] = [];
        }

        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'error_message' => '',
            'results' => $result
        ]);
    }

}