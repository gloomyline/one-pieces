<?php
namespace common\models;

use backend\models\Admin;

/**
 * This is the model class for table "quota_apply".
 *
 * @property integer $id 申请ID
 * @property integer $user_id 用户ID
 * @property integer $admin_id 审核员ID
 * @property integer $apply_quota 申请额度
 * @property integer $allow_quota 通过额度
 * @property integer $available_quota 可用额度
 * @property integer $total_quota 总额度
 * @property integer $state 申请通过状态：0-待审核 1-审核通过 2-审核失败
 * @property integer $apply_type 额度类型：0-后台添加 1-用户申请
 * @property string $memo 备注
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class QuotaApply extends \yii\db\activeRecord
{
    public static function tableName()
    {
        return 'quota_apply';
    }

    public function rules()
    {
        return [
            [['user_id', 'admin_id', 'apply_quota', 'allow_quota', 'state', 'apply_type'], 'integer'],
            [['available_quota', 'total_quota'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['memo'], 'string', 'max' => 300],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'admin_id' => 'Admin ID',
            'apply_quota' => 'Apply Quota',
            'allow_quota' => 'Allow Quota',
            'available_quota' => 'Available Quota',
            'total_quota' => 'Total Quota',
            'state' => 'State',
            'apply_type' => 'Apply Type',
            'memo' => 'Memo',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * 关联用户表
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * 关联管理员列表
     * @return \yii\db\ActiveQuery
     */
    public function getAdmin()
    {
        return $this->hasOne(Admin::className(), ['id' => 'admin_id']);
    }

}