<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "shop_product".
 *
 * @property string $id
 * @property integer $shop_id
 * @property integer $category_id
 * @property string $title
 * @property string $no
 * @property string $pic
 * @property integer $sort
 * @property string $intro
 * @property string $spec
 * @property string $service
 * @property integer $on_sale
 * @property integer $sale
 * @property integer $total_stock
 * @property integer $state
 * @property string $opinion
 * @property integer $auditor_id
 * @property string $audited_at
 * @property string $created_at
 * @property string $updated_at
 */
class ShopProduct extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shop_id', 'category_id', 'sort', 'on_sale', 'sale', 'total_stock', 'state', 'auditor_id'], 'integer'],
            // [['intro', 'spec', 'service'], 'required'],
            [['intro', 'spec', 'service'], 'string'],
            [['audited_at', 'created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 50],
            [['no'], 'string', 'max' => 20],
            [['pic'], 'string', 'max' => 500],
            [['opinion'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'shop_id' => 'Shop ID',
            'category_id' => 'Category ID',
            'title' => 'Title',
            'no' => 'No',
            'pic' => 'Pic',
            'sort' => 'Sort',
            'intro' => 'Intro',
            'spec' => 'Spec',
            'service' => 'Service',
            'on_sale' => 'On Sale',
            'sale' => 'Sale',
            'total_stock' => 'Total Stock',
            'state' => 'State',
            'opinion' => 'Opinion',
            'auditor_id' => 'Auditor ID',
            'audited_at' => 'Audited At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getProSpec()
    {
        return $this->hasMany(ShopProSpec::className(), ['product_id' => 'id']);
    }
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }
}
