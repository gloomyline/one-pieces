<?php
namespace common\models;

use common\bases\CommonModel;
use Yii;

class UserLimuModel extends CommonModel
{
    const TYPE_JD = 1; // 平台类型 1-京东
    const TYPE_TAOBAO = 2; // 平台类型 2-淘宝
    const TYPE_EDUCATION = 3; // 平台类型 3-学信网
    const TYPE_BILL = 4; // 平台类型 4-信用卡账单
    const TYPE_EBANK = 5; // 平台类型 5-网银流水

    const STATE_PASS = 'pass'; // 状态：pass-认证通过
    const STATE_NOPASS = 'nopass'; // 状态：nopass-认证不通过
    const STATE_BUSY = 'busy'; // 状态：busy- 待认证

    const HAS_REPORT = 1; // 是否已生成报告：0-未生成 1-已生成
    const HAS_NO_REPORT = 0; // 是否已生成报告：0-未生成 1-已生成

    /**
     * 保存用户征信信息
     * @param integer $userId 用户ID
     * @param string $mobile 手机号码
     * @param integer $state 否已生成报告 0:未生成 1:已生成
     * @param string $token 立木token
     * @param integer $platformType 平台类型：1-京东 2-淘宝 3-学历
     * @return boolean 是否添加成功 true-是 false-否
     */
    public static function addUserLimu($userId, $mobile, $state, $token, $platformType)
    {
        $userLimu = new UserLimu();
        $userLimu->user_id = $userId;
        $userLimu->mobile = $mobile;
        $userLimu->state = $state;
        $userLimu->token = $token;
        $userLimu->platform_type = $platformType;
        if (!$userLimu->validate()) {
            \Yii::info(json_encode($userLimu->getErrors()));
            return json_encode($userLimu->getErrors());
        }
        if (!$userLimu->save()) {
            return false;
        }
        return true;
    }

    /**
     * 获取用户最新的认证信息
     * @param string $mobile 手机号码
     * @param integer $platformType 认证平台类型
     * @return array|null|\yii\db\ActiveRecord 返回查询的结果
     */
    public static function getLatestCredit($mobile, $platformType)
    {
        $model = UserLimu::find();
        $model->where(['mobile' => $mobile]);
        $model->andWhere(['platform_type' => $platformType]);
        $model->orderBy('id desc');
        $model->limit(1);
        return $model->one();
    }

    /**
     * 查询京东、淘宝认证列表
     * @param integer $limit 查询的记录数
     * @param integer $offset 查询的偏移量
     * @param string $userName 用户姓名
     * @param string $mobile 手机号码
     * @param string $state 状态
     * @param integer $platformType 平台类型：1-京东 2-淘宝
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getLimuAuthList($limit, $offset, $userName, $mobile, $state, $platformType)
    {
        $model = UserLimu::find();
        $model->joinWith('user');
        $model->where(['platform_type' => $platformType]);
        if ($userName) {
            $model->andWhere(['user.real_name' =>$userName]);
        }
        if ($mobile) {
            $model->andWhere(['user_limu.mobile' =>$mobile]);
        }
        if ($state) {
            $model->andWhere(['user_limu.state' =>$state]);
        }
        $result['count'] = $model->count(); // 总记录数
        $model->offset($offset);
        $model->limit($limit);
        $result['result'] = $model->all(); // 查询的记录列表
        return $result;
    }

    /**
     * 根据ID 查询用户购物信息
     * @param integer $userLimuId 用户购物信息ID
     * @return null|object 返回查询的结果
     */
    public static function getUserLimuById($userLimuId) {
        return UserLimu::findOne(['id' => $userLimuId]);
    }

    /**
     * 根据记录id删除淘宝京东认证记录
     * @param integer $id 记录id
     * @return int 返回删除的记录条数
     */
    public static function delUserLimuById($id)
    {
        return UserLimu::deleteAll(['id' => $id]);
    }
}