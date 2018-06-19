<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "product".
 *
 * @property integer $id 产品ID
 * @property string $title 产品名称
 * @property integer $max_quota 借款最大额度
 * @property integer $min_quota 借款最小额度
 * @property integer $max_period 借款周期上限
 * @property integer $min_period 借款周期下限
 * @property double $annualized_interest_rate 年化利率
 * @property integer $repayment_way 还款方式 1-到期本息
 * @property integer $active 上线状态 0:下线 1:上线
 * @property double $trial_rate 信审费率
 * @property double $service_rate 服务费率
 * @property double $overdue_rate 逾期费率
 * @property double $poundage 手续费率
 * @property double $limit_overdue_days 逾期最大天数限制
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
            [['max_quota', 'min_quota', 'max_period', 'min_period', 'admin_id', 'repayment_way', 'active'], 'integer'],
            [['created_at', 'updated_at'], 'string', 'max' => '19'],
            [['title'], 'string', 'max' => 10],
            [['trial_rate', 'service_rate', 'poundage', 'overdue_rate', 'annualized_interest_rate'], 'double'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'max_quota' => 'Max Quota',
            'min_quota' => 'Min Quota',
            'max_period' => 'Max Period',
            'min_period' => 'Min Period',
            'annualized_interest_rate' => 'Annualized Interest Rate',
            'repayment_way' => 'Repayment Way',
            'active' => 'Active',
            'trial_rate' => 'Trial Rate',
            'service_rate' => 'Service Rate',
            'poundage' => 'Poundage',
            'overdue_rate' => 'Overdue Rate',
            'limit_overdue_days' => 'Limit Overdue Days',
            'admin_id' => 'Admin ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    
}
