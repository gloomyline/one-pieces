<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_basic".
 *
 * @property integer $id ID
 * @property integer $user_id 用户ID
 * @property integer $live_area 居住区域
 * @property integer $live_addr 详细地址
 * @property integer $live_time 居住时长
 * @property integer $is_work_auth 工作信息认证 0:未填写/未认证 1：已认证
 * @property integer $is_relation_auth 人际关系认证 0:未填写/未认证 1：已认证
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class UserBasic extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_basic';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'is_work_auth', 'is_relation_auth'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['live_area'], 'string', 'max' => 30],
            [['live_addr'], 'string', 'max' => 50],
            [['live_time'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'live_area' => 'Live Area',
            'live_addr' => 'Live Addr',
            'live_time' => 'Live Time',
            'is_work_auth' => 'Is Work Auth',
            'is_relation_auth' => 'Is Relation Auth',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
