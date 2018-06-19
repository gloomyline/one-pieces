<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/11
 * Time: 15:11
 */

namespace shop\controllers;


use common\models\LoanModel;
use common\models\ShopOrderLog;
use common\models\ShopOrderLogModel;
use common\services\LoanService;
use shop\services\OrderService;
use Yii;
use yii\helpers\Json;
use shop\bases\ShopBackendController;

class OrderController extends ShopBackendController
{
    /**
     * 商户订单列表
     * @return string
     */
    public function actionList()
    {
        $data = $results = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $productName = trim($request->get('product_name', ''));
        $beginAt = $request->get('start_at', ''); // 申请起始时间
        $endAt = $request->get('end_at', ''); // 申请截止时间
        $state = $request->get( 'state', ''); // 借款状态

        if ($state != '') {
            $state = self::formatterState($state);
        }
        $orderState = intval(($request->get('order_state', ''))); // 订单状态
        $shopId = Yii::$app->user->identity->shop_id;
        $results = LoanModel::getShopOrderList($offset, $limit, $shopId, $productName, $beginAt, $endAt, $state, $orderState);
        foreach ($results['result'] as $row) {

            if ($row->orderDetail) {
                $productName = '';
                $productTitle = [];
                foreach ($row->orderDetail as $lt) {
                    $productTitle[] = $lt->title ;
                }
                $productName = implode(',', $productTitle);
            }
            $data[] = [
                'id' => $row->id,
                'encoding' => $row->encoding,
                'real_name' => $row->user->real_name,
                'mobile' => $row->user->mobile,
                'product_name' => $productName ?? '',
                'total_price' => $row->quota, // 商品价格
                'period' => $row->period, // 分期期数
                'created_at' => $row->created_at, // 提交时间
                'order_state' => $row->shopOrderLog->state ?? '',
                // 'check_at' => $row->check_at, // 初审时间
                'review_at' => $row->review_at, // 复审时间-> 审核时间
                'state' => $row->state, // 借款状态
                'lending_at' => $row->lending_at, // 放款时间
                'arrival_amount' => $row->arrival_amount, // 放款金额
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int)$results['count'],
            'results' => $data
        ]);

    }

    /**
     * 借款状态格式化
     * @param $state 状态值
     * @return array|string 返回该状态值的对应的借款状态条件
     */
    private static function formatterState($state) {
        $cond = [];
        switch ($state) {
            case 1 : $cond = [LoanModel::STATE_AUDITING, LoanModel::STATE_REVIEWING]; break; // 待审核
            case 2 : $cond = [LoanModel::STATE_REVIEW_SUCCESS, LoanModel::STATE_CONFIRMING, LoanModel::STATE_CONFIRM_SUCCESS, LoanModel::STATE_CONFIRM_FAILURE]; break; // 审核成功
            case 3 : $cond = [LoanModel::STATE_AUDIT_FAILURE, LoanModel::STATE_REVIEW_FAILURE]; break; // 审核失败
            case 4 : $cond = [LoanModel::STATE_REPAYING , LoanModel::STATE_FINISHED, LoanModel::STATE_OVERDUE]; break; // 已放款
            case 5 : $cond = [LoanModel::STATE_GRANTING]; break; // 未放款
            default: $cond = ''; break; // 返回空
        }
        return $cond;
    }

    /**
     * 订单详情
     * @return string
     */
    public function actionDetail()
    {
        $request = Yii::$app->request;
        $loanId = intval($request->get('loan_id', ''));
        $loan = LoanModel::findShopOrderDetailByLoanId($loanId);
        if (!$loan) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        // 判断是否存在该订单
        $data = [
            'id' => $loan->id,
            'encoding' => $loan->encoding,
            'real_name' => $loan->user->real_name ?? '',
            'mobile' => $loan->user->mobile ?? '',
            'quota' => $loan->quota, // 商品总价
            'period' => $loan->period,
            'created_at' => $loan->created_at,
            'order_state' => $loan->shopOrderLog->state ?? 0, // 订单状态
            'audited_at' => $loan->review_at,// 审核时间
            'state' => $loan->state,// 借款状态
            'lending_at' => $loan->lending_at,// 放款时间
            'arrival_amount' => $loan->arrival_amount // 放款金额
        ];
        // 商品详情
        if ($loan->orderDetail) {
            foreach ($loan->orderDetail as $row) {
                $data['order'][] = [
                    'title' => $row->title,
                    'spec' => $row->spec,
                    'quantity' => $row->quantity,
                    'price' => $row->price,
                ];
            }
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'results' => $data
        ]);
    }

    /**
     * 确认订单
     * @return string
     */
    public function actionConfirmOrder()
    {
        $request = Yii::$app->request;
        $loanId = $request->get('loan_id', '');
        $loan = LoanModel::findLoanByLoanId($loanId);
        if (!$loan) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        $state = $request->post('state', '');
        // 判断是否为合法的状态
        if (!in_array($state, [ShopOrderLogModel::STATE_CONFIRM_SUCCESS, ShopOrderLogModel::STATE_CONFIRM_FAILURE])) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '确认状态不合法！']);
        }
        $confirmOpinion = $request->post('opinion', '');
        if ($confirmOpinion == '') {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '意见填写不能为空！']);
        }
        if (ShopOrderLog::findOne(['loan_id' => $loan->id])) { // 如果操作记录存在不能进行操作，确保一个订单对应一条日志
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '不合法的操作！']);
        }

        $transaction =Yii::$app->db->beginTransaction();
        try{
            // 创建商户操作日志
            if (!ShopOrderLogModel::addConfirmOrder($loanId, $state, $confirmOpinion)) {
                throw new \Exception('商户操作日志创建失败');
            }
            // 修改loan表状态并生成订单确认日志
            if ($state == ShopOrderLogModel::STATE_CONFIRM_SUCCESS) {
                $data['state'] = LoanModel::STATE_CONFIRM_SUCCESS;
                // 调用商家确认成功的方法
                OrderService::updateCorrelationAfterConfirmSuccess($loanId);
            } elseif ($state == ShopOrderLogModel::STATE_CONFIRM_FAILURE) {
                $data['state'] = LoanModel::STATE_CONFIRM_FAILURE;
                // 调用商家确认失败的方法
                OrderService::updateCorrelationAfterConfirmFailure($loanId);
            }
            $data['confirmed_at'] = date('Y-m-d H:i:s'); // 订单确认时间

            if (!LoanModel::update($loanId, $data)) {
                throw new \Exception('借款状态跟新失败！');
            }
            $transaction->commit();
            return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
        }catch (\Exception $e){
            $transaction->rollBack();
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => $e->getMessage()]);
        }
    }

    /**
     * 确认收货
     * @return string
     */
    public function actionReceivingConfirm()
    {
        $request = Yii::$app->request;
        $loanId = $request->get('loan_id', '');
        // 借款id 确认状态 确认意见
        $loan = LoanModel::findLoanByLoanId($loanId);
        if (!$loan) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        $state = $request->post('state', '');
        // 判断是否为合法的状态
        if (!in_array($state, [ShopOrderLogModel::STATE_RECEIVING_SUCCESS, ShopOrderLogModel::STATE_RECEIVING_FAILURE])) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '状态不合法！']);
        }
        $receivingOpinion = $request->post('opinion', '');
        if ($receivingOpinion == '') {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '意见填写不能为空！']);
        }
        if (!ShopOrderLogModel::receivingConfirm($loanId ,$state, $receivingOpinion)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '确认收货操作失败']);
        }
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

}