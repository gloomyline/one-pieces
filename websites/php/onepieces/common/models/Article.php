<?php

namespace common\models;

use common\bases\BaseModel;
use Yii;

/**
 * This is the model class for table "article".
 *
 * @property string $id
 * @property string $title
 * @property string $author
 * @property string $description
 * @property integer $nav_id
 * @property string $image
 * @property integer $sort
 * @property integer $state
 * @property integer $notice
 * @property string $content
 * @property string $created_at
 * @property string $updated_at
 */
class Article extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nav_id', 'sort', 'state', 'notice'], 'integer'],
            // [['content'], 'required'],
            [['content'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['title', 'description'], 'string', 'max' => 50],
            [['author'], 'string', 'max' => 10],
            [['image'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'author' => 'Author',
            'description' => 'Description',
            'nav_id' => 'Nav ID',
            'image' => 'Image',
            'sort' => 'Sort',
            'state' => 'State',
            'notice' => 'Notice',
            'content' => 'Content',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getNavigation()
    {
        return $this->hasOne(Navigation::className(), ['id' => 'nav_id']);
    }
}
