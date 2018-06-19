<?php
/**
 * Created by PhpStorm.
 * User: lzh
 * Date: 2017/11/23
 * Time: 16:20
 */

namespace common\models;

use common\models\Statistics;
use common\bases\CommonModel;

class StatisticsModel extends CommonModel
{
    /**
     * 添加每日统计信息
     * @param $data 数据参数
     * @return bool
     */
    public static function add($data)
    {
        $model = new Statistics();
        $model->setAttributes($data);
        //$model->created_at = date('Y-m-d');
        if ($model->validate()) {
            if (!$model->save()) {
                return false;
            }
            return true;
        }
        Yii::info('StatisticsModel add Error : ' . $model->getErrors());
        return false;
    }

    /**
     * 获取每日统计列表数据
     * @param $offset
     * @param $limit
     * @param $start 开始时间
     * @param $end 结束时间
     * @param array $orderBy 排序
     * @return array 返回数组【满足条件的记录数，列表数据】
     */
    public static function getDailyStatisticsList($offset, $limit, $start, $end ,$orderBy = ['created_at' => SORT_DESC, 'id' => SORT_DESC])
    {

        $model = Statistics::find();
        if ($start != '') {
            $start = date('Y-m-d', (int)($start)); // 时间戳转字符串
            $model->andWhere(['>=', 'created_at', $start]); // 起始时间
        }
        if ($end != '') {
            $end = date('Y-m-d', (int)($end) + 3600 * 24 - 1); // 时间戳转字符串, 截止时间设置为当前日期的 23:59:59，如 2017-09-20 23:59:59
            $model->andWhere(['<=', 'created_at', $end]); // 截止时间
        }
        return [
            'count' => $model->count(),
            'result' => $model->offset($offset)->limit($limit)->orderBy($orderBy)->all() // 查询的结果
        ];
    }

    /**
     * 根据日期获取统计记录
     * @param $date 日期
     * @return static 返回空对象或者查询的数据对象
     */
    public static function findOneByCreatedAt($date)
    {
        return Statistics::findOne(['created_at' => $date]);
    }

}