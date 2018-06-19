<?php
namespace common\services;
use common\models\UserIdentityCardModel;
use common\models\UserBasicModel;
use common\models\UserMobileReport;
use common\models\UserMobileReportModel;
use common\models\UserModel;
use common\models\User;
use common\models\UserLimu;
use common\models\UserLimuModel;
use Yii;

class UserService
{
    const BASIC_INFO_IDCARD_MATCH = 'idcard_match';
    const BASIC_INFO_NAME_MATCH = 'name_match';
    /**
     * 添加用户身份认证
     * @param int $userId 借款额度
     * @param string $realName 借款期限
     * @param string $identityNo 产品配置
     * @param string $state 产品配置
     * @return boolean
     */
    // public static function addUserIdentity($userId, $realName, $identityNo, $state)
    // {
    //     $identity = UserIdentityCardModel::getIdentityCard($userId);
    //     //如果已经存在,则更新
    //     if ($identity) {
    //         if ($identity->state == UserIdentityCardModel::STATE_NOPASS) {
    //             return UserIdentityCardModel::updateIdentityCard($user->id, $realName, $identityNo, $state);
    //         } else {
    //             return true;
    //         }
    //     } else {
    //         return UserIdentityCardModel::addIdentityCard($user->id, $realName, $identityNo, $state);
    //     }
    // }

    /**
     * 设置个人信息认证状态
     * @param integer $userId 用户ID
     * @return boolean true 设置成功，false 设置失败
     */
    public static function setProfileAuth($userId)
    {
        $userBasicModel = new UserBasicModel();
        $userBasic = $userBasicModel->getUserBasic($userId); // 获取个人信息等认证状态信息

        // 个人信息、工作信息、人际关系 皆认证成功时，更改is_profile_auth状态，否则不用更改
        if (isset($userBasic->is_work_auth) && $userBasic->is_work_auth == 1 && $userBasic->is_relation_auth == 1 && $userBasic->live_area) {
            $user = User::findOne(['id' => $userId]);
            $user->is_profile_auth = 1; // 更新状态
            $user->updated_at= date('Y-m-d H:i:s'); // 更新时间
            if ($user->save()) { // 保存成功
                return true;
            }
            return false;
        }
        return true;
    }

    /**
     * 立木手机认证回调处理
     * @param integer $token 立木token
     * @return boolean true 设置成功，false 设置失败
     */
    public static function setUserMobileAuth($token, $content)
    {
        $userMobileReport = UserMobileReport::findOne(['token' => $token]);
        if (!$userMobileReport) {
            Yii::warning('没有相关认证结果,token=' . $token, 'limu');
            return false;
        }
        // 更新user_mobile
        $userMobileReport->content = $content;
        $userMobileReport->state = UserMobileReportModel::STATE_PASS;
        $userMobileReport->has_report = UserMobileReportModel::HAS_REPORT;
        $userMobileReport->updated_at = date('Y-m-d H:i:s');
        $userMobileReport->save();

        //更新user
        $user = User::findOne(['id' => $userMobileReport->user_id]);
        $user->is_phone_auth = UserModel::HAS_PHONE_AUTH;
        $user->updated_at= date('Y-m-d H:i:s'); // 更新时间
        if ($user->save()) { // 保存成功
            return true;
        }
        return false;
    }

    /**
     * 立木手机认证(获取运营商报告)回调处理
     * @param integer $token 立木token
     * @param string $report 运营商报告
     * @param string $baseData 运营商报告原始数据
     * @return boolean true 设置成功，false 设置失败
     */
    public static function setUserMobileReportAuth($token, $report, $baseData)
    {
        $userMobileReport = UserMobileReport::findOne(['token' => $token]);
        if (!$userMobileReport) {
            Yii::warning('没有相关认证结果,token=' . $token, 'limu');
            return false;
        }
        // 更新user_mobile_report
        $userMobileReport->report = json_encode($report);
        $userMobileReport->content = json_encode($baseData);
        $userMobileReport->state = UserMobileReportModel::STATE_PASS;
        $userMobileReport->has_report = UserMobileReportModel::HAS_REPORT;

        $userMobileReport->reg_time = $report->basicInfo ->regTime ?? null; // 入网时间

        // 存在 基本信息检测项
        if (isset($report->basicInfoCheck)) {
            foreach($report->basicInfoCheck as $k=>$v) {
                if ($v->item == self::BASIC_INFO_IDCARD_MATCH) {
                    $userMobileReport->idcard_match = (integer)$v->result; // 身份证号与运营商数据是否匹配：3-未知 2-模糊匹配成功 1-匹配成功 0-匹配失败
                    continue;
                }
                if ($v->item == self::BASIC_INFO_NAME_MATCH) {
                    $userMobileReport->name_match = (integer)$v->result; // 姓名与运营商数据是否匹配：3-未知 2-模糊匹配成功 1-匹配成功 0-匹配失败
                    continue;
                }
            }
        }

        $userMobileReport->risk_list_cnt = (integer)$report->personas->riskProfile->riskListCnt ?? 0; // 命中风险清单次数
        $userMobileReport->overdue_loan_cnt = (integer)$report->personas->riskProfile->overdueLoanCnt ?? 0; // 信贷逾期次数
        $userMobileReport->multi_lend_cnt = (integer)$report->personas->riskProfile->multiLendCnt ?? 0; // 多头借贷次数
        $userMobileReport->risk_call_cnt = (integer)$report->personas->riskProfile->riskCallCnt ?? 0; // 风险通话次数

        $userMobileReport->fre_contact_area = $report->personas->socialContactProfile->freContactArea ?? ''; // 最常联系人区域
        $userMobileReport->contact_num_cnt = (integer)$report->personas->socialContactProfile->contactNumCnt ?? 0; // 联系人号码总数
        $userMobileReport->interflow_contact_cnt = (integer)$report->personas->socialContactProfile->interflowContactCnt ?? 0; // 互通号码数

        $userMobileReport->avg_call_cnt = (double)$report->personas->callProfile->avgCallCnt ?? 0.00; // 日均通话次数
        $userMobileReport->avg_call_time = (double)$report->personas->callProfile->avgCallTime ?? 0.00; // 日均通话时长（m）
        $userMobileReport->silence_cnt = (integer)$report->personas->callProfile->silenceCnt ?? 0; // 静默次数
        $userMobileReport->night_call_cnt = (integer)$report->personas->callProfile->nightCallCnt ?? 0; // 夜间通话次数
        $userMobileReport->night_avg_call_time = (double)$report->personas->callProfile->nightCallTime ?? 0.00; // 夜间平均通话时长

        $userMobileReport->avg_fee_month = (double)$report->personas->consumptionProfile ->avgFeeMonth ?? 0.00; // 月均消费


        $userMobileReport->updated_at = date('Y-m-d H:i:s');
        $userMobileReport->save();

        //更新user
        $user = User::findOne(['id' => $userMobileReport->user_id]);
        $user->is_phone_auth = UserModel::HAS_PHONE_AUTH;
        $user->age = (integer)$report->basicInfo->age ?? 0;
        $user->gender = isset($report->basicInfo->gender)? ($report->basicInfo->gender == '男' ? UserModel::GENDER_MALE : UserModel::GENDER_FEMALE) :  UserModel::GENDER_UNKNOWN;
        $user->updated_at= date('Y-m-d H:i:s'); // 更新时间
        if ($user->save()) { // 保存成功
            return true;
        }
        return false;
    }

    /**
     * 立木京东查询回调处理
     * @param integer $token 立木token
     * @param string $content 回调的内容
     * @param integer $platformType 查询的平台类型 1-京东 2-淘宝
     * @return boolean true 设置成功，false 设置失败
     */
    public static function setUserLimuAuth($token, $content, $platformType = UserLimuModel::TYPE_JD)
    {
        $userShopping = UserLimu::findOne(['token' => $token]);
        if (!$userShopping) {
            Yii::warning('没有相关认证结果,token=' . $token, 'limu');
            return false;
        }
        // 更新user_mobile
        $userShopping->content = json_encode($content);
        $userShopping->state = UserLimuModel::STATE_PASS;
        $userShopping->has_report = UserLimuModel::HAS_REPORT;
        $userShopping->updated_at = date('Y-m-d H:i:s');
        $userShopping->save();

        //更新user
        $user = User::findOne(['id' => $userShopping->user_id]);
        if ($platformType == UserLimuModel::TYPE_JD) {
            $user->is_jd_auth = UserModel::HAS_JD_AUTH;
        } else if($platformType == UserLimuModel::TYPE_TAOBAO) {
            $user->is_taobao_auth = UserModel::HAS_TAOBAO_AUTH;
        } else if($platformType == UserLimuModel::TYPE_EDUCATION) {
            $user->is_edu_auth = UserModel::HAS_EDU_AUTH;
            $user->education = $content->educationInfo->arrangement  ?? ''; // 学历信息->层次
        } else if ($platformType == UserLimuModel::TYPE_BILL) {
            $user->is_bill_auth = UserModel::HAS_BILL_AUTH;
        } else if ($platformType == UserLimuModel::TYPE_EBANK) {
            $user->is_ebank_auth = UserModel::HAS_EBANK_AUTH;
        }
        $user->updated_at= date('Y-m-d H:i:s'); // 更新时间
        if ($user->save()) { // 保存成功
            return true;
        }
        return false;
    }
}