<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "anti_fraud".
 *
 * @property integer $id ID
 * @property string $user_id 用户ID
 * @property string $content 反欺诈内容
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class AntiFraud extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'anti_fraud';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['content'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['content'], 'string', 'max' => 2000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'USER ID',
            'content' => 'Content',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
