<?php

namespace frontend\controllers;

use common\models\BudgetPlanModel;
use common\models\Loan;
use common\models\LoanLogModel;
use common\models\OrderDetailModel;
use common\models\PayLog;
use common\models\PayLogModel;
use common\models\ShopModel;
use common\models\ShopProductModel;
use common\models\ShopProSpecModel;
use common\models\VisaModel;
use common\services\QiniuService;
use common\services\RedisService;
use frontend\bases\FrontendController;
use common\models\LoanModel;
use common\models\UserModel;
use common\models\ProductModel;
use common\models\UserBankModel;
use common\services\LoanService;
use yii\helpers\Json;
use yii\base\InvalidParamException;
use yii\base\ErrorException;
use yii\base\Exception;
use Yii;

class LoanController extends FrontendController
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

    public function actionCashInstallment()
    {
        $user = Yii::$app->user->identity;
        if ($user->is_forbidden == UserModel::DISABLE) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '暂时无法申请借款，敬请谅解']);
        } else if ( !$user->is_identity_auth || !$user->is_profile_auth || !$user->is_bankcard_auth || !$user->is_phone_auth) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请先完成认证后，才可以借款']);
        }

        $product = ProductModel::getProductByCondition(['type' => ProductModel::TYPE_CASH , 'active' => ProductModel::STATE_ACTIVE]);
        if (!$product) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '获取产品配置失败，请联系客服']);
        }
        $defaultBankCard = UserBankModel::getUserDefaultBankCard($user->id);
        if (!$defaultBankCard) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '获取用户默认银行卡失败，请联系客服']);
        }
        $data = [
            'max_quota' => $product['max_quota'] ?? 0, // 借款最大额度
            'min_quota' => $product['min_quota'] ?? 0, // 借款最小额度
            'available_quota' => $user->available_quota ?? 0, // 用户当前可用额度
            'use' => $product['use'] ? explode(',', $product['use']) : [], // 用途
            'period' => $product['period'] ? explode(',', $product['period']) : [], // 借款期数
            'repayment_way' => $product['repayment_way'] ?? 0, // 还款方式 1:等本等息
            'annualized_interest_rate' => $product['annualized_interest_rate'] ?? 0.00, // 年化利率
            'trial_rate' => $product['trial_rate'] ?? 0.00, // 信审费率
            'service_rate' => $product['service_rate'] ?? 0.00, // 服务费率
            'overdue_rate' => $product['overdue_rate'] ?? 0.00, // 逾期费率
            'poundage' => $product['poundage'] ?? 0.00, // 手续费率
            'bank_name' => $defaultBankCard->bank_name ?? '', // 银行名称
            'end_bank_no' => substr($defaultBankCard->bank_no ?? '', -4), // 银行卡后四位
        ];

        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            "error_message" => "",
            'results' => $data,
        ]);
    }

    /**
     *  现金分期借款提交
     */
    public function actionCashInstallmentSubmit()
    {
        $user = Yii::$app->user->identity;
        if ($user->is_forbidden == UserModel::DISABLE) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '暂时无法申请借款，敬请谅解']);
        } else if ( !$user->is_identity_auth || !$user->is_profile_auth || !$user->is_bankcard_auth || !$user->is_phone_auth) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请先完成认证后，才可以借款']);
        }

        $request = Yii::$app->request;
        $quota = intval($request->post('amount')); // 借款金额
        $use = $request->post('use', ''); // 用途
        $period = intval($request->post('period')); // 分期期限

        if ($quota <= 0 || $period <= 0 || $use == '') {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }

        $product = ProductModel::getProductByCondition(['type' => ProductModel::TYPE_CASH , 'active' => ProductModel::STATE_ACTIVE]);
        if (!$product) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '获取产品配置失败，请联系客服']);
        }
        if ($quota % 100 != 0) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '借款金额应为可用额度内的整百金额']);
        }
        if ($quota > $user->available_quota) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '借款金额不能超过可用额度']);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $orderCode = LoanService::buildOrderCode($user->id);
            $loan = LoanModel::addLoan($user->id, ProductModel::TYPE_CASH, $orderCode, $quota, $period, $use, $product);
            UserModel::frozenUserQuota($user->id, $quota); // 冻结额度
            LoanService::generateSubmitLog($loan->id); // 申请资料提交成功
            LoanService::generateAuditingLog($loan->id); // 系统审核中
            if (!$loan) {
                return Json::encode([
                    'status' => self::STATUS_FAILURE,
                    'error_message' => '提交申请失败',
                ]);
            }
            $transaction->commit();
        } catch (yii\db\Exception $e) {
            Yii::error($e);
            $transaction->rollBack();
            return Json::encode([
                "status" => self::STATUS_FAILURE,
                "error_message" => "数据出错了, 请联系客服",
            ]);
        } catch (yii\base\Exception $e) {
            Yii::error($e);
            $transaction->rollBack();
            return Json::encode([
                "status" => self::STATUS_FAILURE,
                "error_message" => "系统故障了, 请联系客服",
            ]);
        }

        $im = Yii::$app->companyim;
        $im->sendTextToChatGroup($im::SENDER_XIAOBAI, $im::COMPANY_CHAT_ID, sprintf('用户提交现金分期借款申请成功！用户ID(%s),金额(%s),分期期限(%s期)！', $user->id, $quota, $period));

        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'error_message' => '提交申请成功',
        ]);
        
    }

    /**
     * 用户待还款记录
     */
    public function actionRepayingRecord()
    {
        $request = Yii::$app->request;
        $offset = (integer)$request->get('offset', 0);
        $limit = (integer)$request->get('limit', 10);

        $userId = Yii::$app->user->getId(); // 当前登录用户ID
        $loanModel = new LoanModel();
        $loanIds = BudgetPlanModel::getCurrentTermLoanId($userId) ? array_column(BudgetPlanModel::getCurrentTermLoanId($userId), 'loan_id') : '';
        $result = $loanModel->findRepayingByLoanIds($loanIds, 'loan.id desc', $offset, $limit); // 查询下个月10号之前存在待还记录的借款订单

        $count = Loan::find() ->where(['in', 'loan.id', $loanIds])->andWhere(['or', 'state="' . LoanModel::STATE_REPAYING . '"', 'state="' . LoanModel::STATE_OVERDUE . '"'])->count();
        if (($offset + $limit) < $count) {
            $hasMore = true; // 标识是否存在更多数据，true 时标识存在， false 标识无更多数据
        } else {
            $hasMore = false; // 标识是否存在更多数据，true 时标识存在， false 标识无更多数据
        }

        $data= [
            'total_amount' => 0.00,
            'detail' => [],
        ]; // 近30天应还金额
        foreach ($result as $k => $v) {
            $repayDetail = LoanService::caculateRepaymentAmountDetail($v['id']);
            $data['detail'][] = [
                'loan_id' => $v['id'], // 借款Id
                'type' => $v['type'], // 借款分类 1：现金分期 2：消费分期
                'quota' => $v['quota'], // 申请额度
                'period' => $v['period'], // 借款期数
                'current_term' => (integer)($v['repayed_count'] + $repayDetail['term_count']), // 当前期数
                'state' => $v['state'], // 借款状态
                'created_at' => strtotime($v['created_at']), // 申请时间
                'repayment_at' => strtotime($repayDetail['repayment_at']), // 本期还款时间
                'term_amount' => $repayDetail['term_amount'] ?? 0.00, // 当前应还金额
                'overdue_days' => $repayDetail['overdue_days'] ?? 0, // 逾期天数
                'max_overdue_days' => $repayDetail['max_overdue_days'] ?? 0 // 产品逾期上限天数
            ];
            $data['total_amount'] += $data['detail'][$k]['term_amount']; // 近30天应还金额
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            "error_message" => "",
            'results' => $data,
            'has_more' => $hasMore
        ]);
    }

    /**
     * 待还款记录详情
     */
    public function actionRepayingDetail()
    {
        $request = Yii::$app->request;
        $loanId = $request->get('loan_id');
        if (empty($loanId)) {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                "error_message" => "参数错误",
            ]);
        }
        $loan = LoanModel::findLoanById($loanId); // 借款详情

        $repayDetail = LoanService::caculateRepaymentAmountDetail($loanId);
        $data= [
            'surplus_amount' => $repayDetail['surplus_amount'], // 剩余未还总额
            'period' => $loan['period'], // 借款期数
            'current_term' => (integer)($loan['repayed_count'] + $repayDetail['term_count']), // 当前期数
            'repayment_at' => strtotime($repayDetail['repayment_at']), // 本期还款时间
            'state' => $loan['state'], // 借款状态

            'overduePrincipal' => $repayDetail['overdue_principal'] ?? 0.00, // 逾期本金
            'term_amount' => $repayDetail['term_amount'] ?? 0.00, // 当前应还金额

            'need_repay_principal' => $repayDetail['monthly'] + $repayDetail['prepayment_fee'] ?? 0, // 未还借款本金（包含息费） = 应还月供 + 提前还款手续费
            'overdue_days' => $repayDetail['overdue_days'] ?? 0, // 逾期天数
            'overdue_fine' => $repayDetail['overdue_fine'] ?? 0.00, // 逾期罚金
            'repayment_day' => Yii::$app->params['repayment_day'], // 还款日
        ];

        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            "error_message" => "",
            'results' => $data,
        ]);
    }

    /**
     * 用户借款记录
     */
    public function actionLoanRecord()
    {
        $request = Yii::$app->request;
        $offset = (integer)$request->get('offset', 0);
        $limit = (integer)$request->get('limit', 10);
        $userId = Yii::$app->user->getId(); // 当前登录用户ID
        $loanModel = new LoanModel();
        $result = $loanModel->findLoanByUserId($userId, 'loan.id desc', $offset, $limit);

        $count = (integer)Loan::find()->where(['loan.user_id' => $userId])->count();
        if (($offset + $limit) < $count) {
            $hasMore = true; // 标识是否存在更多数据，true 时标识存在， false 标识无更多数据
        } else {
            $hasMore = false; // 标识是否存在更多数据，true 时标识存在， false 标识无更多数据
        }

        $data = []; // 返回结果
        foreach ($result as $k => $v) {
            $repayDetail = LoanService::caculateRepaymentAmountDetail($v->id);
            $data[] = [
                'loan_id' => $v->id, // 借款Id
                'quota' => $v->quota ?? 0, // 借款金额
                'period' => $v->period ?? 0, // 借款期限
                'use' => $v->use ?? '', // 用途
                'current_period' => ($v->repayed_count ?? 0) + $repayDetail['term_count'], // 当前期数
                'state' => LoanService::STATE_MAP[$v->state], // 当前状态
                'type' => $v->type, // 借款分类 1：现金分期 2：消费分期
                'created_at' => strtotime($v->created_at), // 申请时间
            ];
            switch ($v->state) {
                case LoanModel::STATE_AUDITING:
                case LoanModel::STATE_AUDIT_FAILURE:
                case LoanModel::STATE_REVIEWING:
                case LoanModel::STATE_REVIEW_FAILURE:
                case LoanModel::STATE_REVIEW_SUCCESS:
                case LoanModel::STATE_CONFIRMING:
                case LoanModel::STATE_CONFIRM_SUCCESS:
                case LoanModel::STATE_CONFIRM_FAILURE:
                case LoanModel::STATE_GRANTING:
                    $data[$k]['surplus_principal'] = 0.00; // 剩余未还本金
                    break;
                case LoanModel::STATE_REPAYING:
                case LoanModel::STATE_FINISHED:
                case LoanModel::STATE_OVERDUE:
                    $data[$k]['surplus_principal'] = $repayDetail['surplus_principal']; // 剩余未还本金
                    break;
            }
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            "error_message" => "",
            'results' => $data,
            'has_more' => $hasMore
        ]);

    }

    /**
     * 用户还款记录
     */
    public function actionRepayedRecord()
    {
        $request = Yii::$app->request;
        $offset = (integer)$request->get('offset', 0);
        $limit = (integer)$request->get('limit', 10);

        $userId = Yii::$app->user->getId(); // 当前登录用户ID

        $result = PayLogModel::findRepaymentPayLogByUserId($userId, $offset, $limit);
        $count = PayLog::find()
                ->where(['or', 'state=\''. PayLogModel::STATE_SUCCESS . '\'', 'state=\'' . PayLogModel::STATE_PENDING . '\' and created_at<\'' . date('Y-m-d H:i:s', time() - 3600 * 2) . '\''])
                ->andWhere(['user_id' => $userId])
                ->andWhere(['pay_type' => PayLogModel::TYPE_AUTHPAY])->count();
        if (($offset + $limit) < $count) {
            $hasMore = true; // 标识是否存在更多数据，true 时标识存在， false 标识无更多数据
        } else {
            $hasMore = false; // 标识是否存在更多数据，true 时标识存在， false 标识无更多数据
        }
        $data= []; // 返回数据

        foreach ($result as $k => $v) {
            $data[] = [
                'loan_id' => $v['loan']['id'],
                'type' => $v['loan']['type'],
                'use' => $v['loan']['use'],
                'money_order' => $v['money_order'],
                'period' => $v['loan']['period'],
                'state' => $v['state'],
                'created_at' => strtotime($v['created_at']),
            ];
            $planIds = explode(',',$v['plan_id']);
            rsort($planIds);
            $recentPlan = $planIds[0];
            foreach ($v['budgetPlan'] as $plan) {
                if ($plan['id'] == $recentPlan) {
                    $data[$k]['current_term'] = $plan['term'];
                    break;
                }
            }
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            "error_message" => "",
            'results' => $data,
            'has_more' => $hasMore
        ]);
    }

    /**
     * 借款详情
     */
    public function actionRecordDetail()
    {
        $request = Yii::$app->request;
        $loanModel = new LoanModel();

        $loanId = $request->get('loan_id', ''); // 借款Id
        if (!$loanId) { return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '借款Id不能为空']); }

        $result = $loanModel->findLoanById($loanId); // 查询该借款明细信息
        $amount = 0; // 最终正常情况下 应还总额
        if ($result->interest) {
            // 放款成功后，以实际应还总额为准 （不包括逾期金额）
            $amount = $result->interest + $result->quota; // 应还总额 = 借款息费 + 借款金额
        } else {
            // 放款生成 借款息费之前，使用生成还款计划计算应还总额与月供
            $plan = LoanService::generateBudgetPlan($loanId); // 还款计划
            foreach ($plan as $v) {
                $amount += $v['monthly']; // 每月月供总和
            }
        }
        $data = [];
        if ($result) {
            $data['detail'] = [
                'use' => $result->use ?? 0, // 借款项目
                'created_at' => strtotime($result->created_at), // 借款时间
                'quota' =>$result->quota, // 借款金额
                'period' => $result->period, // 借款期数
                'monthly' => round(($amount / $result->period), 2), // 每月应还
            ];
            if ($result->state == LoanModel::STATE_REPAYING || $result->state == LoanModel::STATE_OVERDUE) {
                $repaymentDetail = LoanService::caculateRepaymentAmountDetail($loanId);
                $data['repayed_amount'] = $result->repayed_amount ?? 0.00; // 已还金额
                $data['surplus_amount'] = $repaymentDetail['surplus_amount'] ?? 0.00; // 剩余未还金额
                if ($result->budgetPlan) {
                    foreach ($result->budgetPlan as $k => $v) {
                        $data['plan'][] = [
                            'monthly' => $v['monthly'],
                            'planned_repayment_at' => strtotime($v['planned_repayment_at']),
                            'state' => $v['state'],
                        ];
                    }
                }
            } else {
                $log = ['submit', 'audit', 'confirm', 'loan'];
                foreach($log as $v) {
                    // 若 现金分期，不必查询商家确认日志
                    if ($result->type == LoanModel::TYPE_CASH && $v == 'confirm') {
                        continue;
                    }
                    $temp = LoanLogModel::getLoanLogByState($loanId, $v);
                    $data['log'][] = [
                        'title' => $temp['title'] ?? $v,
                        'content' => $temp['content'] ?? '',
                        'created_at' => strtotime($temp['created_at']) ?? 0,
                    ];
                }
            }
        }

        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            "error_message" => "",
            'results' => $data ? [$data] : [],
        ]);
    }

    /**
     * 消费分期借款提交
     */
    public function actionConsumeInstallmentSubmit()
    {
        $user = Yii::$app->user->identity;
        if ($user->is_forbidden == UserModel::DISABLE) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '暂时无法申请借款，敬请谅解']);
        } else if ( !$user->is_identity_auth || !$user->is_profile_auth || !$user->is_bankcard_auth || !$user->is_phone_auth) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请先完成认证后，才可以借款']);
        }

        $request = Yii::$app->request;
        $period = intval($request->post('period', 0)); // 分期期限
        $shopId = intval($request->post('shop_id', 0)); // 商家ID
        $details = $request->post('details', ''); // 详情
        $signPic = $request->post('sign_pic', ''); // 亲签照

        if ($period <= 0 || $shopId <= 0 || empty($details) || empty($signPic)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }

        try {
            $mutex = Yii::$app->mutex;
            $lockName = sprintf('%s%s',RedisService::SHOP_PREFIX , $shopId);
            $lock = $mutex->acquire($lockName, 0); // 获取锁
        } catch (ErrorException $e) {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                'error_message' => '服务器异常，请检查redis服务是否开启'
            ]);
        } catch (Exception $e) {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                'error_message' => $e->getMessage()
            ]);
        }
        if ($lock) {
            $orderDetail = []; // 借款订单详情
            $quota = 0; // 借款总金额
            if ($details) {
                foreach ($details as $k => $v) {
                    // 若参数非对象，转成对象
                    if (is_array($v)) {
                        $v = json_encode($v);
                    }
                    if (!is_object($v)) {
                        $v = json_decode($v);
                    }
                    if (!isset($v->shop_product_id) || $v->shop_product_id <= 0) {
                        $mutex->release($lockName); // 释放锁
                        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数商户产品ID错误']);
                    }
                    if (!isset($v->spec_id) || $v->spec_id <= 0) {
                        $mutex->release($lockName); // 释放锁
                        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数商户产品规格ID错误']);
                    }
                    if (!isset($v->quantity) || (integer)$v->quantity < 0) {
                        $mutex->release($lockName); // 释放锁
                        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数商户数量错误']);
                    }
                    $shopProduct = ShopProductModel::getShopProductById($v->shop_product_id); // 商品信息
                    if (!$shopProduct) {
                        $mutex->release($lockName); // 释放锁
                        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '商品信息不存在']);
                    }
                    $shopProductSpec = ShopProSpecModel::findShopProSpecById($v->spec_id); // 商品规格价格信息
                    if (!$shopProductSpec) {
                        $mutex->release($lockName); // 释放锁
                        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '不存在的商品规格信息']);
                    }
                    if ($v->quantity == 0) {
                        $mutex->release($lockName); // 释放锁
                        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => sprintf('名称：%s 规格：%s 的商品购买数量应大于0', $shopProduct['title'], $shopProductSpec['spec'])]);
                    }
                    if ($v->quantity > $shopProductSpec['stock']) {
                        $mutex->release($lockName); // 释放锁
                        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => sprintf('库存不足！名称：%s 规格：%s 的商品库存仅为 %s', $shopProduct['title'], $shopProductSpec['spec'], $shopProductSpec['stock'])]);
                    }

                    $orderDetail[] = [
                        'shop_product_id' => $shopProduct['id'],
                        'spec_id' => $shopProductSpec['id'],
                        'title' => $shopProduct['title'], // 商品名称
                        'spec' => $shopProductSpec['spec'], // 商品规格
                        'price' => (float)$shopProductSpec['price'], // 商品单价
                        'quantity' => (integer)$v->quantity, // 数量
                        'total' => round(($shopProductSpec['price'] * $v->quantity), 2), // 合计
                    ];
                    $quota += $orderDetail[$k]['total']; // 总金额
                }
            } else {
                $mutex->release($lockName); // 释放锁
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '明细参数格式不正确']);
            }

            $shop = ShopModel::findShopByShopId($shopId); // 查询当前商户信息
            if (!$shop) {
                $mutex->release($lockName); // 释放锁
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '无此商户信息']);
            }
            if ($shop['state'] != ShopModel::STATE_AUDIT_PASS) {
                $mutex->release($lockName); // 释放锁
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '商家正在审核中或审核未通过，请联系商家确认']);
            }

            // 验证金额
            if ($quota > $user->available_quota) {
                $mutex->release($lockName); // 释放锁
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '借款金额不能超过可用额度']);
            }
            // 验证商户总额度
            if ($quota > $shop['available_quota']) {
                $mutex->release($lockName); // 释放锁
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '商家可用额度不足']);
            }
            // 验证商户单日可用额度
            if ($quota > $shop['daily_available_quota']) {
                $mutex->release($lockName); // 释放锁
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '商家今日可用额度不足']);
            }
            // 验证商户单笔限额
            if ($quota > $shop['single_limit_quota']) {
                $mutex->release($lockName); // 释放锁
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => sprintf('商家单笔限额%s元', $shop['single_limit_quota'])]);
            }

            $product = ProductModel::getProductByCondition(['type' => ProductModel::TYPE_CONSUMPTION , 'active' => ProductModel::STATE_ACTIVE]);
            if (!$product) {
                $mutex->release($lockName); // 释放锁
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '获取产品配置失败，请联系客服']);
            }
            // 验证图片参数
            if ($signPic) {
                if (is_array($signPic)) {
                    $signPic = json_encode($signPic);
                }
                if (!is_object($signPic)) {
                    $signPic = json_decode($signPic);
                }
                if (!isset($signPic->pic_url) || $signPic->pic_url == '') {
                    // 需重新上传图片
                    if (!isset($signPic->img_b64) || $signPic->img_b64 == '') {
                        $mutex->release($lockName); // 释放锁
                        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数待上传的亲签照图片错误']);
                    }
                    if (!isset($signPic->suffix) || $signPic->suffix == '') {
                        $mutex->release($lockName); // 释放锁
                        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数亲签照图片后缀错误']);
                    }
                    //匹配出图片的格式
                    if (!preg_match('/^(data:\s*image\/(\w+);base64,)/', $signPic->img_b64, $pic)) {
                        $mutex->release($lockName); // 释放锁
                        return Json::encode([
                            'status' => self::STATUS_FAILURE,
                            'error_message' => '参数待上传的亲签照图片格式不正确',
                        ]);
                    }
                    $_FILES['file'] =  ['name' => sprintf('%s.%s', date('YmdHis'), $signPic->suffix), 'tmp_name' => $signPic->img_b64, 'size'=> strlen(file_get_contents($signPic->img_b64)) / 1024];
                    if ($_FILES['file']['size'] > 3 * 1024) {
                        $mutex->release($lockName); // 释放锁
                        return Json::encode([
                            'status' => self::STATUS_FAILURE,
                            'error_message' => '仅支持压缩后小于3MB的图片哦~',
                        ]);
                    }
                    $result = QiniuService::qiniuImageUpload(QiniuService::BUCKET_FENQI);
                    if ($result['code'] != QiniuService::STATUS_SUCCESS) {
                        $mutex->release($lockName); // 释放锁
                        return Json::encode([
                            'status' => self::STATUS_FAILURE,
                            'error_message' => $result['message'],
                        ]);
                    }

                    $picUrl = $result['url']; // 调用上传返回的图片路径
                } else {
                    $picUrl = $signPic->pic_url; // 亲签照图片路径
                }
            } else {
                $mutex->release($lockName); // 释放锁
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '亲签照参数格式不正确']);
            }

            $transaction = Yii::$app->db->beginTransaction();
            try {
                $orderCode = LoanService::buildOrderCode($user->id); // 借款编号
                $loan = LoanModel::addLoan($user->id, ProductModel::TYPE_CONSUMPTION, $orderCode, $quota, $period, LoanModel::USE_CONSUMPTION, $product, $shopId); // 提交订单
                $isFrozenUserQuota = UserModel::frozenUserQuota($user->id, $quota); // 冻结用户额度
                ShopModel::frozenShopQuota($shopId, $quota); // 冻结商家额度
                LoanService::generateSubmitLog($loan->id); // 申请资料提交成功
                LoanService::generateAuditingLog($loan->id); // 系统审核中
                foreach ($orderDetail as $v) {
                    ShopProSpecModel::deductProStock($v['spec_id'], $v['quantity']); // 扣除 商品库存
                    ShopProductModel::deductProTotalStock($v['shop_product_id'], $v['quantity']);// 扣除商品的总库存
                    $isAddSuccess = OrderDetailModel::addOrderDetail($loan->id, $v['total'], $v['shop_product_id'], $v['spec_id'], $v['title'], $v['spec'], $v['quantity'], $v['price']); // 添加借款订单明细
                    if (!$isAddSuccess) {
                        $mutex->release($lockName); // 释放锁
                        return Json::encode([
                            'status' => self::STATUS_FAILURE,
                            'error_message' => '提交申请失败',
                        ]);
                    }
                }
                // 添加 用户亲签照
                $visa = VisaModel::findVisaByUserIdShopId($user->id, $shopId);
                if ($visa) {
                    VisaModel::update($user->id, $shopId, $picUrl); // 更新亲签照信息
                } else {
                    VisaModel::add($user->id, $shopId, $picUrl); // 添加用户亲签照信息
                }

                if (!$loan || !$isFrozenUserQuota) {
                    $mutex->release($lockName); // 释放锁
                    return Json::encode([
                        'status' => self::STATUS_FAILURE,
                        'error_message' => '提交申请失败',
                    ]);
                }
                $transaction->commit();
            } catch (yii\db\Exception $e) {
                Yii::error($e);
                $transaction->rollBack();
                $mutex->release($lockName); // 释放锁
                return Json::encode([
                    "status" => self::STATUS_FAILURE,
                    "error_message" => "数据出错了, 请联系客服",
                ]);
            } catch (yii\base\Exception $e) {
                Yii::error($e);
                $transaction->rollBack();
                $mutex->release($lockName); // 释放锁
                return Json::encode([
                    "status" => self::STATUS_FAILURE,
                    "error_message" => "系统故障了, 请联系客服",
                ]);
            }

            $im = Yii::$app->companyim;
            $im->sendTextToChatGroup($im::SENDER_XIAOBAI, $im::COMPANY_CHAT_ID, sprintf('用户提交消费分期借款申请成功！用户ID(%s),金额(%s),分期期限(%s期)！', $user->id, $quota, $period));
            $mutex->release($lockName); // 释放锁
            return Json::encode([
                'status' => self::STATUS_SUCCESS,
                'error_message' => '提交申请成功',
            ]);
        } else {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                'error_message' => '人多拥挤，请重试'
            ]);
        }
    }

}
