<?php
namespace common\models;

use Yii;

/**
 * This is the model class for table "user_mobile_report".
 *
 * @property integer $id 用户ID
 * @property string $mobile 手机号码
 * @property string $token 立木token
 * @property string $report 运营商报告内容
 * @property string $content 运营商报告原始数据内容
 * @property integer $state 状态 pass：认证通过 nopass：认证不通过 busy：待认证
 * @property integer $has_report 是否已生成报告 0:未生成 1:已生成
 * @property string $reg_time 入网时间
 * @property integer $idcard_match 身份证号与运营商数据是否匹配：3-未知 2-模糊匹配成功 1-匹配成功 0-匹配失败
 * @property integer $name_match 姓名与运营商数据是否匹配：3-未知 2-模糊匹配成功 1-匹配成功 0-匹配失败
 * @property integer $risk_list_cnt 命中风险清单次数
 * @property integer $overdue_loan_cnt 信贷逾期次数
 * @property integer $multi_lend_cnt 多头借贷次数
 * @property integer $risk_call_cnt 风险通话次数
 * @property string $fre_contact_area 最常联系人区域
 * @property integer $contact_num_cnt 联系人号码总数
 * @property integer $interflow_contact_cnt 互通号码数
 * @property double $avg_call_cnt 日均通话次数
 * @property double $avg_call_time 日均通话时长（m）
 * @property integer $silence_cnt 静默次数
 * @property integer $night_call_cnt 夜间通话次数
 * @property double $night_avg_call_time 夜间平均通话时长
 * @property double $avg_fee_month 月均消费
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class UserMobileReport extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'user_mobile_report';
    }

    public function rules()
    {
        return [
            [['id', 'user_id', 'has_report', 'idcard_match', 'name_match', 'risk_list_cnt', 'overdue_loan_cnt', 'multi_lend_cnt', 'risk_call_cnt', 'contact_num_cnt', 'interflow_contact_cnt', 'silence_cnt', 'night_call_cnt'], 'integer'],
            [['mobile'], 'string', 'max'=>11],
            [['state'], 'string', 'max'=>10],
            [['fre_contact_area'], 'string', 'max'=>20],
            [['created_at', 'updated_at', 'reg_time'], 'string', 'max'=>19],
            [['avg_call_cnt', 'avg_call_time', 'night_avg_call_time', 'avg_fee_month'], 'double'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'mobile' => 'Mobile',
            'report' => 'Report',
            'content' => 'Content',
            'state' => 'State',
            'has_report' => 'Has Report',
            'reg_time' => 'Reg Time',
            'idcard_match' => 'Idcard Match',
            'name_match' => 'Name Match',
            'risk_list_cnt' => 'Risk List Cnt',
            'overdue_loan_cnt' => 'Overdue Loan Cnt',
            'multi_lend_cnt' => 'Multi Lend Cnt',
            'risk_call_cnt' => 'Risk Call Cnt',
            'fre_contact_area' => 'Fre Contact Area',
            'contact_num_cnt' => 'Contact Num Cnt',
            'interflow_contact_cnt' => 'Interflow Contact Cnt',
            'avg_call_cnt' => 'Avg Call Cnt',
            'avg_call_time' => 'Avg Call Time',
            'silence_cnt' => 'Silence Cnt',
            'night_call_cnt' => 'Night Call Cnt',
            'night_avg_call_time' => 'Night Avg Call Time',
            'avg_fee_month' => 'Avg Fee Month',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * 关联user表
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}