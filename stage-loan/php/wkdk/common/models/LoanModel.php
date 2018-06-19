<?php

namespace common\models;

use common\bases\CommonModel;
use yii\helpers\Json;
use Yii;

class LoanModel extends CommonModel
{
    const STATE_AUDITING = 'auditing'; //待初审
    const STATE_AUDIT_FAILURE = 'audit_failure'; //初审失败
    const STATE_REVIEWING = 'reviewing'; //待复审
    const STATE_REVIEW_FAILURE = 'review_failure'; //复审失败
    const STATE_REVIEW_SUCCESS = 'review_success'; //复审成功
    const STATE_CONFIRMING = 'confirming'; //商家确认中
    const STATE_CONFIRM_SUCCESS = 'confirm_success'; //商家确认通过
    const STATE_CONFIRM_FAILURE = 'confirm_failure'; //商家确认未通过
    const STATE_GRANTING = 'granting'; //放款中
    const STATE_REPAYING = 'repaying'; //还款中
    const STATE_FINISHED = 'finished'; //已还完
    const STATE_OVERDUE = 'overdue'; //逾期

    const WAY_PRINCIPAL_INTEREST_DUE = 1; // 还款方式： 1-等本等息
    const TYPE_CASH = 1; // 借款分类 1：现金分期 2：消费分期
    const TYPE_CONSUMPTION = 2; // 借款分类 1：现金分期 2：消费分期

    const USE_CONSUMPTION = '线下分期'; // type = 2 消费分期时的借款用途，统一为 线下分期

    const RESULT_AUDIT_PASS = 1; // 初审结果 1-通过 2-不通过
    const RESULT_AUDIT_NOPASS = 2; // 初审结果 1-通过 2-不通过
    const RESULT_TEL_PASS = 1; // 电审结果 1-通过 2-不通过
    const RESULT_TEL_NOPASS = 2; // 电审结果 1-通过 2-不通过

    /**
     * 获取用户的有效借款订单
     * @param string $userId 用户ID
     * @return object|boolean
     */
    public static function getUserActiveLoan($userId)
    {
        $model = Loan::find();
        $model->Where(['user_id' => $userId]);
        $model->andWhere(['<>', 'state', self::STATE_FINISHED]);
        $model->orderBy('id desc');
        return $model->one();
    }

    /**
     * 获取用户最新的借款订单
     * @param string $userId 用户ID
     * @return object|boolean
     */
    public static function getUserLatestLoan($userId)
    {
        $model = Loan::find();
        $model->Where(['user_id' => $userId]);
        $model->orderBy('id desc');
        $model->limit(1);
        return $model->one();
    }

    /**
     * 添加借款订单
     * @param string $userId 用户ID
     * @param string $encoding 订单编号
     * @param int $quota 借款额度
     * @param int $period 借款期限
     * @param double $use 用途
     * @param object $product 产品配置
     * @param integer $shopId 商家ID 默认值【0】表示与商家无关，如现金分期时，默认值0
     * @return boolean
     */
    public static function addLoan($userId, $type, $encoding, $quota, $period, $use, $product, $shopId = 0)
    {
        $model = new Loan();
        $model->user_id = $userId;
        $model->encoding = $encoding;
        $model->quota = $quota;
        $model->period = $period;
        $model->type = $type;
        $model->use = $use;

        $model->annualized_interest_rate = $product->annualized_interest_rate;
        $model->trial_rate = $product->trial_rate;
        $model->service_rate = $product->service_rate;
        $model->overdue_rate = $product->overdue_rate;
        $model->poundage = $product->poundage;
        if ($shopId > 0) {
            $model->shop_id = $shopId;
        }

        $res =  $model->save();
        if ($res) {
            return $model;
        } else {
            return false;
        }
    }

    /**
     * 获取全部借款订单
     * @param int $offset 查询的基准数
     * @param int $limit 查询的记录数
     * @param string $real_name 真实姓名
     * @param string $mobile 手机号
     * @param string $state 借款状态
     * @param string|integer $type 消费类型
     * @param string|integer $orderState 订单状态
     * @return array 返回查询的结果集
     */
    public static function getAllLoan($offset, $limit, $real_name, $mobile, $state, $type, $orderState)
    {
        $loan = Loan::find()
            ->joinWith('user')
            ->with('preliminaryOfficer')
            ->with('reviewOfficer')
            ->with('shop')
            ->joinWith('shopOrderLog');

        if ($real_name != '') {
            $loan->andWhere(['user.real_name' => trim($real_name)]); // 真实姓名
        }
        if ($mobile != '') {
            $loan->andWhere(['like', 'user.mobile', trim($mobile)]); // 手机号
        }
        if ($state != '') {
            if ($state != 'all') {
                $loan->andWhere(['loan.state' => trim($state)]); // 借款状态
            }
        }
        if ($type != '') {
            if ($type != 'all') {
                $loan->andWhere(['loan.type' => (integer)trim($type)]); // 借款状态
            }
        }
        if ($orderState != '') {
            if ($orderState != 'all') {
                $orderState = (integer)trim($orderState);
                // 查询 已确认通过订单
                if ($orderState == 1) {
                    // 确认订单通过、未收货、已收货
                    $orderState = [ShopOrderLogModel::STATE_CONFIRM_SUCCESS, ShopOrderLogModel::STATE_RECEIVING_SUCCESS, ShopOrderLogModel::STATE_RECEIVING_FAILURE];
                }
                $loan->andWhere(['shop_order_log.state' => $orderState]); // 借款状态
            }
        }

        return [
            'count' => $loan->count(),
            'result' =>  $loan->offset($offset)->limit($limit)->orderBy(['id' => SORT_DESC])->all() // 查询的结果
        ] ;

    }

    /**
     * 获取借款初审/复审订单（待初审、初审失败、待复审、复审成功、复审失败）
     * @param int $offset 查询的基准数
     * @param int $limit 查询的记录数
     * @param string $real_name 真实姓名
     * @param string $mobile 手机号
     * @param string $state 借款状态
     * @param string $quota 申请金额
     * @param string $begin_at 申请起始时间
     * @param string $end_at 申请截止时间
     * @param boolean $is_checks 标识初审或复审，true：初审、false：复审
     * @param integer|string $orderState 订单状态
     * @return array 返回查询的结果集
     */
    public static function getAuditLoan($offset, $limit, $real_name, $mobile, $state, $quota, $begin_at, $end_at, $is_checks = true, $orderState = '')
    {
        $loan = Loan::find()
                ->joinWith('user')
                ->with('preliminaryOfficer')
                ->with('reviewOfficer')
                ->with('orderDetail')
                ->joinWith('shopOrderLog');

        if ($real_name != '') {
            $loan->andWhere(['user.real_name' => trim($real_name)]); // 真实姓名
        }
        if ($mobile != '') {
            $loan->andWhere(['user.mobile' => trim($mobile)]); // 手机号
        }
        if ($state != '') {
            if ($state != 'all') {
                $loan->andWhere(['loan.state' => trim($state)]); // 借款状态
            }
        }
        if ($quota != 0) {
            $loan->andWhere(['loan.quota' => trim($quota)]); // 申请金额
        }
        if ($begin_at != '') {
            $begin_at = date('Y-m-d H:i:s', (int)($begin_at)); // 时间戳转字符串
            $loan->andWhere(['>=', 'loan.created_at', $begin_at]); // 申请起始时间
        }
        if ($end_at != '') {
            $end_at = date('Y-m-d H:i:s', (int)($end_at) + 3600 * 24 - 1); // 时间戳转字符串, 截止时间设置为当前日期的 23:59:59，如 2017-09-20 23:59:59
            $loan->andWhere(['<=', 'loan.created_at', $end_at]); // 申请截止时间
        }
        if ($is_checks) { // 初审记录
            $loan ->andWhere(['or', 'loan.state=\'auditing\'', 'loan.state=\'audit_failure\'']); // 查询待初审、初审
        } else { // 复审记录
            $loan ->andWhere(['or', 'loan.state=\'reviewing\'', 'loan.state=\'review_failure\'', 'loan.state=\'review_success\'']); // 查询待复审、复审
        }

        if ($orderState != '') {
            if ($orderState != 'all') {
                $orderState = (integer)trim($orderState);
                // 查询 已确认通过订单
                if ($orderState == 1) {
                    // 确认订单通过、未收货、已收货
                    $orderState = [ShopOrderLogModel::STATE_CONFIRM_SUCCESS, ShopOrderLogModel::STATE_RECEIVING_SUCCESS, ShopOrderLogModel::STATE_RECEIVING_FAILURE];
                }
                $loan->andWhere(['shop_order_log.state' => $orderState]); // 借款状态
            }
        }


        return [
            'count' => $loan->count(),
            'result' => $loan->offset($offset)->limit($limit)->orderBy(['id' => SORT_DESC])->all() // 查询的结果
        ];

    }

    /**获取所有待放款的记录 复审通过 商家确认通过 放款中
     * @param integer $offset 查询的基准数
     * @param integer $limit 查询的记录数
     * @param string $real_name 用户名称
     * @param string $mobile 手机号
     * @param string $begin_at 申请开始时间
     * @param string $end_at 申请截止时间
     * @param integer $type 借款类型
     * @param string $state 借款状态
     * @return array 返回查询的结果集
     */
    public static function getGrantingLoan($offset, $limit, $real_name, $mobile, $begin_at, $end_at, $type, $state)
    {
        $loan = Loan::find()
            ->joinWith('user')
            ->with('orderDetail')
            ->joinWith('shopOrderLog')
            ->where([
                    'or',
                    ['loan.type' => self::TYPE_CASH, 'loan.state' => [self::STATE_REVIEW_SUCCESS, self::STATE_GRANTING]],
                    ['loan.type' => self::TYPE_CONSUMPTION, 'loan.state' => [self::STATE_CONFIRM_SUCCESS, self::STATE_GRANTING]]
            ]);

        if ($real_name != '') {
            $loan->andWhere(['user.real_name' => trim($real_name)]); // 真实姓名
        }
        if ($mobile != '') {
            $loan->andWhere(['user.mobile' => trim($mobile)]); // 手机号
        }
        if ($type != '') {
            $loan->andWhere(['loan.type' => intval($type)]); // 手机号
        }
        if ($state!= '') {
            $loan->andWhere(['loan.state' => trim($state)]); // 手机号
        }

        if ($begin_at != '') {
            $begin_at = date('Y-m-d H:i:s', (int)($begin_at)); // 时间戳转字符串
            $loan->andWhere(['>=', 'loan.created_at', $begin_at]); // 申请起始时间
        }
        if ($end_at != '') {
            $end_at = date('Y-m-d H:i:s', (int)($end_at) + 3600 * 24 - 1); // 时间戳转字符串, 截止时间设置为当前日期的 23:59:59，如 2017-09-20 23:59:59
            $loan->andWhere(['<=', 'loan.created_at', $end_at]); // 申请截止时间
        }

        return [
            'count' => $loan->count(),
            'result' => $loan->offset($offset)->limit($limit)->orderBy(['id' => SORT_DESC])->all() // 查询的结果
        ];

    }

    /**
     * 获取借款还款订单（还款中、已还完）
     * @param int $offset 查询的基准数
     * @param int $limit 查询的记录数
     * @param string $real_name 真实姓名
     * @param string $mobile 手机号
     * @param string $state 借款状态
     * @param string $begin_at 申请起始时间
     * @param string $end_at 申请截止时间
     * @param integer|string $type 消费类型
     * @return array 返回查询的结果集
     */
    public static function getRepaymentsLoan($offset, $limit, $real_name, $mobile, $state, $begin_at, $end_at, $type)
    {
        $loan = Loan::find()->joinWith('user')->joinWith('shopOrderLog');

        if ($real_name != '') {
            $loan->andWhere(['user.real_name' => trim($real_name)]); // 真实姓名
        }
        if ($mobile != '') {
            $loan->andWhere(['user.mobile' => trim($mobile)]); // 手机号
        }
        if ($state != '') {
            $loan->andWhere(['loan.state' => trim($state)]); // 借款状态
        }
        if ($begin_at != '') {
            $begin_at = date('Y-m-d H:i:s', (int)($begin_at)); // 时间戳转字符串
            $loan->andWhere(['>=', 'loan.repayment_at', $begin_at]); // 实际还款 起始时间
        }
        if ($end_at != '') {
            $end_at = date('Y-m-d H:i:s', (int)($end_at) + 3600 * 24 - 1); // 时间戳转字符串, 截止时间设置为当前日期的 23:59:59，如 2017-09-20 23:59:59
            $loan->andWhere(['<=', 'loan.repayment_at', $end_at]); // 实际还款 截止时间
        }

        if ($type != '') {
            if ($type != 'all') {
                $loan->andWhere(['loan.type' => (integer)trim($type)]); // 借款状态
            }
        }
        $loan ->andWhere(['or', 'loan.state=\'repaying\'', 'loan.state=\'finished\'']); // 查询还款中、已结束

        return [
            'count' => $loan->count(),
            'result' => $loan->offset($offset)->limit($limit)->orderBy(['id' => SORT_DESC])->all() // 查询的结果
        ];
    }

    /**
     * 设置借款状态
     * @param int $id 借款记录ID
     * @param string $state 变更的借款状态
     * @param string $auditOpinion 审核意见
     * @param integer $preliminaryResult 初审结果
     * @param string $telOpinion 电审意见
     * @param int $telResult 电审结果
     * @return Json 成功或错误的信息
     */
    public static function setLoanState($id, $state, $auditOpinion = '', $preliminaryResult = 0, $telOpinion = '', $telResult = 0)
    {
        $loan = Loan::findOne(['id' => $id]); // 借款记录ID
        if (!$loan) { // 若找不到记录
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        $loan->state = $state; // 设置状态
        switch ($state) {
            case 'audit_failure':
            case 'reviewing': {
               $loan->preliminary_officer =  Yii::$app->user->getId(); // 审核员ID
               $loan->check_at = date('Y-m-d H:i:s'); // 设置初审时间
               $loan->preliminary_opinion = $auditOpinion; // 初审意见
               $loan->preliminary_result = $preliminaryResult; // 初审结果
               $loan->tel_opinion = $telOpinion; // 电审意见
               $loan->tel_result = $telResult; // 电审意见
               break;
            }
            case 'review_failure': {
               $loan->review_officer =  Yii::$app->user->getId(); // 审核员ID
               $loan->review_at = date('Y-m-d H:i:s'); // 设置复审时间
               $loan->review_opinion = $auditOpinion; // 复审意见
               break;
            }
            case 'review_success': {
               $loan->review_officer =  Yii::$app->user->getId(); // 审核员ID
               $loan->review_at = date('Y-m-d H:i:s'); // 设置复审时间
               $loan->review_opinion = $auditOpinion; // 复审意见
               break;
            }
            case 'granting': $loan->lending_at = date('Y-m-d H:i:s');break; // 设置放款时间
            case 'repaying': $loan->repayment_at = date('Y-m-d H:i:s');break; // 设置实际还款时间
        }
        if ($loan->validate()) { // 验证rules
            if (!$loan->save()) { // 保存失败
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '设置失败']);
            }
        } else { // 验证失败
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => $loan->getErrors()]);
        }
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    /**
     * 按照借款ID 查找借款以及借款人相关明细信息
     * @param int $id 借款ID
     * @return object|boolean 返回查询的结果
     */
    public static function findDetailById($id)
    {
        $loan =  Loan::find()->joinWith('user')
                            ->with('userBasic')
                            ->with('userIdentityCard')
                            ->with('userBank')
                            ->with('userAdditional')
                            ->with('userMobileReport')
                            ->with('orderDetail')
                            ->with('shop');
        $loan->where(['loan.id' => $id]);
        $result = $loan->one();
        return $result;
    }

    /**
     * 按照用户ID查询用户所有的待还款记录信息
     * @param array $loanIds 借款ID
     * @param string $order 记录排序方式 默认值【按照借款Id升序】
     * @param int $offset 查询的基准数
     * @param int $limit 查询的记录数
     * @return array 返回所有记录
     */
    public static function findRepayingByLoanIds($loanIds, $order = 'loan.id asc', $offset = 0, $limit = 10)
    {
        // 还款记录
        return Loan::find()
            ->where(['in', 'id', $loanIds])
            ->andWhere(['or', 'state="' . self::STATE_REPAYING . '"', 'state="' . self::STATE_OVERDUE . '"'])
            ->offset($offset)
            ->limit($limit)
            ->orderBy($order)
            ->asArray()
            ->all();
    }

    /**
     * 按照用户ID查询用户所有的逾期记录信息
     * @param int $id 用户ID
     * @return array 返回所有记录
     */
    public static function findOverdueByUserId($id)
    {
        // 逾期记录
        return Loan::find()
            ->where(['loan.user_id' => $id])
            ->andWhere(['loan.state' => self::STATE_OVERDUE])
            ->asArray()
            ->all();
    }

    /**
     * 按照用户ID查询用户所有的借款记录信息
     * @param int $userId 用户ID
     * @param string $order 记录排序方式 默认值【按照借款Id升序】
     * @param int $offset 查询的基准数
     * @param int $limit 查询的记录数
     * @return array 返回所有记录
     */
    public static function findLoanByUserId($userId, $order = 'loan.id asc', $offset = 0, $limit = 10)
    {
        $result = Loan::find()
            ->where(['loan.user_id' => $userId])
            ->orderBy($order)
            ->offset($offset)
            ->limit($limit)
            ->all();
        return $result;
    }

    /**
     * 按照借款Id查询该借款明细信息
     * @param integer $loanId 借款Id
     * @return object|boolean 返回所有记录
     */
    public static function findLoanById($loanId)
    {
        return Loan::find()
            ->with('shop')
            ->with('userBank')
            ->with('user')
            ->with('budgetPlan')
            ->with('orderDetail')
            ->where(['loan.id' => $loanId])
            ->one();
    }
    
    /**
     * 更新用户最新有效订单信息
     * @param integer $loanId 借款ID
     * @param array $params 更新的参数
     * @return bool|integer 是否更新成功 true-是 false-否
     */
    public static function updateActiveLoanById($loanId, $params)
    {
        $loan = Loan::findOne(['id' => $loanId]);
        if (isset($params['arrival_amount'])) { // 到账金额
            $loan->arrival_amount = $params['arrival_amount'];
            $loan->lending_at = date('Y-m-d H:i:s');
        }
        if (isset($params['repayed_amount'])) { // 已还款金额
            $loan->repayed_amount = $params['repayed_amount'];
            $loan->repayment_at = date('Y-m-d H:i:s');
        }
        if (isset($params['state'])) { // 借款状态
            $loan->state = $params['state'];
        }

        if ($loan->validate()) {
            if (!$loan->save()) { return false; }
            return true;
        } else {
            Yii::error('LoanModel 更新用户最新有效订单失败：' . Json::encode($loan->getErrors()), 'lianlianpay');
            return false;
        }
    }

    /**
     * 通过借款Id变更当前借款状态为放款中
     * @param integer $loanId 借款ID
     * @param string $lendState 借款状态
     * @return boolean true-修改成功， false-修改失败
     */
    public static function updateLendingStateById($loanId, $lendState = '')
    {
        $model = Loan::findOne(['id' => $loanId]);

        if ($lendState) {
            $model->state = $lendState; // 状态变更为其他状态
        } else {
            $model->state = self::STATE_GRANTING; // 状态变更为放款中
        }
        $model->lending_at = date('Y-m-d H:i:s'); // 变更放款时间
        if (!$model->save()) { return false; }
        return true;
    }

    /**
     * 某日申请借款总额、笔数
     * @param string $date 日期 2018-01-18
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function statQuotaTotalByDate($date = '')
    {
        return Loan::find()
            ->select("sum(quota) as today_quota_total , count(id) as count")
            ->where(["DATE_FORMAT(`created_at` ,'%Y-%m-%d')" => $date])
            ->asArray()
            ->one();
    }

    /**
     * 统计某日借款人数
     * @param string $date 日期
     * @return int|string 返回记录条数
     */
    public static function statQuotaUserCountByDate($date = '')
    {
        return Loan::find()
            ->select('user_id')
            ->where(["DATE_FORMAT(`created_at` ,'%Y-%m-%d')" => $date])
            ->distinct()
            ->count();
    }

    /**
     * 累计申请总额/总笔数
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function quotaTotal()
    {
        return Loan::find()
            ->select('sum(quota) as quota_total , count(id) as count')
            ->asArray()
            ->one();
    }

    /**
     * 某日放款总额/笔数/借款息费
     * @param string $date 日期
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function statLoanTotalByDate($date = '')
    {
        return Loan::find()
            ->select("sum(arrival_amount) as today_loan_total , count(id) as count, sum(interest) as today_interest_total")
            ->where(["DATE_FORMAT(`lending_at` ,'%Y-%m-%d')" => $date])
            ->asArray()
            ->one();
    }

    /**
     * 累计放款总额/笔数
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function loanTotal()
    {
        return Loan::find()
            ->select('sum(arrival_amount) loan_total , count(id) as count')
            ->where(['>', 'lending_at', 0])
            ->asArray()
            ->one();
    }

    /**
     * 某日放款人数
     * @param string $date 日期
     * @return int|string
     */
    public static function statLoanUserCountByDate($date = '')
    {
        return Loan::find()
            ->select('user_id')
            ->where(["DATE_FORMAT(`lending_at` ,'%Y-%m-%d')" => $date])
            ->distinct()
            ->count();
    }


    /**
     * 今日拒绝总额/笔数---初审失败，复审失败,商家确认失败
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function todayRefuse()
    {
        return Loan::find()
                ->select('sum(quota) as today_refuse , count(id) as count')
                ->where(
                [
                    'or',
                    [
                        'and',
                        ["DATE_FORMAT(`check_at` ,'%Y-%m-%d')" => date('Y-m-d')],
                        ['=', 'state', self::STATE_AUDIT_FAILURE]
                    ],
                    [
                        'and',
                        ["DATE_FORMAT(`review_at` ,'%Y-%m-%d')" => date('Y-m-d')],
                        ['=', 'state', self::STATE_REVIEW_FAILURE]
                    ],
                    [
                        'and',
                        ["DATE_FORMAT(`confirmed_at` ,'%Y-%m-%d')" => date('Y-m-d')],
                        ['=', 'state', self::STATE_CONFIRM_FAILURE]
                    ],
                ])->asArray()->one();
    }

    /**
     *累计拒绝总额、笔数(初审失败+复审失败+商户确认失败)
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function refuseTotal()
    {
        return Loan::find()
            ->select('sum(quota) as refuse , count(id) as count')
            ->where(['state' => [self::STATE_AUDIT_FAILURE, self::STATE_REVIEW_FAILURE, self::STATE_CONFIRM_FAILURE]])
            ->asArray()
            ->one();
    }

    /**
     * 根据状态获取借款笔数---待初审、待复审、逾期未还、待放款
     * @param string|array $state 借款订单状态
     * @return int|string 返回查询记录条数没有为0
     */
    public static function getCountByState($state)
    {
        return Loan::find()->where(['state' => $state])->count();
    }

    /**
     * 获取最近数据库七天的借款数据
     * @return array 返回日期，当日放款总额
     */
    public static function dailyLoan()
    {
        $sql = "SELECT DATE_FORMAT( `lending_at`, '%Y-%m-%d' ) AS `dates`, sum( `arrival_amount` ) as loan_amount FROM `loan` WHERE `lending_at` > 0 GROUP BY `dates` order by `dates` desc LIMIT 7";
        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    /**
     * 获取最近半年放款---数据库半年的数据
     * @return array 返回月份，当月放款总额
     */
    public static function monthLoan()
    {
        $sql = "SELECT DATE_FORMAT( `lending_at`, '%Y-%m' ) AS `dates`, sum( `arrival_amount` ) as loan_amount FROM `loan` WHERE `lending_at` > 0 GROUP BY `dates` order by `dates` desc LIMIT 6";
        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    /**
     * 一周申请次数
     * @return array 返回日期，每天申请的次数
     */
    public static function dailyApply()
    {
        $sql = "SELECT DATE_FORMAT( `created_at`, '%Y-%m-%d' ) AS `dates`, COUNT(id) as count FROM `loan`  GROUP BY `dates` order by `dates` desc LIMIT 7";
        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    /**
     * 半年月申请次数
     * @return array 返回日期，每月申请次数
     */
    public static function monthApply()
    {
        $sql = "SELECT DATE_FORMAT( `created_at`, '%Y-%m' ) AS `dates`, COUNT(id) as count FROM `loan`  GROUP BY `dates` order by `dates` desc LIMIT 6";
        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    /**
     *一周拒绝统计--初审失败
     * @return array 返回日期，每天初审拒绝次数
     */
    public static function dailyCheckRefuse()
    {
        $sql = "SELECT DATE_FORMAT( `check_at`, '%Y-%m-%d' ) AS `dates`, COUNT(id) as count FROM `loan` WHERE `state` = 'audit_failure' GROUP BY `dates` order by `dates` desc LIMIT 7";
        return Yii::$app->db->createCommand($sql)->queryAll();

    }

    /**
     * 一周拒绝统计---复审失败
     * @return array 返回日期，每天复审拒绝次数
     */
    public static function dailyReviewRefuse()
    {
        $sql = "SELECT DATE_FORMAT( `review_at`, '%Y-%m-%d' ) AS `dates`, COUNT(id) as count FROM `loan` WHERE `state` = 'review_failure' GROUP BY `dates` order by `dates` desc LIMIT 7";
        return  Yii::$app->db->createCommand($sql)->queryAll();
    }

    /**
     * 一周拒绝---商家确认失败
     * @return array 返回日期，商户每天确认失败次数
     */
    public static function dailyConfirmRefuse()
    {
        $sql = "SELECT DATE_FORMAT( `confirmed_at`, '%Y-%m-%d' ) AS `dates`, COUNT(id) as count FROM `loan` WHERE `state` = 'confirm_failure' GROUP BY `dates` order by `dates` desc LIMIT 7";
        return  Yii::$app->db->createCommand($sql)->queryAll();
    }

    /**
     * 月初审拒绝次数
     * @return array 返回月份，当月拒绝次数
     */
    public static function monthCheckRefuse()
    {
        $sql = "SELECT DATE_FORMAT( `check_at`, '%Y-%m' ) AS `dates`, COUNT(id) as count FROM `loan` WHERE `state` = 'audit_failure' GROUP BY `dates` order by `dates` desc LIMIT 6";
        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    /**
     * 月复审拒绝次数
     * @return array 返回月份，当月复审失败次数
     */
    public static function monthReviewRefuse()
    {
        $sql = "SELECT DATE_FORMAT( `review_at`, '%Y-%m' ) AS `dates`, COUNT(id) as count FROM `loan` WHERE `state` = 'review_failure' GROUP BY `dates` order by `dates` desc LIMIT 6";
        return  Yii::$app->db->createCommand($sql)->queryAll();
    }

    /**
     * 月商户确认失败次数
     * @return array 返回月份，当月商户确认失败次数
     */
    public static function monthConfirmRefuse()
    {
        $sql = "SELECT DATE_FORMAT( `confirmed_at`, '%Y-%m' ) AS `dates`, COUNT(id) as count FROM `loan` WHERE `state` = 'confirm_failure' GROUP BY `dates` order by `dates` desc LIMIT 6";
        return  Yii::$app->db->createCommand($sql)->queryAll();
    }

    /*
     * 根据条件查询借款信息
     * @param integer $userId 用户ID
     * @param integer $loanId 借款ID
     * @return array| null
     */
    public static function findUserLoan($userId, $loanId)
    {
        $model = Loan::find();
        $model->where(['user_id' => $userId]);
        $model->andWhere(['id' => $loanId]);
        return $model->one();
    }

    /**
     * 设置借款状态为 待审核
     * @param integer $loanId 借款ID
     * @return boolean 是否成功设置 true-是 false-否
     */
    public static function setAuditingStateById($loanId)
    {
        $loan = Loan::findOne(['id' => $loanId]);
        $loan->state = self::STATE_AUDITING; // 设置借款状态为待初审
        $loan->check_at = date('Y-m-d H:i:s'); // 更新初审时间
        if (!$loan->save()) {
            return false;
        }
        return true;
    }

    /**
     * 设置借款状态为 初审失败
     * @param integer $loanId 借款ID
     * @return boolean 是否成功设置 true-是 false-否
     */
    public static function setAuditFailureStateById($loanId)
    {
        $loan = Loan::findOne(['id' => $loanId]);
        $loan->state = self::STATE_AUDIT_FAILURE; // 设置借款状态为 初审失败
        $loan->check_at = date('Y-m-d H:i:s'); // 更新初审时间
        if (!$loan->save()) {
            return false;
        }
        return true;
    }

    /**
     * 设置借款状态为 复审中
     * @param integer $loanId 借款ID
     * @return boolean 是否成功设置 true-是 false-否
     */
    public static function setReviewingStateById($loanId)
    {
        $loan = Loan::findOne(['id' => $loanId]);
        $loan->state = self::STATE_REVIEWING; // 设置借款状态
        $loan->review_at = date('Y-m-d H:i:s'); // 更新复审时间
        if (!$loan->save()) {
            return false;
        }
        return true;
    }

    /**
     * 设置借款状态为复审失败
     * @param integer $loanId 借款ID
     * @return boolean 是否成功设置 true-是 false-否
     */
    public static function setReviewFailureStateById($loanId)
    {
        $loan = Loan::findOne(['id' => $loanId]);
        $loan->state = self::STATE_REVIEW_FAILURE; // 设置借款状态为 复审失败
        $loan->review_at = date('Y-m-d H:i:s'); // 更新复审时间
        if (!$loan->save()) {
            return false;
        }
        return true;

    }

    /**
     * 设置借款状态为 复审成功
     * @param integer $loanId 借款ID
     * @return boolean 是否成功设置 true-是 false-否
     */
    public static function setReviewSuccessStateById($loanId)
    {
        $loan = Loan::findOne(['id' => $loanId]);
        $loan->state = self::STATE_REVIEW_SUCCESS; // 设置借款状态为 复审成功
        $loan->review_at = date('Y-m-d H:i:s'); // 更新复审时间
        if (!$loan->save()) {
            return false;
        }
        return true;

    }

    /**
     * 设置借款状态为放款中
     * @param integer $loanId 借款ID
     * @return boolean 是否成功设置 true-是 false-否
     */
    public function setGrantingStateById($loanId)
    {
        $loan = Loan::findOne(['id' => $loanId]);
        $loan->state = self::STATE_GRANTING; // 设置借款状态为放款中
        $loan->lending_at = date('Y-m-d H:i:s'); // 更新放款时间
        if (!$loan->save()) {
            return false;
        }
        return true;
    }

    /**
     * 设置借款状态为还款中
     * @param integer $loanId 借款ID
     * @param integer $arrivalAmount 到账金额
     * @param integer $interest 借款息费
     * @return boolean 是否成功设置 true-是 false-否
     */
    public static function setRepayingStateById($loanId, $arrivalAmount, $interest)
    {
        $loan = Loan::findOne(['id' => $loanId]);
        $loan->state = self::STATE_REPAYING; // 设置借款状态为 还款中
        $loan->arrival_amount = $arrivalAmount; // 更新到账金额
        $loan->interest = $interest; // 更新借款息费
        $loan->lending_at = date('Y-m-d H:i:s'); // 更新放款时间
        if (!$loan->save()) {
            return false;
        }
        return true;
    }

    /**
     * 更新还款成功相关字段信息
     * @param integer $loanId 借款ID
     * @param integer $repayedAmount 还款金额
     * @param string $planId 还款计划ID
     * @return boolean 是否成功设置 true-是 false-否
     */
    public static function setRepaymentRelatedById($loanId, $repayedAmount, $planId)
    {
        $termCount = count(explode(',', $planId)); // 本次还款还的期数

        $loan = Loan::findOne(['id' => $loanId]);
        $loan->repayed_count += $termCount; // 更新已还期数
        $loan->repayment_at = date('Y-m-d H:i:s'); // 更新用户还款时间
        // 若已还期数 = 借款期数，表示已还完，变更借款状态
        if ($loan->repayed_count == $loan->period) {
            $loan->state = LoanModel::STATE_FINISHED; // 设置借款状态为 已还完
        } else {
            // 若其中一项计划逾期，借款状态为逾期状态，但还款后且未结清借款时，借款因变更回还款中
            $loan->state = LoanModel::STATE_REPAYING; // 设置借款状态为 还款中
        }
        $loan->repayed_amount = $repayedAmount; // 更新还款金额
        if (!$loan->save()) {
            return false;
        }
        return true;
    }

    /**
     * 设置借款状态为 逾期中
     * @param integer $loanId 借款ID
     * @return boolean 是否成功设置 true-是 false-否
     */
    public static function setOverDueStateById($loanId) {
        $loan = Loan::findOne(['id' => $loanId]);
        $loan->state = self::STATE_OVERDUE; // 设置借款状态为 逾期中
        if (!$loan->save()) {
            return false;
        }
        return true;
    }

    /**
     * 通过用户id获取该用户的借款记录
     * @param $offset 查询的偏移量
     * @param $limit 查询记录数
     * @param $userId 用户id
     * @param array $orderBy
     * @return array 返回【满足条件的记录数，查询到用户借还记录】
     */
    public static function getUserLoanById($offset, $limit, $userId, $orderBy = ['id' => SORT_DESC])
    {
        $model = Loan::find()->with('shop', 'orderDetail')->where(['user_id' => $userId]);

        return [
            'count' => $model->count(),
            'result' => $model->offset($offset)->limit($limit)->orderBy($orderBy)->all()
        ];
    }

    /**
     * 根据借款id获取借款记录（关联user表）
     * @param $loanId 借款id
     * @return array|null|\yii\db\ActiveRecord 返回借款记录数据对象或者空对象
     */
    public static function findLoanByLoanId($loanId)
    {
        return $model = Loan::find()->with('user')->where(['id' => $loanId])->one();
    }

    /**
     * 获取商户订单记录
     * @param $offset
     * @param $limit
     * @param $shopId 商户id
     * @param $productName 产品名称
     * @param $beginAt 订单提交的开始时间
     * @param $endAt 订单提交的结束时间
     * @param $state 借款状态
     * @param $orderState 商户操作订单状态
     * @return array 返回数组【满足条件的记录数，满足条件的所有订单数据对象】
     */
    public static function getShopOrderList($offset, $limit, $shopId, $productName, $beginAt, $endAt, $state, $orderState)
    {
        $model = Loan::find()->where(['loan.shop_id' => $shopId, 'loan.type' => LoanModel::TYPE_CONSUMPTION])->with('user')->joinWith('orderDetail')->joinWith('shopOrderLog');
        if ($productName != '') {
            $model->andWhere(['like', 'order_detail.title', trim($productName)]); // 商品名称
        }
        if ($state != '') { // 借款状态
            $model->andWhere(['loan.state' => $state]); // 借款状态
        }
        if ($beginAt != '') {
            $beginAt = date('Y-m-d H:i:s', (int)($beginAt)); // 时间戳转字符串
            $model->andWhere(['>=', 'loan.created_at', $beginAt]); // 起始时间
        }
        if ($endAt != '') {
            $endAt = date('Y-m-d H:i:s', (int)($endAt) + 3600 * 24 - 1); // 时间戳转字符串, 截止时间设置为当前日期的 23:59:59，如 2017-09-20 23:59:59
            $model->andWhere(['<=', 'loan.created_at', $endAt]); //  截止时间
        }
        if ($orderState != '') { // 订单状态
            $model->andWhere(['shop_order_log.state' => $orderState]);
        }
        return [
            'count' => $model->count(),
            'result' => $model->offset($offset)->limit($limit)->orderBy(['loan.id' => SORT_DESC])->all()
        ];
    }

    /**
     * 修改借款信息
     * @param integer $loanId 借款id
     * @param array $data 修改的参数
     * @return bool 修改成功返回true 失败返回false
     */
    public static function update($loanId, $data)
    {
        $model = Loan::findOne(['id' => $loanId]);
        $model->setAttributes($data);
        if ($model->save()) {
            return true;
        }
        return false;
    }

    /**商户订单详情
     * @param $loanId 借款id
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function findShopOrderDetailByLoanId($loanId)
    {
        $model = Loan::find()->where(['id' => $loanId])->with('user', 'orderDetail', 'shopOrderLog')->one();
        return $model;
    }

    /**
     * 统计商户今日销售额度 笔数 放款成功
     * @param string $date 日期
     * @param $shopId 商户id
     * @return array|null|\yii\db\ActiveRecord 【销售额，销售笔数】
     */
    public static function statShopQuotaByDate($date = '', $shopId)
    {
        return Loan::find()
            ->select("sum(arrival_amount) as quota , count(id) as count")
            ->where(["DATE_FORMAT(`lending_at` ,'%Y-%m-%d')" => $date, 'shop_id' => $shopId, 'type'=>LoanModel::TYPE_CONSUMPTION])
            ->asArray()
            ->one();
    }

    /**
     * 商户累计销售
     * @param $shopId 商户id
     * @return mixed 销售总额/null
     */
    public static function statShopTotalQuota($shopId)
    {
        return Loan::find()
            ->where(['shop_id' => $shopId, 'type'=>LoanModel::TYPE_CONSUMPTION])
            ->sum('arrival_amount');
    }

    /**
     * 统计商户的用户总数
     * @param $shopId 商户id
     * @return int|string 商户的用户数
     */
    public static function countShopUser($shopId)
    {
        return Loan::find()->select('user_id')->where(['shop_id' => $shopId, 'type'=>LoanModel::TYPE_CONSUMPTION])->distinct()->count();
    }

    /**
     * 消费分期放款总额
     * @return int|mixed
     */
    public static function shopArrivalAmount()
    {
        return Loan::find()->where(['and', ['type' => self::TYPE_CONSUMPTION], ['>', 'lending_at', 0]])->sum('arrival_amount') ?? 0;
    }

    /**
     * 消费分期还款总额
     * @return int|mixed
     */
    public static function shopRepaymentAmount()
    {
        return  Loan::find()->where(['and', ['type' => self::TYPE_CONSUMPTION], ['>', 'lending_at', 0]])->sum('repayed_amount') ?? 0;
    }

    /**
     * 获得用户最新消费分期的有效订单
     * @param integer $userId 用户id
     * @return array|null|\yii\db\ActiveRecord 用户最新的一条消费分期借款信息及亲签照
     */
    public static function getLastConsumptionByUserId($userId)
    {
        $model = Loan::find();
        $model->where(['user_id' => $userId, 'type' => self::TYPE_CONSUMPTION])->with('visa');
        $model->orderBy('id desc');
        $model->limit(1);
        return $model->one();
    }
}