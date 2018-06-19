<?php
/**
 * Created by PhpStorm.
 * User: lzh
 * Date: 2017/11/28
 * Time: 16:21
 */

namespace common\models;


use common\bases\CommonModel;
use Yii;

class OverdueLogModel extends CommonModel
{
    /**
     * 添加逾期日志
     * @param array $data 相关参数
     * @return bool 添加成功返回true,失败返回flase
     */
    public static function add($data)
    {
        $model = new OverdueLog();
        $model->setAttributes($data);
        if ($model->validate()) {
            if (!$model->save()) {
                return false;
            }
            return true;
        }
        Yii::info('OverdueLogModel add Error : ' . $model->getErrors());
        return false;
    }

    /**
     * 跟新逾期日志 -- 逾期天数,逾期金额
     * @param integer $planId 计划id
     * @param double $overdueFees 逾期费用
     * @param integer $overdueDays 逾期天数
     * @return bool 跟新成功返回true，失败返回flase
     */
    public static function update($planId, $overdueFees, $overdueDays)
    {
        $model = OverdueLog::findOne(['plan_id' => $planId]);
        $model->overdue_days = $overdueDays;
        $model->overdue_fees = $overdueFees;
        if ($model->save()) {
            return true;
        }

        Yii::info('OverdueLogModel update Error : ' . $model->getErrors());
        return false;
    }

    /**
     * 根据用户id遍历所有的逾期记录
     * @param integer $userId 用户id
     * @return array|\yii\db\ActiveRecord[] 查找成功返回数据对象，失败返回空对象
     */
    public static function findOverdueByUserId($userId)
    {
        return OverdueLog::find()->joinWith('loan')->where(['loan.user_id' => $userId])->all();
    }

    /**
     * 根据计划ID 查询逾期日志
     * @param integer $planId 计划ID
     * @return static
     */
    public static function findOneByPlanId($planId)
    {
        return OverdueLog::find()->where(['plan_id' => $planId])->one();
    }
}