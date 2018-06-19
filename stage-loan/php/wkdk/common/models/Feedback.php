<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "feedback".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $title
 * @property string $content
 * @property integer $state
 * @property string $created_at
 */
class Feedback extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'feedback';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'content', 'user_id'], 'required'],
            [['user_id', 'state'], 'integer'],
            [['type'], 'string', 'max' => 10],
            [['content'], 'string', 'max' => 1000],
            [['created_at'], 'string', 'max' => 19],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'type' => '类型',
            'content' => '内容',
            'state' => '状态',
            'created_at' => '创建时间',
        ];
    }

    /**
     * 关联的User对象
     * @return Object [[User]]
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
