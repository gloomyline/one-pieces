<?php
namespace common\models;

use frontend\bases\FrontendController;
use Yii;

class UserMobileReportModel extends FrontendController
{
    const HAS_NO_REPORT = 0; // 是否已经生成报告 0-未生成
    const HAS_REPORT = 1; // 是否已经生成报告 1-已生成

    const STATE_BUSY = 'busy'; // 认证状态：busy-待认证
    const STATE_PASS = 'pass'; // 认证状态：pass-认证通过
    const STATE_NOPASS = 'nopass'; // 认证状态：nopass-认证不通过

    const INCARD_MATCH_UNKNOWN = 3; // 身份证号与运营商数据是否匹配 3-未知
    const INCARD_FUZZY_MATCH_SUCCESS = 2; //身份证号与运营商数据是否匹配 2-模糊匹配成功
    const INCARD_MATCH_SUCCESS = 1; // 身份证号与运营商数据是否匹配 1-匹配成功
    const INCARD_MATCH_FAILURE = 0; // 身份证号与运营商数据是否匹配 0-匹配失败

    const NAME_MATCH_UNKNOWN = 3; // 姓名与运营商数据是否匹配 3-未知
    const NAME_FUZZY_MATCH_SUCCESS = 2; //姓名与运营商数据是否匹配 2-模糊匹配成功
    const NAME_MATCH_SUCCESS = 1; // 姓名与运营商数据是否匹配 1-匹配成功
    const NAME_MATCH_FAILURE = 0; // 姓名与运营商数据是否匹配 0-匹配失败

    /**
     * 保存运营商报告信息
     * @param integer $userId 用户ID
     * @param string $mobile 手机号码
     * @param integer $state 否已生成报告 0:未生成 1:已生成
     * @param string $token 立木token
     * @return boolean 是否添加成功 true-是 false-否
     */
    public static function addUserMobileReport($userId, $mobile, $state, $token)
    {
        $userMobileReport = new UserMobileReport();
        $userMobileReport->user_id = $userId;
        $userMobileReport->mobile = $mobile;
        $userMobileReport->state = $state;
        $userMobileReport->token = $token;
        if (!$userMobileReport->validate()) {
            \Yii::info(json_encode($userMobileReport->getErrors()));
            return json_encode($userMobileReport->getErrors());
        }
        if (!$userMobileReport->save()) {
            return false;
        }
        return true;
    }

    /**
     * 获取用户最新的认证记录
     * @param string $mobile 手机
     * @return object|boolean
     */
    public static function getLatestCredit($mobile)
    {
        $model = UserMobileReport::find();
        $model->where(['mobile' => $mobile]);
        $model->orderBy('id desc');
        $model->limit(1);
        return $model->one();
    }

    /**
     * 获取手机认证列表
     * @param integer $offset 查询的偏移量
     * @param integer $limit 查询的记录数
     * @param string $realName 真实姓名
     * @param string $userName 用户名
     * @param integer $state 认证状态
     * @param array $orderBy 排序
     * @return array|\yii\db\ActiveRecord[] 返回手机认证数据对象/记录总条数
     */
    public static function getMobileAuthList($offset, $limit, $realName, $userName, $state, $orderBy = ['user_mobile_report.id' => SORT_DESC])
    {
        $data = [];
        $model = UserMobileReport::find()
            ->select('*, user_mobile_report.id as id , user_mobile_report.mobile as mobile , user_mobile_report.created_at as created_at , user_mobile_report.state as state')
            ->joinWith('user');
        if ($realName) {
            $model->andWhere(['user.real_name' => trim($realName)]); // 真实姓名
        }
        if ($userName) {
            $model->andWhere(['user.mobile' => trim($userName)]); // 用户名
        }

        if ($state != '') {

            $model->andWhere(['user_mobile_report.state' => $state]); // 认证状态
        }
        $data['count'] = $model->count();
        $data['result'] =  $model->offset($offset)->limit($limit)->orderBy($orderBy)->all();
        return $data;
    }

    /**
     * 获取用户手机认证记录
     * @param int $userMobileId  手机认证id
     * @return null|object 返回查询的记录
     */
    public static function getMobileCredit($userMobileId)
    {
        $model = UserMobileReport::find();
        $model->Where(['id' => $userMobileId]);
        return $model->one();
    }

    /**
     * 删除手机认证记录
     * @param $id 手机认证记录id
     * @return int 返回成功删除条数
     */
    public static function delUserMobileReportById($id)
    {
        return UserMobileReport::deleteAll(['id' => $id]);
    }

    /**
     * 获取用户手机认证最新的报告数据
     * @param integer $userId 用户id
     * @return array|null|\yii\db\ActiveRecord 返回该用户最新的手机认证成功的报告数据对象
     */
    public static function findLastSuccessMobileReportByUserId($userId)
    {
        $model = UserMobileReport::find()->where(['user_id' => $userId, 'state' => self::STATE_PASS])->orderBy(['id' => SORT_DESC])->one();
        return $model;
    }
}