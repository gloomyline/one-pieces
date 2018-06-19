<?php
namespace common\models;
use Yii;

/**
 * This is the model class for table "budget_plan".
 *
 * @property integer $id ID
 * @property integer $user_id 用户ID
 * @property integer $loan_id 借款ID
 * @property integer $term 期数
 * @property double $monthly 月供（单位：元）
 * @property double $principal 本期本金
 * @property double $interest 本期借款息费 = 信审费用 + 服务费用 + 手续费用 + 利息
 * @property double $trial_fee  信审费用
 * @property double $service_fee 服务费用
 * @property double $poundage_fee 手续费用
 * @property double $interest_fee 利息
 * @property double $prepayment_fee 提前还款手续费
 * @property double $repayed_amount 本期实际还款金额
 * @property string $state 本期借款状态：waiting-等待中 repaying-还款中 finished-已还完
 * @property string $repayed_type 还款方式，0-未还款 1-正常还款 2-提前还款
 * @property string $begin_repayment_at 开始还款时间
 * @property string $planned_repayment_at 计划还款时间
 * @property string $repayment_at 实际还款时间
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class BudgetPlan extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'budget_plan';
    }

    public function rules()
    {
        return [
            [['user_id', 'loan_id', 'term', 'repayed_type'], 'integer'],
            [['monthly', 'principal', 'interest', 'repayed_amount', 'trial_fee', 'service_fee', 'poundage_fee', 'interest_fee', 'prepayment_fee'], 'double'],
            [['state'], 'string', 'max'=>20],
            [['begin_repayment_at', 'planned_repayment_at'], 'string', 'max'=>10],
            [['repayment_at', 'created_at', 'updated_at'], 'string', 'max'=>19],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'loan_id' => 'Loan ID',
            'term' => 'Term',
            'monthly' => 'Monthly',
            'principal' => 'Principal',
            'interest' => 'Interest',
            'trial_fee' => 'Trial Fee',
            'service_fee' => 'Service Fee',
            'poundage_fee' => 'Poundage Fee',
            'interest_fee' => 'Interest Fee',
            'prepayment_fee' => 'Prepayment Fee',
            'repayed_amount' => 'Repayed Amount',
            'state' => 'State',
            'repayed_type' => 'Repayed Type',
            'begin_repayment_at' => 'Begin Repayment At',
            'planned_repayment_at' => 'Planned Repayment At',
            'repayment_at' => 'Repayment At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    public function getLoan()
    {
        return $this->hasOne(Loan::className(), ['id' => 'loan_id']);
    }

    /**
     * 关联逾期催收表
     * @return \yii\db\ActiveQuery
     */
    public function getUrge()
    {
        return $this->hasOne(Urge::className(), ['budget_plan_id' => 'id']);
    }

}