<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_additional".
 *
 * @property integer $id ID
 * @property integer $user_id 用户ID
 * @property string $industry 从事行业
 * @property string $position 工作岗位
 * @property string $company_name 单位名称
 * @property string $company_area 单位所在地
 * @property string $company_addr 单位详细地址
 * @property string $company_tel 单位电话
 * @property string $linkman_relation_fir 1号联系人与本人关系
 * @property string $linkman_name_fir 1号联系人姓名
 * @property string $linkman_tel_fir 1号联系人手机号码
 * @property string $linkman_relation_sec 2号联系人与本人关系
 * @property string $linkman_name_sec 2号联系人姓名
 * @property string $linkman_tel_sec 2号联系人手机号码
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 */
class UserAdditional extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_additional';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['industry', 'position'], 'string', 'max' => 20],
            [['company_name', 'company_area'], 'string', 'max' => 30],
            [['company_addr'], 'string', 'max' => 50],
            [['company_tel'], 'string', 'max' => 12],
            [['linkman_relation_fir', 'linkman_relation_sec'], 'string', 'max' => 4],
            [['linkman_name_fir', 'linkman_name_sec'], 'string', 'max' => 10],
            [['linkman_tel_fir', 'linkman_tel_sec'], 'string', 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'industry' => 'Industry',
            'position' => 'Position',
            'company_name' => 'Company Name',
            'company_area' => 'Company Area',
            'company_addr' => 'Company Addr',
            'company_tel' => 'Company Tel',
            'linkman_relation_fir' => 'Linkman Relation Fir',
            'linkman_name_fir' => 'Linkman Name Fir',
            'linkman_tel_fir' => 'Linkman Tel Fir',
            'linkman_relation_sec' => 'Linkman Relation Sec',
            'linkman_name_sec' => 'Linkman Name Sec',
            'linkman_tel_sec' => 'Linkman Tel Sec',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }
}
