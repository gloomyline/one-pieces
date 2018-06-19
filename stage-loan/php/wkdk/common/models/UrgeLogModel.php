<?php
namespace common\models;

use common\bases\CommonModel;
use Yii;

class UrgeLogModel extends  CommonModel
{
    const RESULT_PROMISE = 1;// 客户承诺还款
    const RESULT_UNABLE = 2; // 客户无法还款
    const RESULT_SUCCESS = 3; // 催款成功
    const RESULT_DISAPPEAR = 4; // 客户失联
    const RESULT_BAD = 5; // 坏账

    const URGE_WAY_MESSAGE = 1 ; // 催款方式：1-短信
    const URGE_WAY_PHONE = 2 ; // 催款方式：2-电话
    const URGE_WAY_DOOR_TO_DOOR = 3 ; // 催款方式：3-上门
    const URGE_WAY_THIRD_PARTY = 4 ; // 催款方式：4-第三方

    /**
     * 根据催收记录id获取最新的催收日记
     * @param integer $urgeId 催收记录ID
     * @return array|null|\yii\db\ActiveRecord 返回一条最新的催收日记记录
     */
    public static function getUrgeLogByUrgeId($urgeId)
    {
        $model = UrgeLog::find();
        $model->Where(['urge_id' => $urgeId]);
        $model->orderBy(['id'=>SORT_DESC]);
        $model->limit(1);
        return $model->one();
    }

    /**
     * 根据催收记录id获取所有的催收日记
     * @param integer $urgeId 催收记录ID
     * @return array|\yii\db\ActiveRecord[] 返回该催收记录所有的催收日志
     */
    public static function getUrgeLogByUrgeIdList($urgeId)
    {
        return UrgeLog::find()->where(['urge_id' => $urgeId])->orderBy(['id' => SORT_DESC])->all();
    }

    /**
     * 某日催收次数
     * @param string $date 日期
     * @return int|string 返回查询到的结果条数
     */
    public static function statUrgeTimesCountByDate($date = '')
    {
        return UrgeLog::find()->where(["DATE_FORMAT(`created_at` ,'%Y-%m-%d')" => $date])->count();
    }

    /**某日催收笔数
     * @param string $date 日期
     * @return int|string 返回查询到的结果条数
     */
    public static  function statUrgeCountByDate($date = '')
    {
        return UrgeLog::find()
            ->select('urge_id')->where(["DATE_FORMAT(`created_at` ,'%Y-%m-%d')" => $date])
            ->distinct()
            ->count();
    }
}
