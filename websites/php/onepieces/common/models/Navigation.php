<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "navigation".
 *
 * @property string $id
 * @property string $name
 * @property string $description
 * @property integer $pid
 * @property string $link
 * @property integer $type
 * @property integer $sort
 * @property integer $is_show
 * @property integer $is_open
 * @property integer $admin_id
 * @property string $created_at
 * @property string $updated_at
 */
class Navigation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'navigation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid', 'type', 'sort', 'is_show', 'is_open', 'admin_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 10],
            [['description', 'link'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'pid' => 'Pid',
            'link' => 'Link',
            'type' => 'Type',
            'sort' => 'Sort',
            'is_show' => 'Is Show',
            'is_open' => 'Is Open',
            'admin_id' => 'Admin ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
