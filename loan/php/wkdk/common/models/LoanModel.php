<?php

namespace common\models;

use common\bases\CommonModel;
use common\services\LoanService;
use yii\helpers\Json;
use Yii;

class LoanModel extends CommonModel
{
    const STATE_AUDITING = 'auditing'; //待初审
    const STATE_AUDIT_FAILURE = 'audit_failure'; //初审失败
    const STATE_REVIEWING = 'reviewing'; //待复审
    const STATE_REVIEW_FAILURE = 'review_failure'; //复审失败
    const STATE_REVIEW_SUCCESS = 'review_success'; //复审成功
    const STATE_GRANTING = 'granting'; //放款中
    const STATE_REPAYING = 'repaying'; //还款中
    const STATE_FINISHED = 'finished'; //已还完
    const STATE_OVERDUE = 'overdue'; //逾期

    const WAY_PRINCIPAL_INTEREST_DUE = 1; // 还款方式： 1-到期本息

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
     * @param double $interest 信申费率
     * @param object $product 产品配置
     * @return boolean
     */
    public static function addLoan($userId, $encoding, $quota, $period, $interest, $product)
    {
        $model = new Loan();
        $model->user_id = $userId;
        $model->encoding = $encoding;
        $model->quota = $quota;
        $model->period = $period;
        $model->interest = $interest;

        $model->annualized_interest_rate = $product->annualized_interest_rate;
        $model->trial_rate = $product->trial_rate;
        $model->service_rate = $product->service_rate;
        $model->overdue_rate = $product->overdue_rate;
        $model->poundage = $product->poundage;

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
     * @return array 返回查询的结果集
     */
    public static function getAllLoan($offset, $limit, $real_name, $mobile, $state)
    {
        $loan = Loan::find()
                ->joinWith('user')
                ->with('preliminaryOfficer')
                ->with('reviewOfficer');

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
     * @return array 返回查询的结果集
     */
    public static function getAuditLoan($offset, $limit, $real_name, $mobile, $state, $quota, $begin_at, $end_at, $is_checks = true)
    {
        $loan = Loan::find()
                ->joinWith('user')
                ->with('preliminaryOfficer')
                ->with('reviewOfficer');

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
     * @return array 返回查询的结果集
     */
    public static function getRepaymentsLoan($offset, $limit, $real_name, $mobile, $state, $begin_at, $end_at)
    {
        $loan = Loan::find()->joinWith('user');

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
     * @param string $audit_opinion 审核意见
     * @return Json 成功或错误的信息
     */
    public static function setLoanState($id, $state, $audit_opinion ='')
    {
        $loan = Loan::findOne(['id' => $id]); // 借款记录ID
        if (!$loan) { // 若找不到记录
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        $loan->state = $state; // 设置状态
        switch ($state) {
            case 'audit_failure':
            case 'reviewing': {
               $loan->preliminary_officer =  \yii::$app->user->identity->getId(); // 审核员ID
               $loan->check_at = date('Y-m-d H:i:s'); // 设置初审时间
               $loan->preliminary_opinion = $audit_opinion; // 初审意见
               break;
            }
            case 'review_failure': {
               $loan->review_officer =  \yii::$app->user->identity->getId(); // 审核员ID
               $loan->review_at = date('Y-m-d H:i:s'); // 设置复审时间
               $loan->review_opinion = $audit_opinion; // 复审意见
               UserModel::thawUserQuota($loan->user_id, $loan->quota); // 解冻用户额度
               break;
            }
            case 'review_success': {
               $loan->review_officer =  \yii::$app->user->identity->getId(); // 审核员ID
               $loan->review_at = date('Y-m-d H:i:s'); // 设置复审时间
               $loan->review_opinion = $audit_opinion; // 复审意见
               UserModel::setUserState($loan->user_id, UserModel::STATE_NORMAL); // 设置复审成功时，更新用户状态为正常状态
               break;
            }
            case 'granting': $loan->lending_at = date('Y-m-d H:i:s');break; // 设置放款时间
            case 'repaying': $loan->repayment_at = date('Y-m-d H:i:s');break; // 设置实际还款时间
        }
        if ($loan->validate()) { // 验证rules
            if ($loan->save()) { // 保存状态
                if ($state == 'audit_failure' || $state == 'review_failure') { // 审核失败时，添加日志
                    $loanService = new LoanService();
                    $loanService->generateAuditFailureLog($id, time());
                }
                if ($state == 'review_success') { // 复审成功 表示审核成功，添加日志
                    $loanService = new LoanService();
                    $loanService->generateAuditSuccessLog($id);
                }
            } else { // 保存失败
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
                            ->with('userMobileReport');
        $loan->where(['loan.id' => $id]);
        $result = $loan->all();
        return $result;
    }

    /**
     * 按照用户ID查询用户所有的还款记录信息
     * @param int $id 用户ID
     * @param string $order 记录排序方式 默认值【按照借款Id升序】
     * @param int $offset 查询的基准数
     * @param int $limit 查询的记录数
     * @return array 返回所有记录
     */
    public static function findRepaymentsByUserId($id, $order = 'loan.id asc', $offset = 0, $limit = 10)
    {
        // 还款记录
        return Loan::find()
            ->where(['loan.user_id' => $id])
            ->andWhere(['or', 'loan.state=\''.self::STATE_REPAYING.'\'', 'loan.state=\''.self::STATE_FINISHED.'\''])
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
            ->with('userBank')
            ->where(['loan.id' => $loanId])
            ->one();
    }

    /**
     * 获取逾期记录的贷款信息
     * @param integer $offset 查询的基准数
     * @param integer $limit 查询记录数
     * @param string $realName 真实姓名
     * @param string $mobile 手机号
     * @param string $beginAt 预计还款起始时间
     * @param string $endAt 预计还款结束时间
     * @return array|\yii\db\ActiveRecord[] 返回所有的逾期记录的数组
     */
    public static function getOverdueAll($offset, $limit, $realName, $mobile, $beginAt, $endAt, $id)
    {
        $loan = Loan::find()
            //->select(['*', 'datediff( curdate() , planned_repayment_at ) as overdue_days , loan.id as id'])
            ->joinWith('user')
            ->with('urge')
            ->andWhere(['loan.state' => self::STATE_OVERDUE]);

        if ($id != '') {
            $loan->andWhere(['loan.id' => intval($id)]); // 真实姓名
        }
        if ($realName != '') {
            $loan->andWhere(['user.real_name' => trim($realName)]); // 真实姓名
        }
        if ($mobile != '') {
            $loan->andWhere(['user.mobile' => trim($mobile)]); // 手机号
        }
        if ($beginAt != '') {
            $beginAt = date('Y-m-d H:i:s', (int)($beginAt)); // 时间戳转字符串
            $loan->andWhere(['>=', 'loan.planned_repayment_at', $beginAt]); // 预计还款 起始时间
        }
        if ($endAt != '') {
            $endAt = date('Y-m-d H:i:s', (int)($endAt) + 3600 * 24 - 1); // 时间戳转字符串, 截止时间设置为当前日期的 23:59:59，如 2017-09-20 23:59:59
            $loan->andWhere(['<=', 'loan.planned_repayment_at', $endAt]); // 预计还款 截止时间
        }
        return [
            'count' => $loan->count(),
            'result' => $loan->offset($offset)->limit($limit)->orderBy(['loan.id' => SORT_DESC])->all() // 查询的结果
        ];

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
     * @param string $date 日期
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
     * 某日还款总额，笔数
     * @param string $day 日期
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function statRepaymentByDate($date = '')
    {
        return Loan::find()
            ->select("sum(repayed_amount) as today_repayment , count(id) as count")
            ->where(["DATE_FORMAT(`repayment_at` ,'%Y-%m-%d')" => $date])
            ->asArray()
            ->one();
    }

    /**
     * 累计还款总额/笔数
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function repaymentTotal()
    {
        return Loan::find()
            ->select('sum(repayed_amount) as repayment , count(id) as count')
            ->where(['state' => self::STATE_FINISHED])
            ->asArray()
            ->one();
    }

    /**
     * 某日逾期还款金额、笔数
     * @param $date 日期
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function statRepaymentOverdueByDate($date = '')
    {
        return Loan::find()
            ->select("sum(repayed_amount) as today_repayment_overdue , count(id) as count")
            ->where(["DATE_FORMAT(`repayment_at` ,'%Y-%m-%d')" => $date])
            ->andWhere(['<', 'planned_repayment_at', $date])
            ->asArray()
            ->one();
    }

    /**
     * 今日拒绝总额/笔数---初审失败，复审失败
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
                        ["DATE_FORMAT(`check_at` ,'%Y-%m-%d')" => date('Y-m-d', time())],
                        ['=', 'state', self::STATE_AUDIT_FAILURE]
                    ],
                    [
                        'and',
                        ["DATE_FORMAT(`review_at` ,'%Y-%m-%d')" => date('Y-m-d', time())],
                        ['=', 'state', self::STATE_REVIEW_FAILURE]
                    ]
                ])->asArray()->one();
    }

    /**
     *累计拒绝总额、笔数
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function refuseTotal()
    {
        return Loan::find()
            ->select('sum(quota) as refuse , count(id) as count')
            ->where(['state' => [self::STATE_AUDIT_FAILURE,self::STATE_REVIEW_FAILURE]])
            ->asArray()
            ->one();
    }

    /**
     * 某日逾期（总额、笔数,总罚息）即计划还款时间等于昨天的逾期记录,逾期天数为一天
     * @param string $date 日期
     * @return array|null|\yii\db\ActiveRecord 返回逾期金额，逾期数量， 逾期罚息
     */
    public static function statOverdueByDate($date = '')
    {
        $yesterday = date('Y-m-d', (strtotime($date) - 3600 * 24)); // 计算前一天的日期
        return Loan::find()
            ->select('sum(arrival_amount + interest) as today_overdue , count(id) as count, sum(arrival_amount * overdue_rate) as overdue_penalty')
            ->where(['state' => self::STATE_OVERDUE, 'planned_repayment_at' => $yesterday])
            ->asArray()
            ->one();
    }

    /**
     * 累计逾期未还款总额/笔数
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function overdueTotal()
    {
        return Loan::find()
            ->select('sum(arrival_amount + interest) as overdue , count(id) as count')
            ->where(['state' => self::STATE_OVERDUE])
            ->asArray()
            ->one();
    }


    /**
     * 今日待还总额/笔数--计划还款时间为今天
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function todayPlanRepayment()
    {
        return Loan::find()
            ->select('sum(arrival_amount + interest) as today_plan_repayment , count(id) as count')
            ->where(['state' => self::STATE_REPAYING, 'planned_repayment_at' => date('Y-m-d', time())])
            ->asArray()
            ->one();
    }

    /**
     * 累计待还款总额/笔数
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function planRepayment()
    {
        return Loan::find()
            ->select('sum(arrival_amount + interest) as plan_repayment , count(id) as count')
            ->where(['state' => self::STATE_REPAYING])
            ->asArray()
            ->one();
    }

    /**
     * 根据状态获取借款笔数---待初审、待复审、逾期未还、待放款
     * @param string $state 借款订单状态
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
     * 获取最近数据库七天的还款数据
     * @return array 返回日期，当日还款总额
     */
    public static function dailyRepayment()
    {
        $sql = "SELECT DATE_FORMAT( `repayment_at`, '%Y-%m-%d' ) AS `dates`, sum( `repayed_amount` ) as repayed_amount FROM `loan` WHERE `repayment_at` > 0 GROUP BY `dates` order by `dates` desc LIMIT 7";
        return  Yii::$app->db->createCommand($sql)->queryAll();
    }

    /**
     * 获取最近半年还款---数据库半年的数据
     * @return array 返回返回日期，月还款总额
     */
    public static function monthRepayment()
    {
        $sql = "SELECT DATE_FORMAT( `repayment_at`, '%Y-%m' ) AS `dates`, sum( `repayed_amount` ) as repayed_amount FROM `loan` WHERE `repayment_at` > 0 GROUP BY `dates` order by `dates` desc  LIMIT 6";
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
     * 获取最新的逾期记录
     * @param int $limit 查询的记录数
     * @param array $orderBy 排序规则
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getLastOverdue($limit = 2, $orderBy = ['id' => SORT_DESC])
    {
        return Loan::find()
            ->where(['state' => self::STATE_OVERDUE])
            ->with('user')
            ->orderBy($orderBy)
            ->limit($limit)
            ->all();
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
     * @return boolean 是否成功设置 true-是 false-否
     */
    public static function setRepayingStateById($loanId, $arrivalAmount)
    {
        $loan = Loan::findOne(['id' => $loanId]);
        $loan->state = self::STATE_REPAYING; // 设置借款状态为 还款中
        $loan->arrival_amount = $arrivalAmount; // 更新到账金额
        $loan->lending_at = date('Y-m-d H:i:s'); // 更新放款时间
        $loan->planned_repayment_at = date('Y-m-d', (time() + $loan->period * 3600 * 24)); // 更新计划还款时间
        if (!$loan->save()) {
            return false;
        }
        return true;
    }

    /**
     * 设置借款状态为 已还完
     * @param integer $loanId 借款ID
     * @param integer $repayedAmount 还款金额
     * @return boolean 是否成功设置 true-是 false-否
     */
    public static function setFinishedStateById($loanId, $repayedAmount)
    {
        $loan = Loan::findOne(['id' => $loanId]);
        $loan->state = self::STATE_FINISHED; // 设置借款状态为 已还完
        $loan->repayed_amount = $repayedAmount; // 更新还款金额
        $loan->repayment_at = date('Y-m-d H:i:s');
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
     * @param integer $offset 查询的偏移量
     * @param integer $limit 查询的记录数
     * @param integer $id 用户id
     * @param array $orderBy 排序方式
     * @return array|\yii\db\ActiveRecord[] 查询成功返回贷款记录
     */
    public static function getUserLoanById($offset, $limit, $id, $orderBy = ['id' => SORT_DESC])
    {
        $model = Loan::find()->where(['user_id' => $id]);

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


}