<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "partner".
 *
 * @property string $id
 * @property string $name
 * @property string $description
 * @property string $link
 * @property string $image
 * @property integer $sort
 * @property integer $state
 * @property string $created_at
 * @property string $updated_at
 */
class Partner extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'partner';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sort', 'state'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 10],
            [['description'], 'string', 'max' => 50],
            [['link', 'image'], 'string', 'max' => 100],
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
            'link' => 'Link',
            'image' => 'Image',
            'sort' => 'Sort',
            'state' => 'State',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
