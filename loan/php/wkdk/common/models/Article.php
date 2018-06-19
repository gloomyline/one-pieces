<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "article".
 *
 * @property integer $id 文章ID
 * @property string $title 文章标题
 * @property string $type 分类 acitivity:活动中心 problem:常见问题
 * @property integer $state 状态：1显示 2不显示
 * @property string $image 图片
 * @property integer $sort 排序
 * @property string $content 内容
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class Article extends \yii\db\ActiveRecord
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
            [['state', 'sort'], 'integer'],
            [['content'], 'required'],
            [['content'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['type'], 'string', 'max' => 10],
            [['title'], 'string', 'max' => 50],
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
            'type' => 'Type',
            'title' => 'Title',
            'state' => 'State',
            'image' => 'Image',
            'sort' => 'Sort',
            'content' => 'Content',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
