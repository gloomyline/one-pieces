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
                    'quota_range' => $row->min_quota . ' - ' . $row->max_quota . ' 元', // 借款金额范围
                    'period_range' => $row->min_period . ' - ' . $row->max_period . ' 天', // 借款周期范围
                    'annualized_interest_rate' => $row->annualized_interest_rate, // 年化利率
                    'repayment_way' => ($row->repayment_way == 1)?'到期本息':'其他方式', // 还款方式 默认【1】 1：到期本息
                    'trial_rate' => $row->trial_rate, // 信审费率
                    'service_rate' => $row->service_rate, // 服务费率
                    'poundage' => $row->poundage, // 手续费率
                    'overdue_rate' => $row->overdue_rate, // 逾期费率
                    'active' => ($row->active == 1)?'上线':'下线', // 上线状态 默认【0】 0：下线 1：上线
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
     * 添加产品配置信息
     * @param string $title 产品名称
     * @param integer $min_quota 借款最小额度
     * @param integer $max_quota 借款最大额度
     * @param integer $min_period 借款周期下限
     * @param integer $max_period 借款周期上限
     * @param double $annualized_interest_rate 年化利率
     * @param integer $repayment_way 还款方式 1:到期本息
     * @param double $trial_rate 信审费率
     * @param double $service_rate 服务费率
     * @param double $poundage 手续费率
     * @param double $overdue_rate 逾期费率
     * @param integer $limit_overdue_days 逾期最大天数限制
     * @return string
     */
    public function actionAdd()
    {
        $request = Yii::$app->request;
        $productModel = new productModel();

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
        $minPeriod = (int)($request->post('min_period'));
        $maxPeriod = (int)($request->post('max_period'));
        if ($minPeriod > $maxPeriod) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '借款周期下限不得高于借款周期上限']);
        }
        $repaymentWay = (int)($request->post('repayment_way'));
        if ($repaymentWay <= 0) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请选择还款方式']);
        }

        $annualizedInterestRate = (double)($request->post('annualized_interest_rate')); // 年化利率
        $trialRate = (double)($request->post('trial_rate')); // 信审费率
        $serviceRate = (double)($request->post('service_rate')); // 服务费率
        $poundage = (double)($request->post('poundage')); // 手续费率
        $overdueRate = (double)($request->post('overdue_rate')); // 逾期费率
        $limitOverdueDays = (int)($request->post('limit_overdue_days')); // 逾期最大天数限制

        $result = $productModel->saveProduct($title, $minQuota, $maxQuota, $minPeriod, $maxPeriod, $annualizedInterestRate, $repaymentWay, $trialRate, $serviceRate, $poundage, $overdueRate, $limitOverdueDays, yii::$app->user->identity->getId());
        return $result;
    }

    /**
     * 修改产品配置信息
     * @param integer  $id 产品ID
     * @param string $title 产品名称
     * @param integer $min_quota 借款最小额度
     * @param integer $max_quota 借款最大额度
     * @param integer $min_period 借款周期下限
     * @param integer $max_period 借款周期上限
     * @param double $annualized_interest_rate 年化利率
     * @param integer $repayment_way 还款方式 1:到期本息
     * @param double $trial_rate 信审费率
     * @param double $service_rate 服务费率
     * @param double $poundage 手续费率
     * @param double $overdue_rate 逾期费率
     * @param integer $limit_overdue_days 逾期最大天数限制
     * @return string
     */
    public function actionUpdate()
    {
        $request = Yii::$app->request;
        $productModel = new productModel();

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
        $minPeriod = (int)($request->post('min_period'));
        $maxPeriod = (int)($request->post('max_period'));
        if ($minPeriod > $maxPeriod) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '借款周期下限不得高于借款周期上限']);
        }
        $repaymentWay = (int)($request->post('repayment_way'));
        if ($repaymentWay <= 0) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请选择还款方式']);
        }


        $annualizedInterestRate = (double)($request->post('annualized_interest_rate')); // 年化利率
        $trialRate = (double)($request->post('trial_rate')); // 信审费率
        $serviceRate = (double)($request->post('service_rate')); // 服务费率
        $poundage = (double)($request->post('poundage')); // 手续费率
        $overdueRate = (double)($request->post('overdue_rate')); // 逾期费率
        $limitOverdueDays = (int)($request->post('limit_overdue_days')); // 逾期最大天数限制

        $result = $productModel->saveProduct($title, $minQuota, $maxQuota, $minPeriod, $maxPeriod, $annualizedInterestRate, $repaymentWay, $trialRate, $serviceRate, $poundage, $overdueRate, $limitOverdueDays, yii::$app->user->identity->getId(), $productId);
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

        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'error_message' => '',
            'results' => $result
        ]);
    }

}