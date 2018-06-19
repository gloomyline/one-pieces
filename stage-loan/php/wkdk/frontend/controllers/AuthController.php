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
use common\models\UserModel;
use common\models\VisaModel;
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
     * 返回更多认证信息（淘宝、京东、学历、信用卡账单、网银流水、央行征信、公积金、社保）
     * 亲签照（返回图片地址）
     * @return string 用户的 认证信息
     */
    public function actionGetCommonAuth()
    {
        $stateType = ['taobao', 'jd', 'education', 'bill', 'ebank', 'credit', 'housefund', 'socialsecurity'];
        $valueType = ['sign_pic'];
        $request = Yii::$app->request;
        $authType = $request->get('auth_type', ''); // 认证类型
        $shopId = $request->get('shop_id', ''); // 商家ID
        if (!$authType) {
            return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => '认证类型不能为空']);
        }
        if (!in_array($authType, $stateType) && !in_array($authType, $valueType))  {
            return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => '错误的认证类型']);
        }

        if (in_array($authType, $stateType)) { // 若认证类型为 淘宝、京东、学历时
            $user = Yii::$app->user->identity;
            if ($authType == 'taobao') {
                $field = 'is_taobao_auth';
            } else if ($authType == 'jd'){
                $field = 'is_jd_auth';
            } else if ($authType == 'education') {
                $field = 'is_edu_auth';
            } else if ($authType == 'bill') {
                $field = 'is_bill_auth';
            } else if ($authType == 'ebank') {
                $field = 'is_ebank_auth';
            } else if ($authType == 'credit') {
                $field = 'is_credit_auth';
            } else if ($authType == 'housefund') {
                $field = 'is_housefund_auth';
            } else if ($authType == 'socialsecurity') {
                $field = 'is_socialsecurity_auth';
            }
            $user = UserModel::getAuthState($user->id);
            $result = [[$authType => $user->$field ?? '']];
        } else {
            if (empty($shopId)) {
                return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => '商家ID不能为空']);
            }
            $visa = VisaModel::findVisaByUserIdShopId(Yii::$app->user->getId(), $shopId); // 获取当前用户的对应商家的亲签照认证信息;
            $result = [[$authType => $visa->$authType ?? '']];
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
        if (!$userBankId) {
            return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => '参数错误']);
        }
        // 设置默认卡成功
        if (UserBankModel::setBankcardDefault($userBankId, Yii::$app->user->getId())) {
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
        if (!$user) {
            return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => '登录状态出错']);
        }
        if ($user->is_credit_auth != UserModel::HAS_CREDIT_AUTH || $user->is_housefund_auth != UserModel::HAS_HOUSE_FUND_AUTH || $user->is_taobao_auth != UserModel::HAS_TAOBAO_AUTH || $user->is_bill_auth != UserModel::HAS_BILL_AUTH) {
            return Json::encode([ "status" => self::STATUS_FAILURE, "error_message" => '请先完成提额的基础认证']);
        }
        $moreAuthCount = 0;
        if ($user->is_jd_auth == UserModel::HAS_JD_AUTH) {
            $moreAuthCount++;
        }
        if ($user->is_edu_auth == UserModel::HAS_EDU_AUTH) {
            $moreAuthCount++;
        }
        if ($user->is_ebank_auth == UserModel::HAS_EBANK_AUTH) {
            $moreAuthCount++;
        }
        if ($user->is_socialsecurity_auth == UserModel::HAS_SOCIAL_SECURITY_AUTH) {
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