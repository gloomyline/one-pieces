<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "order_detail".
 *
 * @property integer $id
 * @property integer $loan_id
 * @property integer $spec_id
 * @property string $total
 * @property integer $shop_product_id
 * @property string $title
 * @property string $spec
 * @property integer $quantity
 * @property string $price
 * @property string $created_at
 * @property string $updated_at
 */
class OrderDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['loan_id', 'spec_id', 'shop_product_id', 'quantity'], 'integer'],
            [['total', 'price'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['title', 'spec'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'loan_id' => 'Loan ID',
            'spec_id' => 'Spec ID',
            'total' => 'Total',
            'shop_product_id' => 'Shop Product ID',
            'title' => 'Title',
            'spec' => 'Spec',
            'quantity' => 'Quantity',
            'price' => 'Price',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'shop_product_id']);
    }
}
