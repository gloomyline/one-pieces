<?php

namespace frontend\controllers;

use common\models\Loan;
use frontend\bases\FrontendController;
use common\models\LoanModel;
use common\models\UserModel;
use common\models\LoanLogModel;
use common\models\ProductModel;
use common\models\UserBankModel;
use common\services\LoanService;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\helpers\Url;
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

    public function actionConfirm()
    {
        $user = Yii::$app->user->identity;
        if ($user->is_forbidden == UserModel::DISABLE) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '暂时无法申请借款，敬请谅解']);
        } else if ( !$user->is_identity_auth || !$user->is_profile_auth || !$user->is_bankcard_auth || !$user->is_phone_auth) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请先完成认证后，才可以借款']);
        }

        $request = Yii::$app->request;
        $amount = intval($request->post('amount'));
        $period = intval($request->post('period'));

        if ($amount <= 0 || $period <= 0) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }

        if ($amount > $user->available_quota) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '借款额度不能超过可用额度']);
        }

        $product = ProductModel::getActiveProduct();
        if (!$product) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '获取产品配置失败，请联系客服']);
        }
        if ($amount < $product->min_quota) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '借款额度不能小于产品最低额度']);
        }
        if ($amount > $product->max_quota) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '借款额度不能大于产品最高额度']);
        }
        if ($period < $product->min_period) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '借款时间不能小于产品借款周期下限']);
        }
        if ($period > $product->max_period) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '借款时间不能大于产品借款周期上限']);
        }
        
        $userBankInfo = UserBankModel::getUserDefaultBankCard($user->id);
        if (!$userBankInfo) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请绑定银行卡']);
        }

        $feeDetail = LoanService::caculateFeeDetail($amount, $period, $product);

        $results = [
              "amount" => $amount, // 借款额度
              "period" => $period, // 借款期限
              "bank" => $userBankInfo->bank_name . '（尾号' . substr($userBankInfo->bank_no, -4, 4) . '）', // 到账银行
              "arrival_amount" => $amount, // 到账金额
              "interest" => $feeDetail['totalInterest'], // 借款息费
              "accrual" => $feeDetail['accrual'], // 利息
              "trialFee" => $feeDetail['trialFee'], // 信审费
              "serviceFee" => $feeDetail['serviceFee'], // 服务费
              "poundage" => $feeDetail['poundage'], // 手续费
              "otherFee" => $feeDetail['otherFee'], // 费用 = 手续费 + 信审费 + 服务费
              "annualized_interest_rate" => $feeDetail['annualizedInterestRate'], // 年化利率
        ];

        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            "error_message" => "",
            'results' => [$results],
        ]);
    }

    public function actionSubmit()
    {
        $user = Yii::$app->user->identity;

        $request = Yii::$app->request;
        $quota = intval($request->post('amount'));
        $period = intval($request->post('period'));

        if ($quota <= 0 || $period <= 0) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }

        if ($quota > $user->available_quota) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '借款额度不能超过可用额度']);
        }

        $product = ProductModel::getActiveProduct();
        if (!$product) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '获取产品配置失败，请联系客服']);
        }
        if ($quota < $product->min_quota) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '借款额度不能小于产品最低额度']);
        }
        if ($quota > $product->max_quota) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '借款额度不能大于产品最高额度']);
        }
        if ($period < $product->min_period) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '借款时间不能小于产品借款周期下限']);
        }
        if ($period > $product->max_period) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '借款时间不能大于产品借款周期上限']);
        }
        
        $interest = LoanService::caculateFee($quota, $period, $product); // 计算借款息费

        $hasLoan = LoanModel::getUserActiveLoan($user->id); // 获取用户的有效借款订单

        if ($hasLoan) { // 存在 有效订单
            if ($hasLoan->state == LoanModel::STATE_AUDIT_FAILURE || $hasLoan->state == LoanModel::STATE_REVIEW_FAILURE ) { // 该有效订单为失败订单,初审失败/复审失败
                 // 判断是否处于用户冻结时间
                $isFreezeTime = LoanService::isFreezeTime(Yii::$app->user->getId()); // 判断用户的最新借款订单是否处于冻结时间
                if ($isFreezeTime) { // 冻结状态
                    return Json::encode([
                        'status' => self::STATUS_FAILURE,
                        'error_message' => '您的借款仍处于冻结状态，请耐心等待',
                    ]);
                }
            } else { // 非审核失败订单
                return Json::encode([
                    'status' => self::STATUS_FAILURE,
                    'error_message' => '您有未完成的借款订单',
                ]);
            }
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $orderCode = LoanService::buildOrderCode($user->id);
            $loan = LoanModel::addLoan($user->id, $orderCode, $quota, $period, $interest, $product);
            UserModel::frozenUserQuota($user->id, $quota); // 冻结额度
            LoanService::generateSubmitLog($loan->id, $quota, $period, $interest);
            LoanService::generateAuditingLog($loan->id, $quota, $period, $interest);
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
        $im->sendTextToChatGroup($im::SENDER_XIAOBAI, $im::COMPANY_CHAT_ID, sprintf('用户提交借款成功！用户ID(%s),金额(%s)！', $user->id, $quota));

        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'error_message' => '提交申请成功',
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
        foreach ($result as $k=>$v) {
            $data[] = [
                'loan_id' => $v->id, // 借款Id
                'quota' => $v->quota ?? 0, // 借款金额
                'period' => $v->period ?? 0, // 借款期限
                'state' => $v->state, // 当前状态
            ];
            switch ($v->state) {
                case LoanModel::STATE_AUDITING : { // 待审核
                    $data[$k]['state_time'] = strtotime($v->created_at); // 申请时间
                    break;
                }
                case LoanModel::STATE_AUDIT_FAILURE : // 初审失败
                case LoanModel::STATE_REVIEWING : { // 待复审
                    $data[$k]['state_time'] = strtotime($v->check_at); // 初审时间
                    break;
                }
                case LoanModel::STATE_REVIEW_FAILURE : // 复审失败
                case LoanModel::STATE_REVIEW_SUCCESS : { // 复审成功
                    $data[$k]['state_time'] = strtotime($v->review_at); // 复审时间
                    break;
                }
                case LoanModel::STATE_GRANTING : // 放款中
                case LoanModel::STATE_REPAYING : // 还款中
                case LoanModel::STATE_FINISHED :  // 已还完
                case LoanModel::STATE_OVERDUE : { // 逾期中
                    $data[$k]['state_time'] = strtotime($v->lending_at); // 放款时间
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
     * 用户还款记录
     */
    public function actionRepaymentRecord()
    {
        $request = Yii::$app->request;
        $offset = (integer)$request->get('offset', 0);
        $limit = (integer)$request->get('limit', 10);

        $userId = Yii::$app->user->getId(); // 当前登录用户ID
        $loanModel = new LoanModel();
        $result = $loanModel->findRepaymentsByUserId($userId, 'loan.id desc', $offset, $limit); // 查询用户所有还款记录

        $count = Loan::find()->where(['loan.user_id' => $userId])->andWhere(['or', 'loan.state=\''.LoanModel::STATE_REPAYING.'\'', 'loan.state=\''.LoanModel::STATE_FINISHED.'\''])->count();
        if (($offset + $limit) < $count) {
            $hasMore = true; // 标识是否存在更多数据，true 时标识存在， false 标识无更多数据
        } else {
            $hasMore = false; // 标识是否存在更多数据，true 时标识存在， false 标识无更多数据
        }

        $data = []; // 返回结果
        foreach ($result as $k=>$v) {
            $data[] = [
                'loan_id' => $v['id'], // 借款Id
                'state' => $v['state'], // 当前状态
                'state_time' => strtotime($v['lending_at']), // 放款时间
            ];
            if ($v['state'] == LoanModel::STATE_FINISHED) {
                $data[$k]['quota'] = $v['repayed_amount']; // 借款订单为已完成状态时，用户还款金额以用户已还款金额为准，无需另外计算
            } else {
                # 还款金额
                $needAmount = LoanService::caculateRepaymentAmountDetail($v['id'])['repaymentAmount'] ?? 0; // 还款金额
                $data[$k]['quota'] = $needAmount; // 还款金额
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

        $data = [];
        if ($result) {
            $data = [
                'quota' => $result->quota ?? 0, // 借款金额
                'period' => $result->period ?? 0, // 借款期限
                'bank' => ($result->userBank->bank_name ?? '') . (isset($result->userBank->bank_no) ? '（尾号' . substr($result->userBank->bank_no, -4) . '）' : ''), // 到账银行,形如 招商银行（尾号0012）
                'lending_at' => strtotime($result->lending_at) ?? '', // 到帐时间
                'planned_repayment_at' => strtotime($result->planned_repayment_at), // 计划还款时间
                'state' => $result->state, // 当前状态
            ];
        }

        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            "error_message" => "",
            'results' => $data ? [$data] : [],
        ]);
    }

}
