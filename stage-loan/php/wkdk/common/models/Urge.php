<?php

namespace common\models;

use backend\models\Admin;
use Yii;

/**
 * This is the model class for table "urge".
 *
 * @property integer $id ID
 * @property integer $loan_id 借款ID
 * @property integer $admin_id 催收员ID
 * @property integer $budget_plan_id 分期计划id
 * @property integer $state 1:等待催收 2:催收未还款 3:催收已还款 4:坏账
 * @property integer $urge_count 催款次数
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class Urge extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'urge';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['loan_id', 'budget_plan_id', 'admin_id', 'state', 'urge_count'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'loan_id' => 'Loan ID',
            'budget_plan_id' => 'Budget Plan Id',
            'admin_id' => 'Admin ID',
            'state' => 'State',
            'urge_count' => 'Urge Count',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * 关联借还记录表
     * @return \yii\db\ActiveQuery
     */
    public function getLoan()
    {
        return $this->hasOne(Loan::className(), ['id' => 'loan_id']);
    }

    /**
     * 关联管理员列表
     * @return \yii\db\ActiveQuery
     */
    public function getAdmin()
    {
        return $this->hasOne(Admin::className(), ['id' => 'admin_id']);
    }

    public function getBudgetPlan()
    {
        return $this->hasOne(BudgetPlan::className(), ['id' => 'budget_plan_id']);
    }
}
