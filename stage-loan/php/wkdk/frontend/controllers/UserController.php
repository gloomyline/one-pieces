<?php

namespace frontend\controllers;

use common\config\SmsConfig;
use common\models\UserAdditionalModel;
use common\models\UserBasicModel;
use common\models\UserIdentityCardModel;
use common\models\UserModel;
use common\models\MobileCodeModel;
use common\services\UserService;
use frontend\bases\FrontendController;
use yii\helpers\Json;
use Yii;

/**
 * 用户帐号相关控制器
 */
class UserController extends FrontendController
{ 
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        // 访问控制
        $behaviors['access']['rules'] = [
            // 允许访客用户访问的Action
            [
                'actions' => ['forget-password'],
                'allow' => true,
                'roles' => ['?'],
            ],
            // 其它的Action必须要授权用户才可访问
            [
                'allow' => true,
                'roles' => ['@'],
            ],
        ];
        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * 获取认证状态
     * @return string 返回查询记录
     */
    public function actionGetBaseAuth()
    {
        $user = new UserModel();
        $result = $user ->getAuthState(Yii::$app->user->getId());
        
        $data = [
            'is_identity_auth' => $result->is_identity_auth, // 身份认证
            'is_profile_auth' => $result->is_profile_auth, // 个人信息认证
            'is_bankcard_auth' => $result->is_bankcard_auth, // 银行卡认证
            'is_phone_auth' => $result->is_phone_auth, // 手机认证
        ];
        return Json::encode([
            "status" => self::STATUS_SUCCESS,
            "error_message" => '',
            "results" => [$data]
        ]);
    }

    /**
     * 获取个人基本信息
     * @return string 成功或失败的json信息
     */
    public function actionProfile()
    {
        $userBasicModel = new UserBasicModel();

        $result = $userBasicModel->getUserBasic(Yii::$app->user->getId()) ; // 查询用户基本信息
        $data[] = [
            'live_area' => isset($result->live_area) ? explode('-', $result->live_area) : [], // 居住区域
            'live_addr' => $result->live_addr ?? '', // 详细地址
            'live_time' => isset($result->live_time) ? explode('-', $result->live_time) : [], // 居住时长
            'is_work_auth' => $result->is_work_auth ?? 0, // 工作信息认证 0:未填写/未认证 1：已认证
            'is_relation_auth' => $result->is_relation_auth ?? 0, // 人际关系认证 0:未填写/未认证 1：已认证
        ];
        
        return Json::encode([
            "status" => self::STATUS_SUCCESS,
            "error_message" => '',
            "results" => $data
        ]);
    }

    /**
     * 保存个人基本信息（包括新增、修改）
     * @param string $live_area 居住区域
     * @param string $live_addr 详细地址
     * @param string $live_time 居住时长
     * @return string 正确或错误提示
     * @throws \yii\db\Exception
     */
    public function actionSaveProfile()
    {
        $request = Yii::$app -> request;
        $userBasicModel = new UserBasicModel();
        $userService = new UserService();
        $liveArea = trim(implode('-', (array)$request->post('live_area', []))); // 居住区域
        $liveAddr = trim($request->post('live_addr', '')); // 详细地址
        $liveTime = trim(implode('-', (array)$request->post('live_time', []))); // 居住时长

        if (empty($liveArea)) { // 居住区域不能为空
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '现居城市不能为空']);
        }
        if (empty($liveAddr)) { // 详细地址不能为空
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '详细地址不能为空']);
        }
        if (empty($liveTime)) { // 居住时长不能为空
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '居住时长不能为空']);
        }

        $result = $userBasicModel->saveUserBasic(Yii::$app->user->getId(), $liveArea, $liveAddr, $liveTime); // 保存个人基本信息
        if ($result) { // 保存成功
            // 视情况更改个人信息状态
            if ($userService->setProfileAuth(Yii::$app->user->getId())) { // 设置成功
                return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '保存成功']);
            }
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '保存失败']);
        }
        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '数据异常']);
    }

    /**
     * 获取工作信息
     * @return string 成功或失败的json信息
     */
    public function actionWork()
    {
        $userAdditionalModel = new UserAdditionalModel();

        $result = $userAdditionalModel->getUserAddition(Yii::$app->user->getId()); // 获取工作信息

        $data[] = [
            'industry' => $result->industry ?? '', // 从事行业
            'position' => $result->position ?? '', // 工作岗位
            'company_name' => $result->company_name ?? '', // 单位名称
            'company_area' => (isset($result->company_area) && $result->company_area != '') ? explode('-', $result->company_area) : [], // 单位所在地
            'company_addr' => $result->company_addr ?? '', // 详细信息
            'company_tel' => $result->company_tel ?? '', // 单位电话
        ];

        return Json::encode([
            "status" => self::STATUS_SUCCESS,
            "error_message" => '',
            "results" => $data
        ]);
    }

    /**
     * 保存工作信息（包括新增、修改）
     * @param string $industry 从事行业
     * @param string $position 工作岗位
     * @param string $company_name 单位名称
     * @param string $company_area 单位所在地
     * @param string $company_addr 详细信息
     * @param string $company_tel 单位电话
     * @return string 成功或失败的json信息
     * @throws \yii\db\Exception
     */
    public function actionSaveWork()
    {
        $request = Yii::$app->request;
        $userAdditionalModel = new UserAdditionalModel();
        $userBasicModel = new UserBasicModel();
        $userService = new UserService();

        $industry= trim($request->post('industry', '')); // 从事行业
        $position = trim($request->post('position', '')); // 工作岗位
        $companyName = trim($request->post('company_name', '')); // 单位名称
        $companyArea = trim(implode('-', (array)$request->post('company_area', []))); // 单位所在地
        $companyAddr = trim($request->post('company_addr', '')); // 详细信息
        $companyTel = trim($request->post('company_tel', '')); // 单位电话

        if (empty($industry)) { // 从事行业不能为空
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '从事行业不能为空']);
        }
        if (empty($position)) { // 工作岗位不能为空
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '工作岗位不能为空']);
        }
        if (empty($companyName)) { // 单位名称不能为空
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '单位名称不能为空']);
        }
        if (empty($companyArea)) { // 单位所在地不能为空
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '单位所在地不能为空']);
        }
        if (empty($companyAddr)) { // 详细地址不能为空
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '详细信息不能为空']);
        }
        if (!empty($companyTel) && strlen($companyTel) > 12){ // 单位电话限制长度
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '单位电话最大长度不能超过12位']);
        }


        $result = $userAdditionalModel->saveWorkInfo(Yii::$app->user->getId(), $industry, $position, $companyName, $companyArea, $companyAddr, $companyTel); // 保存工作信息
        if ($result) { // 保存工作信息成功
            $result1 = $userBasicModel->setAuthState(Yii::$app->user->getId(), 'is_work_auth', 1); // 更新工作信息认证状态
            // 视情况更改个人信息状态
            if ($result1 && $userService->setProfileAuth(Yii::$app->user->getId())) { // 设置成功
                return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '保存成功']);
            }
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '保存失败']);
        }
        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '数据异常']);
    }

    /**
     * 获取人际关系
     * @return string 成功或失败的json信息
     */
    public function actionRelation()
    {
        $userAdditional = new UserAdditionalModel();

        $result = $userAdditional->getUserAddition(Yii::$app->user->getId()); // 获取工作信息

        $data[] = [
            'linkman_relation_fir' => (isset($result->linkman_relation_fir) && $result->linkman_relation_fir != '') ?  explode('-', $result->linkman_relation_fir) : [], // 1号联系人与本人关系
            'linkman_name_fir' => $result->linkman_name_fir ?? '', // 1号联系人姓名
            'linkman_tel_fir' => $result->linkman_tel_fir ?? '', // 1号联系人电话
            'linkman_relation_sec' => (isset($result->linkman_relation_sec) && $result->linkman_relation_sec != '') ?  explode('-', $result->linkman_relation_sec) : [], // 2号联系人与本人关系
            'linkman_name_sec' => $result->linkman_name_sec ?? '', // 2号联系人姓名
            'linkman_tel_sec' => $result->linkman_tel_sec ?? '', // 2号联系人电话
        ];

        return Json::encode([
            "status" => self::STATUS_SUCCESS,
            "error_message" => '',
            "results" => $data
        ]);
    }

    /**
     * 保存人际关系（包括新增、修改）
     * @param string $linkman_relation_fir 1号联系人与本人关系
     * @param string $linkman_name_fir 1号联系人姓名
     * @param string $linkman_tel_fir 1号联系人电话
     * @param string $linkman_relation_sec 2号联系人与本人关系
     * @param string $linkman_name_sec 2号联系人姓名
     * @param string $linkman_tel_sec 2号联系人电话
     * @return string 成功或失败的json信息
     * @throws \yii\db\Exception
     */
    public function actionSaveRelation()
    {
        $request = Yii::$app->request;
        $userAdditionalModel = new UserAdditionalModel();
        $userBasicModel = new UserBasicModel();
        $userService = new UserService();

        $linkmanRelationFir= trim(implode('-', (array)$request->post('linkman_relation_fir', []))); // 1号联系人与本人关系
        $linkmanNameFir = trim($request->post('linkman_name_fir', '')); // 1号联系人姓名
        $linkmanTelFir = trim($request->post('linkman_tel_fir', '')); // 1号联系人电话
        $linkmanRelationSec = trim(implode('-', (array)$request->post('linkman_relation_sec', []))); // 2号联系人与本人关系
        $linkmanNameSec = trim($request->post('linkman_name_sec', '')); // 2号联系人姓名
        $linkmanTelSec = trim($request->post('linkman_tel_sec', '')); // 2号联系人电话


        if ($linkmanRelationFir == '') { // 1号联系人与本人关系不能为空
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '联系人一与本人关系不能为空']);
        }
        if (empty($linkmanNameFir)) { // 1号联系人姓名不能为空
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '联系人一姓名不能为空']);
        }
        if (empty($linkmanTelFir)) { // 1号联系人电话不能为空
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '联系人一电话不能为空']);
        }
        if ($linkmanRelationFir == '') { //  2号联系人与本人关系不能为空
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '联系人二与本人关系不能为空']);
        }
        if (empty($linkmanNameSec)) { // 2号联系人姓名不能为空
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '联系人二姓名不能为空']);
        }
        if (empty($linkmanTelSec)) { // 2号联系人电话不能为空
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '联系人二电话不能为空']);
        }

        $result1 = $userAdditionalModel->saveRelation(Yii::$app->user->getId(), $linkmanRelationFir, $linkmanNameFir, $linkmanTelFir, $linkmanRelationSec, $linkmanNameSec, $linkmanTelSec); // 保存工作信息
        if ($result1) { // 保存成功
            $result2 = $userBasicModel->setAuthState(Yii::$app->user->getId(), 'is_relation_auth', 1); // 更新人际关系认证状态
            // 视情况更改个人信息状态
            if ($result2 && $userService->setProfileAuth(Yii::$app->user->getId())) { // 设置成功
                return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '保存成功']);
            }
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '保存失败']);
        }
        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '数据异常']);
    }

    /**
     * 修改密码
     * @param string $old_password  原登录密码
     * @param string $new_password  新密码
     * @param string $repeat_password  确认密码
     * @return string
     */
    public function actionPassword()
    {
        $currentUser = Yii::$app->user->identity;
        $request = Yii::$app->request;
        $userModel = new UserModel();

        $oldPwd = trim($request->post('old_password', '')); // 当前密码
        $newPwd = trim($request->post('new_password', '')); // 新密码
        $rePwd = trim($request->post('repeat_password', '')); // 重复密码

        if (!$oldPwd || !$newPwd || !$rePwd) {
            return Json::encode(["status" => self::STATUS_FAILURE, "error_message" => "请填写当前密码,新密码,确认密码"]);
        }
        if (mb_strlen($newPwd) < 6 || mb_strlen($newPwd) > 15) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请使用6~15位数字、字母、特殊符号组合']);
        }
        if ($newPwd != $rePwd) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '新密码与确认密码不一致']);
        }
        if ( !$userModel->validatePassword($currentUser, $oldPwd)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '当前密码错误，请重试']);
        }
        $result = $userModel->updatePassword($currentUser->id, $rePwd); // 修改密码
        if ($result) {
            return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '密码修改成功']);
        }
        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '修改失败，请重试']);
    }

    /**
     * 忘记密码
     * @param string $mobile  手机号码
     * @param string $password  新密码
     * @param string $code  验证码
     * @return string
     */
    public function actionForgetPassword()
    {
        $request = Yii::$app->request;

        $password = trim($request->post('password', ''));
        $mobile = trim($request->post('mobile', ''));
        $code = trim($request->post('code', ''));
        $cardMantissa = trim($request->post('card_mantissa', '')); // 身份证后6位
        if ($mobile == '') {
            return Json::encode(["status" => self::STATUS_FAILURE, "error_message" => "请填写手机号码"]);
        }
        $user = UserModel::getUserByMobile($mobile);
        if (!$user) {
            return Json::encode(["status" => self::STATUS_FAILURE, "error_message" => "手机号未注册, 请先注册"]);
        }
        if ($code == '') {
            return Json::encode(["status" => self::STATUS_FAILURE, "error_message" => "请填写验证码"]);
        }
        if (!$password) {
            return Json::encode(["status" => self::STATUS_FAILURE, "error_message" => "请填写新密码"]);
        }
        // 身份认证已通过用户需进行该验证
        $userModel = UserModel::getUserByMobile($mobile);
        if ($userModel && $userModel->is_identity_auth == 1) {
            if ($cardMantissa == '') {
                return Json::encode(["status" => self::STATUS_FAILURE, "error_message" => "请填写身份证后六位"]);
            }
            // 对比身份证号后六位
            $userIdentityCardModel = UserIdentityCardModel::getIdentityCard($userModel->id);
            if ($userIdentityCardModel && $userIdentityCardModel->state == UserIdentityCardModel::STATE_PASS ) {
                if (strtolower(substr($userIdentityCardModel->identity_no, -6)) != strtolower($cardMantissa)) {
                    return Json::encode(["status" => self::STATUS_FAILURE, "error_message" => "身份证后六位输入有误，请重新输入"]);
                }
            }
        }

        //短信验证码
        $checkCode = MobileCodeModel::checkMobileCode($mobile, $code);
        if (!$checkCode) {
            return Json::encode(["status" => self::STATUS_FAILURE, "error_message" => SmsConfig::MOBILE_CODE_ERR]);
        }

        try {
            UserModel::updatePasswordByMobile($mobile, $password);
        } catch (Exception $e) {
            Yii::error($e);
            return Json::encode(["status" => self::STATUS_FAILURE, "error_message" => "系统故障, 密码重置失败, 请重试"]);
        }

        return Json::encode(["status" => self::STATUS_SUCCESS,"error_message" => ""]);
    }

    public function actionGetQuota()
    {
        $user = UserModel::findUserById(Yii::$app->user->getId());
        if ($user) {
            // 判断是否认证通过（实名认证、个人信息、银行卡、亲签照、[芝麻信用]、运营商认证）
            if ($user->is_identity_auth != UserModel::HAS_IDENTITY_AUTH
             || $user->is_profile_auth != UserModel::HAS_PROFILE_AUTH
             || $user->is_bankcard_auth != UserModel::HAS_PROFILE_AUTH
             || $user->is_phone_auth != UserModel::HAS_PROFILE_AUTH) {
                // 认证未全部通过，返回当前上线现金分期产品的最高可借额度
                $data['available_quota'] = 0; // 可用额度
            } else {
                $data['available_quota'] = $user->available_quota; // 可用额度
            }
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            "error_message" => "",
            'results' => $data ?? [],
        ]);
    }

    public function actionGetBaseAuthState()
    {
        $user = UserModel::findUserById(Yii::$app->user->getId());
        if ($user) {
            // 判断是否认证通过（实名认证、个人信息、银行卡、亲签照、[芝麻信用]、运营商认证）
            if ($user->is_identity_auth != UserModel::HAS_IDENTITY_AUTH
                || $user->is_profile_auth != UserModel::HAS_PROFILE_AUTH
                || $user->is_bankcard_auth != UserModel::HAS_PROFILE_AUTH
                || $user->is_phone_auth != UserModel::HAS_PROFILE_AUTH) {
                // 认证未全部通过，返回当前上线现金分期产品的最高可借额度
                $data['is_auth_pass'] = 0; // 标识是否完成所有必填认证
            } else {
                $data['is_auth_pass'] = 1; // 标识是否完成所有必填认证
            }
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            "error_message" => "",
            'results' => $data ?? [],
        ]);
    }

}
