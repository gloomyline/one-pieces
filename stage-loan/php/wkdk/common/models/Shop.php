<?php

namespace common\models;

use backend\models\Admin;
use Yii;

/**
 * This is the model class for table "shop".
 *
 * @property integer $id 商户ID
 * @property string $shop_name 商户名称
 * @property string $legal_person_name 企业法人姓名
 * @property string $legal_person_mobile 企业法人手机号码
 * @property string $legal_person_id_no 企业法人身份证号
 * @property string $legal_person_id_card_pic 企业法人身份证正反照片
 * @property integer $is_eq 1：法人与实际控制人一致 2：不一致
 * @property string $actual_controller_name 实际控制人姓名
 * @property string $actual_controller_mobile 实际控制人手机号码
 * @property string $actual_controller_id_no 实际控制人身份证号
 * @property string $actual_controller_id_card_pic 实际控制人身份证正反照片
 * @property string $corporate_contacts_email 企业联系人邮箱
 * @property string $corporate_contacts_mobile 企业联系人手机号
 * @property string $bank_name 银行名称
 * @property string $bank_no 打款银行卡号
 * @property string $shop_addr 企业详细地址
 * @property string $three_cards_pic 企业三证图片
 * @property double $registered_capital 注册资金(万)
 * @property string $registered_at 注册时间
 * @property double $office_area 办公面积(平方米)
 * @property integer $staff_no 职工人数
 * @property string $corporate_office_pic 企业办公场所照片及房产租赁协议
 * @property string $salesman_logo_pic 业务员与公司logo合照
 * @property string $qualification_pic 相关行业资质照
 * @property string $holder_no_remark 持证人数以及备注
 * @property string $protocol_pic 商户合作协议
 * @property string $authorization_pic 征信授权协议书
 * @property string $commitment_pic 商户承诺书
 * @property string $bank_bill_pic 近六个月网银流水
 * @property string $period 商户期数
 * @property integer $city_id 商户所在城市id
 * @property integer $shop_category 商户可分期类别
 * @property integer $product_category 商品可分期类别
 * @property integer $salesman_id 业务经理ID
 * @property integer $auditor_id 审核人员ID
 * @property integer $state 是否显示0：待审核 1：审核通过 2：审核不通过
 * @property string $audit_updated_at 审核状态更新时间
 * @property string $shop_no 商户号
 * @property integer $total_quota 总额度
 * @property string $init_total_quota 初始总额度
 * @property double $available_quota 可用额度
 * @property integer $single_limit_quota 单笔限额
 * @property integer $daily_limit_quota 每日限额
 * @property double $daily_available_quota 当日剩余可用额度
 * @property string $opinion 风控意见
 * @property string $logo
 * @property string $shop_pic
 * @property string $intro
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class Shop extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_eq', 'staff_no', 'city_id', 'salesman_id', 'auditor_id', 'state'], 'integer'],
            [['registered_capital', 'office_area', 'total_quota', 'init_total_quota', 'daily_available_quota', 'available_quota', 'single_limit_quota', 'daily_limit_quota'], 'number'],
            [['audit_updated_at', 'created_at', 'updated_at'], 'safe'],
            [['shop_name'], 'string', 'max' => 20],
            [['legal_person_name', 'actual_controller_name', 'shop_no'], 'string', 'max' => 10],
            [['legal_person_mobile', 'actual_controller_mobile', 'corporate_contacts_mobile'], 'string', 'max' => 11],
            [['legal_person_id_no', 'actual_controller_id_no', 'corporate_contacts_email', 'shop_category', 'period'], 'string', 'max' => 30],
            [['legal_person_id_card_pic', 'actual_controller_id_card_pic', 'holder_no_remark', 'opinion', 'intro'], 'string', 'max' => 200],
            [['bank_name'], 'string', 'max' => 60],
            [['product_category'], 'string', 'max' => 40],
            [['bank_no'], 'string', 'max' => 32],
            [['shop_addr'], 'string', 'max' => 50],
            [['three_cards_pic', 'qualification_pic'], 'string', 'max' => 300],
            [['corporate_office_pic'], 'string', 'max' => 800],
            [['salesman_logo_pic', 'protocol_pic', 'authorization_pic', 'commitment_pic', 'logo'], 'string', 'max' => 100],
            [['bank_bill_pic'], 'string', 'max' => 5000],
            [['shop_pic'], 'string', 'max' => 500],
            [['registered_at'], 'string', 'max' => 8],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'shop_name' => 'Shop Name',
            'legal_person_name' => 'Legal Person Name',
            'legal_person_mobile' => 'Legal Person Mobile',
            'legal_person_id_no' => 'Legal Person Id No',
            'legal_person_id_card_pic' => 'Legal Person Id Card Pic',
            'is_eq' => 'Is Eq',
            'actual_controller_name' => 'Actual Controller Name',
            'actual_controller_mobile' => 'Actual Controller Mobile',
            'actual_controller_id_no' => 'Actual Controller Id No',
            'actual_controller_id_card_pic' => 'Actual Controller Id Card Pic',
            'corporate_contacts_email' => 'Corporate Contacts Email',
            'corporate_contacts_mobile' => 'Corporate Contacts Mobile',
            'bank_name' => 'Bank Name',
            'bank_no' => 'Bank No',
            'shop_addr' => 'Shop Addr',
            'three_cards_pic' => 'Three Cards Pic',
            'registered_capital' => 'Registered Capital',
            'registered_at' => 'Registered At',
            'office_area' => 'Office Area',
            'staff_no' => 'Staff No',
            'corporate_office_pic' => 'Corporate Office Pic',
            'salesman_logo_pic' => 'Salesman Logo Pic',
            'qualification_pic' => 'Qualification Pic',
            'holder_no_remark' => 'Holder No Remark',
            'protocol_pic' => 'Protocol Pic',
            'authorization_pic' => 'Authorization Pic',
            'commitment_pic' => 'Commitment Pic',
            'bank_bill_pic' => 'Bank Bill Pic',
            'period' => 'Period',
            'city_id' => 'City ID',
            'shop_category' => 'Shop ID',
            'product_category' => 'Product ID',
            'salesman_id' => 'Salesman ID',
            'auditor_id' => 'Auditor ID',
            'state' => 'State',
            'audit_updated_at' => 'Audit Updated At',
            'shop_no' => 'Shop No',
            'total_quota' => 'Total Quota',
            'init_total_quota' => 'Init Total Quota',
            'daily_available_quota' => 'Daily Available Quota',
            'available_quota' => 'Available Quota',
            'single_limit_quota' => 'Single Limit Quota',
            'daily_limit_quota' => 'Daily Limit Quota',
            'opinion' => 'Opinion',
            'logo' => 'Logo',
            'shop_pic' => 'Shop Pic',
            'intro' => 'Intro',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * 获取业务经理信息
     * @return \yii\db\ActiveQuery
     */
    public function getSalesman()
    {
        return $this->hasOne(Admin::className(), ['id' => 'salesman_id']);
    }

    /**
     * 获取审核人信息
     * @return \yii\db\ActiveQuery
     */
    public function getAuditor()
    {
        return $this->hasOne(Admin::className(), ['id' => 'auditor_id']);
    }
}