<?php

namespace common\models;

use Yii;
use yii\web\IdentityInterface;
use common\models\UserBank;
use common\models\UserIdentityCard;
use common\models\UserBasic;
use common\models\UserAdditional;

/**
 * This is the model class for table "user".
 *
 * @property integer $id 用户ID
 * @property string $mobile 手机号码
 * @property string $password 密码
 * @property string $real_name 真实姓名
 * @property integer $gender 性别
 * @property integer $age 年龄
 * @property string $education 学历
 * @property string $zm_open_id 芝麻open_id
 * @property string $login_ip  登录IP
 * @property string $login_time 登入时间
 * @property string $auth_key cookie auth_key
 * @property integer $success_count 成功借款次数
 * @property double success_amount 成功借款金额
 * @property integer success_repay_count 成功还款次数
 * @property double success_repay_amount 成功借款金额
 * @property integer $fronzen_quota 冻结额度
 * @property integer $available_quota 可用额度
 * @property integer $total_quota 总额度
 * @property integer $state  用户状态 1:注册未申请 2:正常 3:逾期用户 4:黑名单
 * @property integer $is_forbidden 1:启用,2:禁用
 * @property integer $is_identity_auth 身份认证 0:未填写/未认证 1：已认证
 * @property integer $is_profile_auth 个人信息认证 0:未填写/未认证 1：已认证
 * @property integer $is_bankcard_auth 银行卡认证 0:未填写/未认证 1：已认证
 * @property integer $is_phone_auth 手机认证 0:未填写/未认证 1：已认证
 * @property integer $is_jd_auth 京东认证 0:未填写/未认证 1：已认证
 * @property integer $is_taobao_auth 淘宝认证 0:未填写/未认证 1：已认证
 * @property integer $is_edu_auth 学历认证 0:未填写/未认证 1：已认证
 * @property integer $is_bill_auth 信用卡账单认证 0:未填写/未认证 1：已认证
 * @property integer $is_ebank_auth 网银流水认证 0:未填写/未认证 1：已认证
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{

    public static function tableName()
    {
        return 'user';
    }

    public function rules()
    {
        return [
            [['state', 'success_count', 'fronzen_quota', 'available_quota', 'total_quota', 'gender', 'age'], 'integer'],
            [['mobile'], 'string', 'max' => 11],
            [['password'], 'string', 'max' => 64],
            [['login_ip'], 'string', 'max' => 30],
            [['login_time', 'created_at', 'updated_at'], 'string', 'max' => 19],
            [['auth_key'], 'string', 'max' => 32],
            [['real_name', 'education'], 'string', 'max' => 10],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mobile' => 'Mobile',
            'password' => 'Password',
            'real_name' => 'Real Name',
            'gender' => 'Gender',
            'age' => 'Age',
            'education' => 'Education',
            'zm_open_id' => 'Zm Open ID',
            'login_ip' => 'Login Ip',
            'login_time' => 'Login Time',
            'auth_key'  => 'Auth Key',
            'success_count' => 'Success Count',
            'success_amount' => 'Success Amount',
            'success_repay_count' => 'Success Repay Count',
            'success_repay_amount' => 'Success Repay Amount',
            'fronzen_quota' => 'Frozen Quota',
            'available_quota' => 'Available Quota',
            'total_quota' => 'Total Quota',
            'state' => 'State',
            'is_forbidden' => 'Is Forbidden',
            'is_identity_auth' => 'Is Identity Auth',
            'is_profile_auth' => 'Is Profile Auth',
            'is_bankcard_auth' => 'Is Bankcard Auth',
            'is_phone_auth' => 'Is Phone Auth',
            'is_jd_auth' => 'Is JD Auth',
            'is_taobao_auth' => 'Is Taobao Auth',
            'is_edu_auth' => 'Is Edu Auth',
            'is_bill_auth' => 'Is Bill Auth',
            'is_ebank_auth' => 'Is Ebank Auth',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public static function findIdentity($userid)
    {
        return static::findOne(['id' => $userid]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        $userToken = UserTokenModel::validateAccessToken($token);
        if (!$userToken) {
            return null;
        }
        $userid = $userToken->userid;
        return self::findIdentity($userid);
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * 关联用户银行信息表
     * @return \yii\db\ActiveQuery
     */
    public function getUserBank()
    {
        return $this->hasOne(UserBank::className(), ['user_id' => 'id']);
    }

    /**
     * 关联用户银行卡信息表获得默认银行卡记录
     * @return \yii\db\ActiveQuery
     */
    public function getUserBankList()
    {
        return $this->hasMany(UserBank::className(), ['user_id' => 'id']);
    }

    /**
     * 关联基本信息表
     * @return \yii\db\ActiveQuery
     */
    public function getUserBasic()
    {
        return $this->hasOne(UserBasic::className(), ['user_id' => 'id']);
    }

    /**
     * 关联用户身份证信息表
     * @return \yii\db\ActiveQuery
     */
    public function getUserIdentityCard()
    {
        return $this->hasOne(UserIdentityCard::className(), ['user_id' => 'id']);
    }

    /**
     * 关联用户其他信息表
     * @return \yii\db\ActiveQuery
     */
    public function getUserAdditional()
    {
        return $this->hasOne(UserAdditional::className(), ['user_id' => 'id']);
    }

    /**
     * 关联用户手机认证列表
     * @return \yii\db\ActiveQuery
     */
    public function getUserMobile()
    {
        return $this->hasOne(UserMobileReport::className(), ['user_id' => 'id']);
    }

    /**
     * 关联额度申请列表
     * @return \yii\db\ActiveQuery
     */
    public function getQuotaApply()
    {
        return $this->hasOne(QuotaApply::className(), ['user_id' => 'id']);
    }
}
