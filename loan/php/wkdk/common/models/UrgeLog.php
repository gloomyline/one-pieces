<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "urge_log".
 *
 * @property integer $id ID
 * @property integer $urge_id 催收记录ID
 * @property integer $urge_way 催款方式1:短信 2:电话 3:上门 4:第三方
 * @property integer $urge_result 催款结果 1:客户承诺还款 2:客户无法还款 3:催款成功 4:客户失联 5:坏账
 * @property string $planned_repayment_at 预计还款时间
 * @property string $content 催款情况说明
 * @property integer $admin_id 催收员id
 * @property string $created_at 创建时间
 */
class UrgeLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'urge_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['urge_id', 'urge_way', 'urge_result', 'admin_id'], 'integer'],
            [['urge_way', 'urge_result'], 'required'],
            [['planned_repayment_at', 'created_at'], 'safe'],
            [['content'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'urge_id' => 'Urge ID',
            'urge_way' => 'Urge Way',
            'urge_result' => 'Urge Result',
            'planned_repayment_at' => 'Planned Repayment At',
            'content' => 'Content',
            'admin_id' => 'Admin Id',
            'created_at' => 'Created At',
        ];
    }
}
