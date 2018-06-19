<?php
namespace common\models;

use common\bases\CommonModel;
use Yii;

class UserModel extends CommonModel
{

    const ENABLE = 1; // 启用状态
    const DISABLE = 2; // 禁用状态
    const HAS_IDENTITY_AUTH = 1; // 身份认证已认证
    const HAS_PROFILE_AUTH = 1; // 个人信息认证已认证
    const HAS_BANKCARD_AUTH = 1; // 银行卡认证已认证
    const HAS_PHONE_AUTH = 1; // 手机认证已认证
    const HAS_JD_AUTH = 1; // 京东已认证
    const HAS_TAOBAO_AUTH = 1; // 淘宝已认证
    const HAS_EDU_AUTH = 1; // 学历已认证
    const HAS_BILL_AUTH = 1; // 信用卡账单已认证
    const HAS_EBANK_AUTH = 1; // 网银流水已认证

    const HAS_NO_IDENTITY_AUTH = 0; // 身份认证未认证
    const HAS_NO_PROFILE_AUTH = 0; // 个人信息认证未认证
    const HAS_NO_BANKCARD_AUTH = 0; // 银行卡认证未认证
    const HAS_NO_PHONE_AUTH = 0; // 手机认证未认证
    const HAS_NO_JD_AUTH = 0; // 京东未认证
    const HAS_NO_TAOBAO_AUTH = 0; // 淘宝未认证
    const HAS_NO_EDU_AUTH = 0; // 学历未认证
    const HAS_NO_BILL_AUTH = 0; // 信用卡账单未认证
    const HAS_NO_EBANK_AUTH = 0; // 网银流水未认证

    const HAS_PHONE_AUTH_SUBMIT = 2; // 手机认证已提交

    const STATE_REGIST = 1; // 注册未申请
    const STATE_NORMAL = 2; //正常
    const STATE_OVERDUE = 3; // 逾期用户
    const STATE_BLCAK = 4; // 黑名单

    const GENDER_UNKNOWN = 0; // 未知性别
    const GENDER_MALE = 1; // 男
    const GENDER_FEMALE = 2; // 女

    /**
     * 添加用户
     *
     * @param string $mobile 手机号
     * @param string $password 密码
     * @param string $userIp IP地址
     * @return object|boolean
     */
    public static function addUser($mobile, $password, $userIp = null)
    {
        $model = new User();
        $model->mobile = $mobile;
        $model->password = $password ? md5($password) : '';
        $model->created_at = date('Y-m-d H:i:s');
        $model->updated_at = date('Y-m-d H:i:s');
        $model->login_time = date('Y-m-d H:i:s');
        $model->login_ip = $userIp;
        if ($model->save()) {
            return $model;
        } else {
            return false;
        }
    }

    /**
     * 根据手机号获取用户
     *
     * @param string $mobile 手机号
     * @return object|false
     */
    public static function getUserByMobile($mobile)
    {
        $user = User::find()->where(['mobile' => $mobile])->one();
        if ($user) {
            return $user;
        } else {
            return false;
        }
    }



    /**
     * 验证密码
     *
     * @param object $user user 用户对象
     * @param string $password 密码
     * @return boolean
     */
    public static function validatePassword($user, $password)
    {
        if ($user->password != md5($password)) {
            return false;
        }
        return true;
    }

    /**
     * 设置用户的AuthKey
     *
     * @return object|false
     */
    public static function setAuthKey()
    {
        $user = \Yii::$app->user->identity;
        if ($user and $user->auth_key == '') {
            $user->auth_key = \Yii::$app->security->generateRandomString();
            return $user->save() ? $user : false;
        } else {
            return false;
        }
    }

    /**
     * 获取用列表
     * @param integer $offset 查询的基准数
     * @param integer $limit 查询的记录数
     * @param string $realName 真实姓名
     * @param string $mobile 手机号码
     * @param integer $state 状态
     * @param integer $beginAt 申请开始时间
     * @param integer $endAt 申请截止时间
     * @param integer|string $quota 额度是否有效
     * @return array 返回查询的结果集/记录条数
     */
    public static function getUserList($offset, $limit, $realName, $mobile, $state, $beginAt, $endAt, $quota = '')
    {
        $result = $data = [];
        $userModel = User::find();
        if ($state) {
            $userModel->andWhere(['state' => intval($state)]);
        }
        if ($mobile) {
            $userModel->andWhere(['mobile' => trim($mobile)]);
        }
        if ($realName) {
            $userModel->andWhere(['real_name' => trim($realName)]);
        }
        if ($beginAt != '') {
            $beginAt = date('Y-m-d H:i:s', (int)($beginAt)); // 时间戳转字符串
            $userModel->andWhere(['>=', 'created_at', $beginAt]); // 注册起始时间
        }
        if ($endAt != '') {
            $endAt = date('Y-m-d H:i:s', (int)($endAt) + 3600 * 24 - 1); // 时间戳转字符串, 截止时间设置为当前日期的 23:59:59，如 2017-09-20 23:59:59
            $userModel->andWhere(['<=', 'created_at', $endAt]); // 注册截止时间
        }
        if ($quota != '') {
            $userModel->andWhere(['>', 'total_quota', 0]);
        }
        $data['count'] = $userModel->count();
        $data['result'] = $userModel->offset($offset)->limit($limit)->orderBy(['id' => SORT_DESC])->all();
        return $data;
    }

    /**
     * 获取认证状态
     * @param integer $userId 用户ID
     * @return object
     */
    public static function getAuthState($userId)
    {
       return User::find()->select('is_identity_auth, is_profile_auth, is_bankcard_auth, is_phone_auth, is_jd_auth, is_taobao_auth, is_edu_auth, is_bill_auth, is_ebank_auth')
                          ->where(['id' => $userId])
                          ->one();
    }

    /**
     * 获取身份认证列表数据
     * @param integer $offset 查询的基准数
     * @param integer $limit 查询的数目
     * @param string $realName 真实名称
     * @param string $userName 用户名-手机号
     * @param string $identityNo 身份证号码
     * @param integer $state 身份认证状态
     * @return array 返回身份认证信息数组/记录总条数
     */
    public static function getUserIdentityCardList($offset, $limit, $realName, $userName, $identityNo, $state)
    {
        $data = [];
        $user = User::find()->joinWith('userIdentityCard');
        if ($realName) {
            $user->andWhere(['user.real_name' => trim($realName)]); // 真实姓名
        }
        if ($userName) {
            $user->andWhere(['user.mobile' => trim($userName)]); // 手机号-用户名
        }
        if ($identityNo) {
            $user->andWhere(['user_identity_card.identity_no' => trim($identityNo)]); // 身份证号
        }
        if ($state != '') {
            $user->andWhere(['user.is_identity_auth' => intval($state)]); // 审核状态
        }
        $data['count'] = $user->count();
        $data['result'] = $user->offset($offset)->limit($limit)->orderBy(['id' => SORT_ASC])->all();

        return $data;
    }

    /**
     * 获取用户认证中心列表数据
     * @param integer $offset 查询的基准数
     * @param integer $limit 查询的数目
     * @param string $realName 真实名称
     * @param string $userName 用户名-手机号
     * @return array 用户认证信息数据对象/记录总条数
     */
    public static function getAuthCenterList($offset, $limit, $realName, $userName)
    {
        $data = [];
        $user = User::find()->with(['quotaApply' => function ($query){ $query->where(['apply_type' => QuotaApplyModel::TYPE_USER_APPLY, 'state' => QuotaApplyModel::STATE_AUDIT_SUCCESS]);}]);
        if ($realName) {
            $user->andWhere(['user.real_name' => trim($realName)]); // 真实姓名
        }
        if ($userName) {
            $user->andWhere(['user.mobile' => trim($userName)]); // 手机号-用户名
        }
        $data['count'] = $user->count();
        $data['result'] = $user->offset($offset)->limit($limit)->orderBy(['id' => SORT_ASC])->all();
        return  $data;
    }


    /**
     * 获取个人信息列表
     * @param integer $offset 查询的基准数
     * @param integer $limit 查询的数目
     * @param string $realName 真实姓名
     * @param string $userName 用户名
     * @param integer $state 个人信息认证状态
     * @return array 返回个人认证信息数据对象/数据总条数
     */
    public static function getProfileAuthList($offset, $limit, $realName, $userName, $state)
    {
        $data = [];
        $user = User::find()->joinWith('userBasic');
        if ($realName) {
            $user->andWhere(['user.real_name' => trim($realName)]); // 真实姓名
        }
        if ($userName) {
            $user->andWhere(['user.mobile' => trim($userName)]); // 手机号-用户名
        }
        if ($state != '') {
            $user->andWhere(['user.is_profile_auth' => intval($state)]); // 个人信息审核状态
        }
        $data['count'] = $user->count(); // 记录总条数
        $data['result'] = $user->offset($offset)->limit($limit)->orderBy(['id' => SORT_ASC])->all();
        return $data;
    }


    /**
     * 修改密码
     * @param integer $userId 用户ID
     * @param string $password 新密码
     * @return boolean true 修改成功， false 修改失败
     */
    public function updatePassword($userId, $password)
    {
        $user = User::findOne(['id' => $userId]);

        if (!$user) {
            return false;
        }
        $user->password = $password ? md5($password) : ''; // 密码
        $user->updated_at = date('Y-m-d H:i:s'); // 更新时间
        if ($user->save()) { // 保存新密码
            return true;
        } else {
            return false;
        }
    }

    /**
     * 根据手机号修改用户密码
     * 
     * @param string $mobile 用户手机号
     * @param string $newPassword 新密码
     * @return object|false
     */
    public static function updatePasswordByMobile($mobile, $newPassword)
    {
        $user = self::getUserByMobile($mobile);
        if ($user) {
            $user->password = md5($newPassword);
            return $user->save() ? $user : false;
        } else {
            return false;
        }
    }


    /**
     * 更新用户的用户真实姓名和身份认证状态
     * @param integer $userId 用户id
     * @param string $realName 用户真实姓名
     */
    public static function updateUserIdentity($userId, $realName)
    {
        $user = User::findOne(['id' => $userId]);
        if (empty($user)) {
            return;
        }
        $user->real_name = $realName;
        $user->is_identity_auth = UserModel::HAS_IDENTITY_AUTH; // 身份认证通过
        $user->save();
    }

    /**统计今日注册的用户数
     * @param string $date 日期
     * @return int|string返回查询记录条数
     */
    public static function statRegisterUserCountByDate($date = '')
    {
        return  User::find()
            ->where(["DATE_FORMAT(created_at, '%Y-%m-%d')" => $date])
            ->count();
    }

    /**
     * 统计累计注册用户数
     * @return int|string 返回所有用户总数
     */
    public static function RegisterUserCount()
    {
        return User::find()->count();
    }

    /**
     * 更新用户银行卡认证为已认证
     * @param integer $userId 用户id
     */
    public static function setUserBankCardAuth($userId)
    {
        $user = User::findOne(['id' => $userId]);
        if (empty($user)) {
            return;
        }
        $user->is_bankcard_auth = UserModel::HAS_BANKCARD_AUTH; // 银行卡认证通过
        $user->save();
    }

    /**
     * 放款后 更新相关信息
     * @params integer $userId 用户ID
     * @params integer $quota 交易金额
     * @return boolean 是否更新成功 true-是 false-否
     */
    public static function updateAfterGrantSuccess($userId, $quota)
    {
        $user = User::findOne(['id' => $userId]);
        $user->success_count += 1; // 成功借款次数
        $user->success_amount += $quota; // 成功借款金额
        $user->updated_at = date('Y-m-d H:i:s'); // 更新时间
        if (!$user->save()) {
            return false;
        }
        return true;
    }

    /**
     * 冻结用户额度
     * @params integer $userId 用户ID
     * @params integer $quota 金额
     * @return boolean 是否更新成功 true-是 false-否
     */
    public static function frozenUserQuota($userId, $quota)
    {
        $user = User::findOne(['id' => $userId]);
        $user->fronzen_quota = $quota; // 冻结额度
        $user->available_quota = $user->total_quota - $user->fronzen_quota; // 可用额度 = 总额度 - 冻结额度
        $user->updated_at = date('Y-m-d H:i:s'); // 更新时间
        if (!$user->save()) {
            return false;
        }
        return true;
        
    }

    /**
     * 解冻用户额度
     * @params integer $userId 用户ID
     * @params integer $quota 金额
     * @params boolean $needUpdateRepayCount 是否需要更新成功还款次数
     * @return boolean 是否更新成功 true-是 false-否
     */
    public static function thawUserQuota($userId, $quota, $needUpdateRepayCount = false)
    {
        $user = User::findOne(['id' => $userId]);
        $user->fronzen_quota = $user->fronzen_quota - $quota; // 冻结额度
        $user->available_quota = $user->available_quota + $quota; // 可用额度 = 原来可用额度 + 还款额度
        if ($needUpdateRepayCount) {
            $user->success_repay_count += 1;
            $user->success_repay_amount += $quota;
        }
        $user->updated_at = date('Y-m-d H:i:s'); // 更新时间
        if (!$user->save()) {
            return false;
        }
        return true;
    }

    /**
     * 根据用户id获取用户的详细信息
     * @param integer $id 用户id
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function getUserDetailByUserId($id)
    {
        $condition = UserBankModel::DEFAULT_BANKCARD;
        return User::find()
            ->where(['id' => $id])
            ->with('userIdentityCard', 'userBasic', 'userAdditional')
            ->with(['userBank' => function ($query) use($condition) { $query->where(['is_default' => $condition]);}])
            ->one();
    }

    /*
     * 设置用户状态
     * @param integer $userId 用户ID
     * @param string $state 用户状态
     * @return boolean 用户状态是否设置成功 true-是 false-否
     */
    public static function setUserState($userId, $state)
    {
        $user = User::findOne(['id' => $userId]);
        $user->state = $state; // 设置用户状态
        $user->updated_at = date('Y-m-d H:i:s');
        if (!$user->save()) {
            return false;
        }
        return true;
    }

    /**
     * 按ID查询用户信息
     * @param integer $userId 用户ID
     * @return object 返回查询的结果
     */
    public static function findUserById($userId)
    {
        return User::findOne(['id' => $userId]);
    }

    /**
     * 额度审核成功后跟新用户的总额度和可用额度
     * @param integer $userId 用户id
     * @param integer $allowQuota 通过金额+、-
     * @return bool 更新成功返回true，失败返回false
     */
    public static function updateUserQuota($userId, $allowQuota)
    {
        $user = User::findOne(['id' => $userId]);
        $user->total_quota += $allowQuota;
        $user->available_quota += $allowQuota;
        if ($user->available_quota < 0 || $user->total_quota < 0) {
            return false ;
        }
        if (!$user->save()) {
            return false;
        }
        return true;
    }

    /**
     * 获取额度提升列表内容
     * @param $offset
     * @param $limit
     * @param $realName 真实姓名
     * @param $mobile 手机号码
     * @param array $orderBy 排序规则
     * @return array 返回数组【总记录数，满足条件的数据对象】
     */
    public static function getIncreaseQuotaList($offset, $limit, $realName, $mobile, $orderBy = ['id' => SORT_DESC])
    {
        // 学历认证，京东认证，淘宝认证，常用信用卡认证，微信，qq，网银流水，信用卡账单
        $model = User::find()->with('userBasic');
        if ($realName != '') {
            $model->where(['real_name' => $realName]);
        }
        if ($mobile !='') {
            $model->where(['mobile' => $mobile]);
        }
        return [
            'count' => $model->count(),
            'result' => $model->offset($offset)->limit($limit)->orderBy($orderBy)->all(),
        ];
    }
    /**
     * 更新用户手机认证状态
     * @param integer $userId 用户id
     * @param integer $state 手机认证状态
     */
    public static function setUserPhoneAuth($userId, $state)
    {
        $user = User::findOne(['id' => $userId]);
        if (empty($user)) {
            return;
        }
        $user->is_phone_auth = $state; // 手机认证状态
        $user->save();
    }
}
