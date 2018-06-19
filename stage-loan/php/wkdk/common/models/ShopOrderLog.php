<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "shop_order_log".
 *
 * @property string $id
 * @property integer $loan_id
 * @property integer $state
 * @property string $confirm_opinion
 * @property string $receiving_opinion
 * @property string $created_at
 * @property string $updated_at
 */
class ShopOrderLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_order_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['loan_id', 'state'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['confirm_opinion', 'receiving_opinion'], 'string', 'max' => 200],
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
            'state' => 'State',
            'confirm_opinion' => 'Confirm Opinion',
            'receiving_opinion' => 'Receiving Opinion',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
