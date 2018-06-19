<?php

namespace common\models;

use backend\models\Admin;
use Yii;

/**
 * This is the model class for table "shop_quota_apply".
 *
 * @property string $id
 * @property integer $shop_id
 * @property integer $admin_id
 * @property integer $apply_total
 * @property integer $apply_single_limit
 * @property integer $apply_daily_limit
 * @property integer $allow_total
 * @property integer $allow_single_limit
 * @property integer $allow_daily_limit
 * @property string $available_quota
 * @property string $total_quota
 * @property string $single_limit_quota
 * @property string $daily_limit_quota
 * @property integer $state
 * @property integer $state_total
 * @property integer $state_single_limit
 * @property integer $state_daily_limit
 * @property integer $apply_type
 * @property string $memo
 * @property string $created_at
 * @property string $updated_at
 */
class ShopQuotaApply extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_quota_apply';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shop_id', 'admin_id', 'apply_total', 'apply_single_limit', 'apply_daily_limit', 'allow_total', 'allow_single_limit', 'allow_daily_limit', 'state', 'state_total', 'state_single_limit', 'state_daily_limit', 'apply_type'], 'integer'],
            [['available_quota', 'total_quota', 'single_limit_quota', 'daily_limit_quota'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['memo'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'shop_id' => 'Shop ID',
            'admin_id' => 'Admin ID',
            'apply_total' => 'Apply Total',
            'apply_single_limit' => 'Apply Single Limit',
            'apply_daily_limit' => 'Apply Daily Limit',
            'allow_total' => 'Allow Total',
            'allow_single_limit' => 'Allow Single Limit',
            'allow_daily_limit' => 'Allow Daily Limit',
            'available_quota' => 'Available Quota',
            'total_quota' => 'Total Quota',
            'single_limit_quota' => 'Single Limit Quota',
            'daily_limit_quota' => 'Daily Limit Quota',
            'state' => 'State',
            'state_total' => 'State Total',
            'state_single_limit' => 'State Single Limit',
            'state_daily_limit' => 'State Daily Limit',
            'apply_type' => 'Apply Type',
            'memo' => 'Memo',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getShop()
    {
        return $this->hasOne(Shop::className(), ['id' => 'shop_id']);
    }
    public function getAdmin()
    {
        return $this->hasOne(Admin::className(), ['id' => 'admin_id']);
    }
}
