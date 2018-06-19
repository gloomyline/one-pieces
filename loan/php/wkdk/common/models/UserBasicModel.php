<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/27
 * Time: 10:27
 */
namespace common\models;

use common\bases\CommonModel;
use Yii;
class UserBasicModel extends CommonModel
{
    const WORK_AUTH_UNAUTHENTICATED = 0; // 工作信息认证 : 0-未填写/未认证
    const WORK_AUTH_CERTIFIED = 1; // 工作信息认证 : 1-已认证
    const RELATION_AUTH_UNAUTHENTICATED = 0; // 人际关系认证 : 0-未填写/未认证
    const RELATION_AUTH_CERTIFIED = 1; // 人际关系认证 : 1-已认证

    /**
     * 获取用户个人基本信息
     * @param integer $userId 用户ID
     * @return object 返回查询的结果
     */
    public static function getUserBasic($userId)
    {
        return UserBasic::find()->where(['user_id' => $userId])->one();
    }
    /**
     * 保存用户基本信息 （包括 新增、修改）
     * @param int $userId 用户ID
     * @param string $liveArea 居住区域
     * @param string $liveAddr 详细地址
     * @param string $liveTime 居住时长
     * @return boolean true 修改成功 ，false 修改失败
     */
   public static function saveUserBasic($userId, $liveArea, $liveAddr, $liveTime)
   {
       $userBasic = UserBasic::findOne(['user_id' => $userId]);
       if (!$userBasic) { // 新增
           $userBasic = new UserBasic();
           $userBasic->user_id = $userId; // 用户ID
       }
       $userBasic->live_area = $liveArea; // 居住区域
       $userBasic->live_addr = $liveAddr; // 详细地址
       $userBasic->live_time = $liveTime; // 居住时长
       $userBasic->updated_at = date('Y-m-d H:i:s'); // 更新时间

       if (!$userBasic->validate()) { // 验证rules
           return false;
       } else { // 验证通过
           if (!$userBasic->save()) {
               return false;
           }
           return true;
       }
   }

    /**
     * 设置认证状态（如：工作信息认证、人际关系认证）
     * @param string $authType 认证类型 （is_work_auth、is_relation_auth）
     * @param integer $value 值 （0:未填写/未认证 1：已认证）
     * @return boolean true 设置成功， false 设置失败
     */
    public static function setAuthState($userId, $authType, $value){
        $userBasic = UserBasic::findOne(['user_id' => $userId]);
        if (!$userBasic) {
            $userBasic = new UserBasic();
            $userBasic->user_id = $userId;
        }
        $userBasic->updated_at = date('Y-m-d H:i:s'); // 更新时间
        switch ($authType) {
            case 'is_work_auth': {
                $userBasic->is_work_auth = $value;break;
            }
            case 'is_relation_auth': {
                $userBasic->is_relation_auth = $value;break;
            }
            default: return false; // 传入参数非法
        }
        if (!$userBasic->save()) { // 保存新认证状态
            return false;
        }
        return true;
    }

    /**
     * 保存 QQ/微信/银行卡 认证信息
     * @param integer $userId 用户Id
     * @param array $authData 认证信息
     * @return boolean true 保存成功， false 保存失败
     */
    public static function saveAuth($userId, $authData)
    {
        $userBasic = UserBasic::findOne(['user_id' => $userId]);
        if (!$userBasic) { // 新增
            $userBasic = new UserBasic();
            $userBasic->user_id = $userId; // 用户ID
        }
        if (isset($authData['qq'])) $userBasic->qq = $authData['qq']; // QQ帐号
        if (isset($authData['wechat'])) $userBasic->wechat = $authData['wechat']; // 微信帐号
        if (isset($authData['bankcard'])) $userBasic->bankcard = $authData['bankcard']; // 银行卡号
        $userBasic->updated_at = date('Y-m-d H:i:s'); // 更新时间

        if (!$userBasic->validate()) { // 验证rules
            return false;
        } else { // 验证通过
            if (!$userBasic->save()) {
                return false;
            }
            return true;
        }
    }
}