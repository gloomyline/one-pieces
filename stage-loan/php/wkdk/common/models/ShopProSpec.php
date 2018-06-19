<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "shop_pro_spec".
 *
 * @property string $id
 * @property integer $product_id
 * @property string $spec
 * @property integer $stock
 * @property string $price
 * @property string $created_at
 * @property string $updated_at
 */
class ShopProSpec extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_pro_spec';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'stock'], 'integer'],
            [['price'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['spec'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'spec' => 'Spec',
            'stock' => 'Stock',
            'price' => 'Price',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
