<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/11
 * Time: 14:17
 */
namespace frontend\controllers;

use common\models\QuotaApplyModel;
use common\models\UserBankModel;
use common\models\UserBasicModel;
use common\models\UserModel;
use frontend\bases\FrontendController;
use Yii;
use yii\helpers\Json;

class AuthController extends FrontendController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        // 访问控制
        $behaviors['access']['rules'] = [
            // 允许访客用户访问的Action
            [
                'actions' => [],
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
     * 更多认证 （QQ、微信、银行卡）
     * @return string 认证成功或错误的提示信息
     */
   public function actionCommonAuth()
   {
       $request = Yii::$app->request;
       $userBasicModel = new UserBasicModel();
       $accounts = $request->post('accounts', ''); // 帐号
       $authType = $request->post('auth_type', ''); // 认证类型
       if (!$authType) { return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => '认证类型不能为空']); }
       if (!in_array($authType, ['qq', 'wechat', 'bankcard']))  { return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => '错误的认证类型']); }
       if (!$accounts) {
           switch ($authType) {
               case 'qq' : return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => 'QQ帐号不能为空']);
               case 'wechat' : return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => '微信帐号不能为空']);
               case 'bankcard' : return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => '银行卡号不能为空']);
           }
       }

       $result = $userBasicModel->saveAuth(Yii::$app->user->getId(), [$authType=>$accounts]);

       if ($result) {
           return Json::encode([
               "status" => self::STATUS_SUCCESS,
               "error_message" => '认证成功',
           ]);
       } else {
           return Json::encode([
               "status" => self::STATUS_FAILURE,
               "error_message" => '认证未成功，请确认信息后重新认证',
           ]);
       }
   }

    /**
     * 返回更多认证信息（QQ、微信、银行卡、淘宝、京东、信用卡账单、网银流水）
     * @return string 用户的 认证信息
     */
    public function actionGetCommonAuth()
    {
        $basicAuthType = ['qq', 'wechat', 'bankcard'];
        $limuAuthType = ['taobao', 'jd', 'education', 'bill', 'ebank'];
        $request = Yii::$app->request;
        $authType = $request->get('auth_type', ''); // 认证类型
        if (!$authType) { return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => '认证类型不能为空']); }
        if (!in_array($authType, $basicAuthType) && !in_array($authType, $limuAuthType))  { return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => '错误的认证类型']); }

        if (in_array($authType, $limuAuthType)) { // 若认证类型为 淘宝、京东、学历时
            $user = Yii::$app->user->identity;
            if ($authType == 'taobao') {
                $field = 'is_taobao_auth';
            } else if($authType == 'jd'){
                $field = 'is_jd_auth';
            } else if($authType == 'education') {
                $field = 'is_edu_auth';
            } else if($authType == 'bill') {
                $field = 'is_bill_auth';
            } else if($authType == 'ebank') {
                $field = 'is_ebank_auth';
            }
            $user = UserModel::getAuthState($user->id);
            $result = [[$authType => $user->$field ?? '']];
        } else {
            $userBasicModel = new UserBasicModel();
            $userBasic = $userBasicModel->getUserBasic(Yii::$app->user->getId()); // 获取当前用户的QQ认证信息
            $result = [[$authType => $userBasic->$authType ?? '']];
        }
        return Json::encode([
            "status" => self::STATUS_SUCCESS,
            "error_message" => '',
            "results" => $result
        ]);
    }

    /**
     * 获取用户已认证的银行卡信息
     */
    public function actionGetBankcard()
    {
        $userId = Yii::$app->user->getId(); // 当前登录用户ID
        $result = UserBankModel::getBankByUserId($userId); // 获取用户的所有银行卡
        $data = [];
        foreach ($result as $v) {
            $data[] = [
                'id' => $v->id ?? 0,
                'bank_name' => $v->bank_name ?? '',
                'bank_no' => isset($v->bank_no) ? (substr($v->bank_no, 0, 4) . '********' . substr($v->bank_no, -4)) : '',
                'bank_code' => $v->bank_code ?? '',
                'is_default' => $v->is_default ?? 0
            ];
        }
        return Json::encode([
            "status" => self::STATUS_SUCCESS,
            "error_message" => '',
            "results" => $data ?? [],
        ]);

    }

    /**
     * 设置银行卡为默认卡
     */
    public function actionSetBankcardDefault()
    {
        $request =  Yii::$app->request;
        $userBankId = $request->post('id'); // 用户银行卡ID
        if (!$userBankId) {  return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => '参数错误']); }
        if (UserBankModel::setBankcardDefault($userBankId, Yii::$app->user->getId())) { // 设置默认卡成功
            return Json::encode([ "status" => self::STATUS_SUCCESS, "error_message" => '设置成功']);
        } else {
            return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => '参数错误']);
        }
    }

    /**
     * 申请提升额度
     */
    public function actionQuotaApply()
    {
        $user = Yii::$app->user->identity; // 获取当前登录用户信息
        // 验证 基础认证是否完成、更多认证至少完成2项
        if ($user->is_edu_auth != UserModel::HAS_EDU_AUTH || $user->is_taobao_auth != UserModel::HAS_TAOBAO_AUTH) {
            return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => '请先完成提额的基础认证']);
        }
        $moreAuthCount = 0;
        if ($user->is_jd_auth == UserModel::HAS_JD_AUTH) {
            $moreAuthCount++;
        }
        if ($user->is_bill_auth == UserModel::HAS_BILL_AUTH) {
            $moreAuthCount++;
        }
        if ($user->is_ebank_auth == UserModel::HAS_EBANK_AUTH) {
            $moreAuthCount++;
        }
        $userBasic = UserBasicModel::getUserBasic($user->id);
        if (!$userBasic) {
            return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => '提额的更多认证请至少完成2项']);
        }
        if ($userBasic->qq !== '') {
            $moreAuthCount++;
        }
        if ($userBasic->wechat !== '') {
            $moreAuthCount++;
        }
        if ($userBasic->bankcard !== '') {
            $moreAuthCount++;
        }
        if ($moreAuthCount < 2) {
            return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => '提额的更多认证请至少完成2项']);
        }
        
        $quotaApply = QuotaApplyModel::findQuotaApplyByUserId($user->id);

        if ($quotaApply && $quotaApply->state == QuotaApplyModel::STATE_AUDITING) {
            return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => '您的提额申请正在审核中，请耐心等待']);
        }

        $applyDetail['user_id'] = $user->id; // 用户ID
        $applyDetail['available_quota'] = $user->available_quota; // 可用额度
        $applyDetail['total_quota'] = $user->total_quota; // 总额度
        $applyDetail['state'] = QuotaApplyModel::STATE_AUDITING; // 审核状态：待审核
        $applyDetail['apply_type'] = QuotaApplyModel::TYPE_USER_APPLY; // 额度类型：用户申请

        $res = QuotaApplyModel::addQuotaApply($applyDetail); // 添加额度申请

        if ($res) {
            return Json::encode([ "status" => self::STATUS_SUCCESS, "error_message" => '您的提额申请已提交成功，请等待系统审核']);
        }
        return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => '申请失败，请重试']);
    }
}