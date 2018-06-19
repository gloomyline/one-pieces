<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "statistics".
 *
 * @property string $id
 * @property string $created_at
 * @property integer $user_count
 * @property integer $loan_count
 * @property string $loan_interest
 * @property integer $loan_user
 * @property integer $lend_count
 * @property string $lend_amount
 * @property integer $lend_user
 * @property string $repayment_amount
 * @property integer $repayment_count
 * @property integer $repayment_overdue_count
 * @property string $repayment_overdue_amount
 * @property integer $overdue_count
 * @property string $overdue_amount
 * @property string $overdue_penalty
 * @property integer $urge_count
 * @property integer $urge_loan_count
 * @property integer $urge_success_count
 * @property integer $bad_count
 * @property string $bad_amount
 */
class Statistics extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'statistics';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at'], 'safe'],
            [['user_count', 'loan_count', 'loan_user', 'lend_count', 'lend_user', 'repayment_count', 'repayment_overdue_count', 'overdue_count', 'urge_count', 'urge_loan_count', 'urge_success_count', 'bad_count'], 'integer'],
            [['loan_interest', 'lend_amount', 'repayment_amount', 'repayment_overdue_amount', 'overdue_amount', 'overdue_penalty', 'bad_amount'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
            'user_count' => 'User Count',
            'loan_count' => 'Loan Count',
            'loan_interest' => 'Loan Interest',
            'loan_user' => 'Loan User',
            'lend_count' => 'Lend Count',
            'lend_amount' => 'Lend Amount',
            'lend_user' => 'Lend User',
            'repayment_amount' => 'Repayment Amount',
            'repayment_count' => 'Repayment Count',
            'repayment_overdue_count' => 'Repayment Overdue Count',
            'repayment_overdue_amount' => 'Repayment Overdue Amount',
            'overdue_count' => 'Overdue Count',
            'overdue_amount' => 'Overdue Amount',
            'overdue_penalty' => 'Overdue Penalty',
            'urge_count' => 'Urge Count',
            'urge_loan_count' => 'Urge Loan Count',
            'urge_success_count' => 'Urge Success Count',
            'bad_count' => 'Bad Count',
            'bad_amount' => 'Bad Amount',
        ];
    }
}
