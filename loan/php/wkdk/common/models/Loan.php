<?php

namespace common\models;

use backend\models\Admin;
use Yii;
use yii\db;
use common\models;

/**
 * This is the model class for table "loan".
 *
 * @property integer $id 借款ID
 * @property string $encoding 订单编号
 * @property integer $user_id 用户ID
 * @property integer $quota 申请额度
 * @property integer $period 申请期限
 * @property integer $repayment_way 还款方式 1-到期本息
 * @property string $check_at 初审时间
 * @property string $review_at 复审时间
 * @property string $lending_at 放款时间
 * @property string $repayment_at 实际还款时间
 * @property string $planned_repayment_at 计划还款时间
 * @property string $state 借款状态
 * @property integer $preliminary_officer 初审人员
 * @property string $preliminary_opinion 初审意见
 * @property integer $review_officer 复审人员
 * @property string $review_opinion 复审意见
 * @property double $arrival_amount 到账额度
 * @property double $repayed_amount 用户已还款金额
 * @property double $interest 借款息费
 * @property string $created_at 创建时间
 * @property double annualized_interest_rate 年化利率
 * @property double trial_rate 信审费率
 * @property double service_rate 服务费率
 * @property double overdue_rate 逾期费率
 * @property double poundage 手粗费率
 */
class Loan extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'loan';
    }

    public function rules()
    {
        return [
            [['user_id', 'quota', 'period', 'repayment_way', 'preliminary_officer', 'review_officer'], 'integer'],
            [['check_at', 'review_at', 'lending_at', 'planned_repayment_at', 'created_at'], 'string', 'max' => '19'],
            [['state'], 'string', 'max' => 20],
            [['encoding'], 'string', 'max' => 32],
            [['arrival_amount'], 'double'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'encoding' => 'Encoding',
            'user_id' => 'User ID',
            'quota' => 'Quota',
            'period' => 'Period',
            'repayment_way' => 'Repayment Way',
            'check_at' => 'Check At',
            'review_at' => 'Review At',
            'lending_at'  => 'Lending At',
            'repayment_at'  => 'Repayment At',
            'planned_repayment_at' => 'Planned Repayment At',
            'state' => 'State',
            'preliminary_officer' => 'Preliminary Officer',
            'preliminary_opinion' => 'Preliminary Opinion',
            'review_officer' => 'Review Officer',
            'review_opinion' => 'Review Officer',
            'arrival_amount' => 'Arrival Amount',
            'repayed_amount' => 'Repayed Amount',
            'interest' => 'Interest',
            'created_at' => 'Created At',
            'annualized_interest_rate' => 'Annualized Interest Rate',
            'trial_rate' => 'Trial Rate',
            'service_rate' => 'Service Rate',
            'overdue_rate' => 'Overdue Rate',
            'poundage' => 'Poundage',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getPreliminaryOfficer()
    {
        return $this->hasOne(Admin::className(), ['id' => 'preliminary_officer']);
    }

    public function getReviewOfficer()
    {
        return $this->hasOne(Admin::className(), ['id' => 'review_officer']);
    }

    public function getUserBasic(){
        return $this->hasOne(UserBasic::className(), ['user_id' => 'user_id']);
    }

    public function getUserBank(){
        return $this->hasOne(UserBank::className(), ['user_id' => 'user_id'])->where(['user_bank.is_default' => UserBankModel::DEFAULT_BANKCARD]);
    }

    public function getUserAdditional()
    {
        return $this->hasOne(UserAdditional::className(), ['user_id' => 'user_id']);

    }
    public function getUserIdentityCard()
    {
        return $this->hasOne(UserIdentityCard::className(), ['user_id' => 'user_id'])->where(['user_identity_card.state' => UserIdentityCardModel::STATE_PASS]);
    }

    /**
     * 关联逾期催收表
     * @return db\ActiveQuery
     */
    public function getUrge()
    {
        return $this->hasOne(Urge::className(), ['loan_id' => 'id']);
    }

    /**
     * 关联手机运营商报告 获取最新的手机有效报告信息
     * @return $this
     */
    public function getUserMobileReport()
    {
         return $this->hasOne(UserMobileReport::className(), ['user_id' => 'user_id'])->where(['state' => UserMobileReportModel::STATE_PASS, 'has_report' => UserMobileReportModel::HAS_REPORT])->orderBy(['id' => SORT_DESC]);
    }
}
