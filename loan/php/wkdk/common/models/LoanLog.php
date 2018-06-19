<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "loan_log".
 *
 * @property integer $id 借款日志ID
 * @property integer $loan_id 借款ID
 * @property string $title 标题
 * @property string $content 内容
 * @property string $created_at 创建时间
 */
class LoanLog extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'loan_log';
    }

    public function rules()
    {
        return [
            [['loan_id'], 'integer'],
            [['title'], 'string', 'max' => 20],
            [['content'], 'string', 'max' => 200],
            [['created_at'], 'string', 'max' => 19],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'loan_id' => 'Loan ID',
            'title' => 'Title',
            'content' => 'Content',
            'created_at' => 'Created At',
        ];
    }

    
}
