<?php

namespace backend\models;

use Yii;
use yii\web\IdentityInterface;
use yii\base\InvalidParamException;
use yii\base\NotSupportedException;

/**
 * This is the model class for table "admin".
 *
 * @property integer $id
 * @property string $title
 * @property string $route
 * @property integer $parent_id
 * @property string $module
 * @property integer $is_show
 * @property integer $created_at
 * @property integer $updated_at
 */
class Menu extends \yii\db\ActiveRecord
{
    private $_routes = null;

    public static function tableName()
    {
        return 'menu';
    }

    public function rules()
    {
        return [
            [['id', 'is_show', 'parent_id'], 'integer'],
            [['title'], 'string', 'max' => 10],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'parent_id' => 'Parent ID',
            'route' => 'Route',
            'module' => 'module',
            'is_show' => 'Is Show',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
