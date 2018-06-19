<?php
namespace common\models;

use common\bases\CommonModel;
use Yii;

class LimuAreaModel extends CommonModel
{
    const STATE_ACTIVE = 1; // 支持地区
    const TYPE_HOUSE_FUND = 1; // 公积金
    const TYPE_SOCIAL_SECURITY = 2; // 社保
    const SOCIAL_SECURITY_SUPPORT = 1; // 支持社保
    const SOCIAL_SECURITY_MAINTENANCE = 0; // 社保查询维护中

    /***
     * 保存立木支持地区
     * @param string $areaCode 地区编号
     * @param string $areaName 地区名称
     * @param integer $status 是否支持
     * @param string $sortLetter 地区拼音
     * @param integer $type 类型 公积金/社保
     * @return bool
     */
    public static function addLimuArea($areaCode, $areaName, $status, $sortLetter, $type = self::TYPE_HOUSE_FUND)
    {
        $limuArea = new LimuArea();
        $limuArea->area_code = $areaCode;
        $limuArea->area_name = $areaName;
        if ($type == self::TYPE_HOUSE_FUND) {
            $limuArea->state = $status;
        }
        if ($type == self::TYPE_SOCIAL_SECURITY) {
            $limuArea->is_social_security = $status;
        }
        $limuArea->sort_letter = $sortLetter;
        if ($limuArea->save()) {
            return true;
        }
        return false;
    }

    /**
     * 获取所有立木公积金支持地区
     * @param array $where 条件
     * @return array|\yii\db\ActiveRecord[] 返回查询的结果
     */
    public static function getActiveLimuArea($where)
    {
        return LimuArea::find()->where($where)->orderBy('area_code asc')->all();
    }

    /**
     * 根据地区编号查询地区信息
     * @param string $areaCode 地区编号
     * @return object|null  返回查询的结果
     */
    public static function getAreaByAreaCode($areaCode)
    {
        return LimuArea::find()->where(['area_code' => $areaCode])->one();
    }

    /**
     * 变更 是否支持社保 状态
     * @param string $id 地区编号
     * @param integer $state 地区状态
     * @param integer $type 类型 公积金/社保
     * @return bool 是否更新成功
     */
    public static function updateById($id, $state, $type)
    {
        $limuArea = LimuArea::findOne(['id' => $id]);
        if ($type == self::TYPE_HOUSE_FUND) {
            $limuArea->state = $state;
        }
        if ($type == self::TYPE_SOCIAL_SECURITY) {
            $limuArea->is_social_security = $state;
        }
        if ($limuArea->save()) {
            return true;
        }
        return false;
    }
}