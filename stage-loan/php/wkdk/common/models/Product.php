<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "product".
 *
 * @property integer $id 产品ID
 * @property string $title 产品名称
 * @property integer $type 产品分类
 * @property integer $max_quota 借款最大额度
 * @property integer $min_quota 借款最小额度
 * @property string $period 分期数
 * @property double $annualized_interest_rate 年化利率
 * @property integer $repayment_way 还款方式 1-等本等息
 * @property integer $active 上线状态 0:关闭 1:开启
 * @property double $trial_rate 信审费率
 * @property double $service_rate 服务费率
 * @property double $overdue_rate 逾期费率
 * @property double $poundage 手续费率
 * @property string $prepayment_poundage 提前还款手续费率
 * @property integer $prepayment_poundage_max 提前还款手续费上限
 * @property double $limit_overdue_days 逾期最大天数限制
 * @property string $use 消费用途
 * @property integer $admin_id 管理员ID
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 */
class Product extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'product';
    }

    public function rules()
    {
        return [
            [['type', 'max_quota', 'min_quota', 'repayment_way', 'active', 'prepayment_poundage_max', 'limit_overdue_days', 'admin_id'], 'integer'],
            [['annualized_interest_rate', 'trial_rate', 'service_rate', 'overdue_rate', 'poundage', 'prepayment_poundage'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 10],
            [['period'], 'string', 'max' => 30],
            [['use'], 'string', 'max' => 150],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'type' => 'Type',
            'max_quota' => 'Max Quota',
            'min_quota' => 'Min Quota',
            'period' => 'Period',
            'annualized_interest_rate' => 'Annualized Interest Rate',
            'repayment_way' => 'Repayment Way',
            'active' => 'Active',
            'trial_rate' => 'Trial Rate',
            'service_rate' => 'Service Rate',
            'poundage' => 'Poundage',
            'overdue_rate' => 'Overdue Rate',
            'prepayment_poundage' => 'Prepayment Poundage',
            'prepayment_poundage_max' => 'Prepayment Poundage Max',
            'limit_overdue_days' => 'Limit Overdue Days',
            'use' => 'Use',
            'admin_id' => 'Admin ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
