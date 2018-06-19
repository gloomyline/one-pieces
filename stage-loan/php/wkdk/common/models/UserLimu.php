<?php
namespace common\models;

use Yii;

/**
 * This is the model class for table "user_limu".
 *
 * @property integer $id ID
 * @property integer $user_id 用户ID
 * @property string $mobile 手机号码
 * @property string $token 立木token
 * @property string $content 获取的数据
 * @property integer $platform_type 平台类型：1-京东 2-淘宝
 * @property string $state 状态 pass：认证通过 nopass：认证不通过 busy：待认证
 * @property integer $has_report 是否已生成报告 0:未生成 1:已生成
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class UserLimu extends \yii\db\ActiveRecord
{
   public static function tableName()
   {
       return 'user_limu';
   }
    public function rules()
    {
        return [
            [['user_id', 'platform_type', 'has_report'], 'integer'],
            [['mobile'], 'string', 'max'=>11],
            [['token'], 'string', 'max'=>64],
            [['content'], 'string'],
            [['created_at', 'updated_at'], 'string'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'mobile' => 'Mobile',
            'token' => 'Token',
            'content' => 'Content',
            'platform_type' => 'Platform Type',
            'state' => 'State',
            'has_report' => 'Has Report',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}