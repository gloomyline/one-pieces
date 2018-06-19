<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/27
 * Time: 13:27
 */
namespace common\models;

use common\bases\CommonModel;
use Yii;
class UserAdditionalModel extends CommonModel
{
    const RELATION_COLLEAGUE = '0'; // 0 同事
    const RELATION_BROTHERS = '1'; // 1 兄弟
    const RELATION_PARENTS = '2'; // 2 父母
    const RELATION_SISTERS = '3'; // 3 姐妹
    const RELATION_FRIENDS = '4'; // 4 朋友
    /**
     * 获取用户的附加信息 （如：工作信息、人际关系等）
     * @param string $userId 用户ID
     * @return object 返回查询的结果
     */
    public static function getUserAddition($userId)
    {
        return UserAdditional::find()->where(['user_id' => $userId])->one();
    }

    /**
     * 保存工作信息
     * @param integer $userId 用户ID
     * @param string $industry 从事行业
     * @param string $position 工作岗位
     * @param string $companyName 单位名称
     * @param string $companyArea 单位所在地
     * @param string $companyAddr 详细信息
     * @param string $companyTel 单位电话
     * @return boolean true 修改成功， false 修改失败
     */
    public static function saveWorkInfo($userId, $industry, $position, $companyName, $companyArea, $companyAddr, $companyTel)
    {
        $userAdditional = UserAdditional::findOne(['user_id' => $userId]);
        if (!$userAdditional) { // 新增
            $userAdditional = new UserAdditional();
            $userAdditional->user_id = $userId;
        }
        $userAdditional->industry = $industry; // 从事行业
        $userAdditional->position = $position; // 工作岗位
        $userAdditional->company_name = $companyName; // 单位名称
        $userAdditional->company_area = $companyArea; // 单位所在地
        $userAdditional->company_addr = $companyAddr; // 详细信息
        $userAdditional->company_tel = $companyTel; //单位电话
        $userAdditional->updated_at = date('Y-m-d H:i:s'); // 修改时间

        if ($userAdditional->validate()) { // 验证rules
            if (!$userAdditional->save()) { // 保存
                return false;
            }
            return true;
        } else { // 验证未通过
            return false;
        }
    }

    /**
     * 保存人际关系
     * @param integer $userId 用户ID
     * @param string $linkmanRelationFir 1号联系人与本人关系
     * @param string $linkmanNameFir 1号联系人姓名
     * @param string $linkmanTelFir 1号联系人电话
     * @param string $linkmanRelationSec 2号联系人与本人关系
     * @param string $linkmanNameSec 2号联系人姓名
     * @param string $linkmanTelSec 2号联系人电话
     * @return boolean ture 修改成功， false 修改失败
     */
    public static function saveRelation($userId, $linkmanRelationFir, $linkmanNameFir, $linkmanTelFir, $linkmanRelationSec, $linkmanNameSec, $linkmanTelSec)
    {
        $userAdditional = UserAdditional::findOne(['user_id' => $userId]);
        if (!$userAdditional) { // 新增
            $userAdditional = new UserAdditional();
            $userAdditional->user_id =$userId; // 用户ID
        }
        $userAdditional->linkman_relation_fir = $linkmanRelationFir; // 1号联系人与本人关系
        $userAdditional->linkman_name_fir = $linkmanNameFir; // 1号联系人姓名
        $userAdditional->linkman_tel_fir = $linkmanTelFir; // 1号联系人电话
        $userAdditional->linkman_relation_sec = $linkmanRelationSec; // 2号联系人与本人关系
        $userAdditional->linkman_name_sec = $linkmanNameSec; // 2号联系人姓名
        $userAdditional->linkman_tel_sec = $linkmanTelSec; // 2号联系人电话
        $userAdditional->updated_at = date('Y-m-d H:i:s'); // 修改时间

        if ($userAdditional->validate()) { // 验证rules
            if (!$userAdditional->save()) { // 保存
                return false;
            }
            return true;
        } else { // 验证未通过
            return false;
        }
    }

    /**
     * 根据用户id获取用户其他信息
     * @param integer $userId 用户ID
     * @return bool|static
     */
    public static function getUserAdditionalByUserId($userId)
    {
        $userAdditional = UserAdditional::findOne(['user_id' => $userId]);
        if ($userAdditional) {
            return $userAdditional;
        } else {
            return false;
        }
    }
}