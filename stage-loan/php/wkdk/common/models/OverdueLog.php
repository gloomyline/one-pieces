<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "overdue_log".
 *
 * @property string $id
 * @property string $loan_id
 * @property string $plan_id
 * @property integer $overdue_days
 * @property string $overdue_fees
 * @property string $updated_at
 * @property string $created_at
 */
class OverdueLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'overdue_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['loan_id', 'plan_id', 'overdue_days'], 'integer'],
            [['overdue_fees'], 'number'],
            [['updated_at', 'created_at'], 'safe'],
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
            'plan_id' => 'Plan ID',
            'overdue_days' => 'Overdue Days',
            'overdue_fees' => 'Overdue Fees',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }

    /**
     * 关联借款表
     * @return \yii\db\ActiveQuery
     */
    public function getLoan()
    {
        return $this->hasOne(Loan::className(), ['id' => 'loan_id']);
    }
    public function getBudgetPlan()
    {
        return $this->hasOne(BudgetPlan::className(), ['id' => 'plan_id']);
    }
}
