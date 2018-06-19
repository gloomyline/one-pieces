<?php
namespace common\models;
use common\bases\CommonModel;
use common\services\SiteService;
use Yii;

class BudgetPlanModel extends CommonModel
{
    const STATE_WAITING = 'waiting'; //本期借款状态：waiting-等待中
    const STATE_REPAYING = 'repaying'; //本期借款状态：repaying-还款中
    const STATE_OVERDUE = 'overdue'; //本期借款状态：overdue-逾期中
    const STATE_FINISHED = 'finished'; //本期借款状态：finished-已还完

    const TYPE_UNKNOWN = 0; // 还款方式，0-未还款 1-正常还款 2-提前还款
    const TYPE_NORMAL = 1; // 还款方式，0-未还款 1-正常还款 2-提前还款
    const TYPE_EARLY = 2; // 还款方式，0-未还款 1-正常还款 2-提前还款

    /**
     * 添加还款分期计划
     * @param array $params 字段名=>值  key=>value
     * @return boolean true-添加成功 false-添加失败
     */
    public static function addBudgetPlan($params)
    {
        $budgetPlan = new BudgetPlan();
        $budgetPlan->user_id = $params['user_id']; // 用户ID
        $budgetPlan->loan_id = $params['loan_id']; // 借款ID
        $budgetPlan->term = $params['term']; // 期数
        $budgetPlan->monthly = $params['monthly']; // 月供
        $budgetPlan->principal = $params['principal']; // 本期本金
        $budgetPlan->interest = $params['interest']; // 本期借款息费
        $budgetPlan->trial_fee = $params['trial_fee']; // 信审费用
        $budgetPlan->service_fee = $params['service_fee']; // 服务费用
        $budgetPlan->poundage_fee = $params['poundage_fee']; // 手续费用
        $budgetPlan->interest_fee = $params['interest_fee']; // 利息
        $budgetPlan->state = $params['state']; // 本期还款状态
        $budgetPlan->begin_repayment_at = $params['begin_repayment_at']; // 开始计息时间
        $budgetPlan->planned_repayment_at = $params['planned_repayment_at']; // 还款到期日
        $budgetPlan->created_at = date('Y-m-d H:i:s');
        if ($budgetPlan->save()) {
            return true;
        }
        return false;
    }

    /**
     * 获取本期待还款的借款编号
     * @param integer $userId 用户ID
     * @return array|null 返回loanId的数组
     */
    public static function getCurrentTermLoanId($userId)
    {
        $currentTermRepayingAt = SiteService::getRecentRepayingDate(date('Y-m-d')); // 本期还款日
        return BudgetPlan::find()
                          ->select('loan_id')
                          ->where(['user_id' => $userId])
                          ->andWhere(['or', 'state="' . self::STATE_REPAYING . '"', 'state="' . self::STATE_OVERDUE . '"'])
                          ->andWhere(['<=', 'planned_repayment_at', $currentTermRepayingAt])
                          ->asArray()
                          ->all();
    }

    /**
     * 根据借款ID 获取借款还款计划
     * @param integer $loanId 借款ID
     * @return array 返回借款还款计划信息
     */
    public static function getBudgetPlanByLoanId($loanId)
    {
        return BudgetPlan::find()->where(['loan_id' => $loanId])->asArray()->all();
    }

    /**
     * 更新还款计划部分信息（提前还款手续费、实际还款金额、实际还款时间）
     * @param integer $id 还款计划ID
     * @param double $prepaymentFee 提前还款手续费
     * @param double $repayedAmount 实际还款金额
     * @param string $repaymentAt 实际还款时间
     * @param integer $repayedType 还款方式
     * @param integer $state 还款状态
     * @return boolean 是否更新成功 true-是 false-否
     */
    public static function updateBudgetPlan($id, $prepaymentFee, $repayedAmount, $repaymentAt, $repayedType, $state)
    {
        $budget = BudgetPlan::findOne(['id' => $id]);
        $budget->prepayment_fee = $prepaymentFee;
        $budget->repayed_amount = $repayedAmount;
        $budget->repayment_at = $repaymentAt;
        $budget->repayed_type = $repayedType;
        $budget->state = $state;
        $budget->updated_at = date('Y-m-d H:i:s'); // 更新时间
        if ($budget->save()) {
            return true;
        }
        return false;
    }

    /**
     * 根据计划ID 查询分期计划
     * @param array|integer $ids 分期计划ID
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getBudgetPlanByIds($ids)
    {
        return BudgetPlan::find()->where(['id' => $ids])->asArray()->all();
    }

    /**
     * 根据条件查询分期计划
     * @param array $conditions 条件
     * @return array|\yii\db\ActiveRecord[] 返回查询的结果
     */
    public static function getBudgetPlanByCondition($conditions)
    {
        return BudgetPlan::find()->with('loan')->with('user')->where($conditions)->asArray()->all();
    }

    /**
     * 设置计划逾期
     * @param integer $id 计划ID
     * @return bool 是否设置成功，true-成功 false-失败
     */
    public static function setOverdueStateById($id)
    {
        $budgetPlan =BudgetPlan::findOne($id);
        $budgetPlan->state = BudgetPlanModel::STATE_OVERDUE;
        $budgetPlan->updated_at = date('Y-m-d H:i:s');
        if ($budgetPlan->save()) {
            return true;
        }
        return false;
    }


    /**
     * 分期计划逾期记录
     * @param integer $offset 偏移量
     * @param integer $limit 条数
     * @param string $realName 真实姓名
     * @param string $mobile 手机号
     * @param string $beginAt 计划还款截止时间
     * @param string $endAt 计划还款结束时间
     * @param integer $budgetPlanId 借款分期计划id
     * @return array 返回数组【记录数，满足条件的记录数据对象】
     */
    public static function getOverdueAll($offset, $limit, $realName, $mobile, $beginAt, $endAt, $budgetPlanId)
    {
        $loan = BudgetPlan::find()
            ->joinWith('user')
            ->with('urge', 'loan.orderDetail', 'loan.shop')
            ->andWhere(['budget_plan.state' => self::STATE_OVERDUE]);

        if ($budgetPlanId != '') {
            $loan->andWhere(['budget_plan.id' => intval($budgetPlanId)]); // 真实姓名
        }
        if ($realName != '') {
            $loan->andWhere(['user.real_name' => trim($realName)]); // 真实姓名
        }
        if ($mobile != '') {
            $loan->andWhere(['user.mobile' => trim($mobile)]); // 手机号
        }
        if ($beginAt != '') {
            $beginAt = date('Y-m-d H:i:s', (int)($beginAt)); // 时间戳转字符串
            $loan->andWhere(['>=', 'budget_plan.planned_repayment_at', $beginAt]); // 预计还款 起始时间
        }
        if ($endAt != '') {
            $endAt = date('Y-m-d H:i:s', (int)($endAt) + 3600 * 24 - 1); // 时间戳转字符串, 截止时间设置为当前日期的 23:59:59，如 2017-09-20 23:59:59
            $loan->andWhere(['<=', 'budget_plan.planned_repayment_at', $endAt]); // 预计还款 截止时间
        }
        return [
            'count' => $loan->count(),
            'result' => $loan->offset($offset)->limit($limit)->orderBy(['budget_plan.loan_id' => SORT_DESC])->all() // 查询的结果
        ];

    }

    /**
     * 根据分期计划id获取分期计划详情
     * @param integer $budgetPlanId 分期计划id
     * @return array|null|\yii\db\ActiveRecord 满足条件的分期计划
     */
    public static function findBudgetWithLoanAndUserById($budgetPlanId)
    {
        return BudgetPlan::find()->where(['id' => $budgetPlanId ])->with('loan', 'user')->one();
    }

    /**
     * 某日还款总额
     * @param string $date 还款日期
     * @return array|null|\yii\db\ActiveRecord 【某日还款总额，还款总笔数】
     */
    public static function statRepaymentByDate($date = '')
    {
        return BudgetPlan::find()
            ->select("sum(repayed_amount) as today_repayment , count(id) as count")
            ->where(["DATE_FORMAT(`repayment_at` ,'%Y-%m-%d')" => $date])
            ->asArray()
            ->one();
    }

    /**
     * 本期逾期(上期应还未还)
     * @param string $date 上个还款日期
     * @return array|null|\yii\db\ActiveRecord 【上期应还未还总额，笔数】
     */
    public static function statOverdueByDate($date = '')
    {
        return BudgetPlan::find()
            ->select('sum(monthly) as today_overdue , count(budget_plan.id) as count')
            ->where(['budget_plan.state' => self::STATE_OVERDUE, 'planned_repayment_at' => $date])
            ->asArray()
            ->one();
    }

    /**
     * 本期待还总额（逾期中+还款中）
     * @param string $date 本期还款日
     * @return array|null|\yii\db\ActiveRecord 【本期待还总额，笔数】
     */
    public static function todayPlanRepayment($date = '')
    {
        return BudgetPlan::find()
            ->select('sum(monthly) as today_plan_repayment , count(id) as count')
            ->where(['and', ['state' => [self::STATE_REPAYING, self::STATE_OVERDUE]], ['<=', 'planned_repayment_at', $date]])
            ->asArray()
            ->one();
    }

    /**
     * 获取最新的逾期记录
     * @param int $limit 查询的记录数
     * @param array $orderBy 排序规则
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getLastOverdue($limit = 2, $orderBy = ['id' => SORT_DESC])
    {
        return BudgetPlan::find()
            ->where(['state' => self::STATE_OVERDUE])
            ->with('user', 'loan')
            ->orderBy($orderBy)
            ->limit($limit)
            ->all();
    }
    /**
     * 获取最近数据库七天的还款数据
     * @return array 返回日期，当日还款总额
     */
    public static function dailyRepayment()
    {
        $sql = "SELECT DATE_FORMAT( `repayment_at`, '%Y-%m-%d' ) AS `dates`, sum( `repayed_amount` ) as repayed_amount FROM `budget_plan` WHERE `repayment_at` > 0 GROUP BY `dates` order by `dates` desc LIMIT 7";
        return  Yii::$app->db->createCommand($sql)->queryAll();
    }
    /**
     * 累计还款总额/笔数
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function repaymentTotal()
    {
        return BudgetPlan::find()
            ->select('sum(repayed_amount) as repayment , count(id) as count')
            ->where(['state' => self::STATE_FINISHED])
            ->asArray()
            ->one();
    }

    /**
     * 累计逾期未还款总额/笔数
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function overdueTotal()
    {
        return BudgetPlan::find()
            ->select('sum(monthly) as overdue , count(id) as count')
            ->where(['state' => self::STATE_OVERDUE])
            ->asArray()
            ->one();
    }
    /**
     * 累计待还款总额/笔数(逾期+还款中)
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function planRepayment()
    {
        return BudgetPlan::find()
            ->select('sum(monthly) as plan_repayment , count(id) as count')
            ->where(['state' => [self::STATE_REPAYING, self::STATE_OVERDUE]])
            ->asArray()
            ->one();
    }

    /**
     * 获取最近半年还款---数据库半年的数据
     * @return array 返回返回日期，月还款总额
     */
    public static function monthRepayment()
    {
        $sql = "SELECT DATE_FORMAT( `repayment_at`, '%Y-%m' ) AS `dates`, sum( `repayed_amount` ) as repayed_amount FROM `budget_plan` WHERE `repayment_at` > 0 GROUP BY `dates` order by `dates` desc  LIMIT 6";
        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    /**
     * 某日逾期还款金额、笔数
     * @param string $date 日期
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function statRepaymentOverdueByDate($date = '')
    {
        return BudgetPlan::find()
            ->select("sum(repayed_amount) as today_repayment_overdue , count(id) as count")
            ->where(["DATE_FORMAT(`repayment_at` ,'%Y-%m-%d')" => $date])
            ->andWhere(['<', 'planned_repayment_at', $date])
            ->asArray()
            ->one();
    }
}