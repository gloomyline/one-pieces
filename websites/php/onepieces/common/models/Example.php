<?php

namespace common\models;

use common\bases\BaseModel;
use Yii;

/**
 * This is the model class for table "example".
 *
 * @property string $id
 * @property string $name
 * @property string $description
 * @property string $link
 * @property string $image
 * @property integer $sort
 * @property integer $nav_id
 * @property integer $state
 * @property string $created_at
 * @property string $updated_at
 */
class Example extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'example';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sort', 'nav_id', 'state'], 'integer'],
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
            'nav_id' => 'Nav ID',
            'state' => 'State',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
