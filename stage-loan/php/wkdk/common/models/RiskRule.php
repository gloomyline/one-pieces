<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "risk_rule".
 *
 * @property string $id
 * @property string $item
 * @property string $name
 * @property string $module
 * @property string $pattern
 * @property string $operator
 * @property string $val
 * @property integer $outcome
 * @property string $symbol
 * @property integer $score
 * @property string $created_at
 * @property string $updated_at
 * @property integer $admin_id
 * @property integer $state
 */
class RiskRule extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'risk_rule';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['score', 'admin_id', 'outcome', 'state'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['item'], 'string', 'max' => 100],
            [['name'], 'string', 'max' => 20],
            [['module'], 'string', 'max' => 50],
            [['pattern', 'operator', 'val', 'symbol'], 'string', 'max' => 10],
            [['remarks'], 'string', 'max'=>200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'item' => 'Item',
            'name' => 'Name',
            'module' => 'Module',
            'pattern' => 'Pattern',
            'operator' => 'Operator',
            'val' => 'Val',
            'outcome' => 'Outcome',
            'symbol' => 'Symbol',
            'score' => 'Score',
            'state' => 'State',
            'remarks' => 'Remarks',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'admin_id' => 'Admin ID',
        ];
    }
}
