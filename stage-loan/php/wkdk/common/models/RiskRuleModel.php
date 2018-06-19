<?php

namespace common\models;


use common\bases\CommonModel;

class RiskRuleModel extends CommonModel
{
    const OPERATOR_ACCORDANT = 'accordant'; // 一致
    const OPERATOR_DISACCORD = 'disaccord'; // 不一致
    const PATTERN_RESULT = 'result'; // 结果模式
    const PATTERN_SCORE = 'score'; // 评分模式
    const STATE_DISABLE = 1; // 禁用
    const STATE_ENABLE = 2; // 启用
    const OUTCOME_PASS = 1; // 结果-通过
    const OUTCOME_NO_PASS = 2; // 结果-不通过
    const OUTCOME_NEED_CHECK = 3; // 结果-需人工审核
    const SYMBOL_INCREASE = 'increase'; // 增加
    const SYMBOL_DECREASE = 'decrease'; // 减少
    /**
     * 信用分规则配置列表数据
     * @param integer $offset 查询的基准数
     * @param integer $limit 查询的数目
     * @param string $name 字段名
     * @param integer $state 状态
     * @param array $orderBy 排序条件
     * @return array 返回数组[满足条数数据总条数，列表数据]
     */
    public static function getRiskRuleList($offset, $limit, $name, $state, $orderBy = ['id' => SORT_DESC])
    {

        $model = RiskRule::find();
        if ($name) {
            $model->andWhere(['name' => $name]);
        }
        if ($state) {
            $model->andWhere(['state' => $state]);
        }
        return [
            'count' => $model->count(),
            'result' => $model->offset($offset)->limit($limit)->orderBy($orderBy)->all()
        ];
    }

    /**
     * 添加风控规则记录
     * @param array $data 规则记录字段数组
     * @return bool|string 添加成功返回成功记录id，失败返回false
     */
    public static function add($data)
    {
        $model = new RiskRule();
        $model->setAttributes($data);

        if (!$model->validate()) {
            return false;
        }

        $result = $model->save();
        if ($result) {
            return $model->id;
        }
        return false;
    }

    /**
     * 根据传入条件查找一条规则记录
     * @param array $cond 添加参数
     * @return static 返回对象
     */
    public static function findByCondition($cond)
    {
        return RiskRule::findOne($cond);
    }

    /**
     * 删除信用分规则记录
     * @param integer $id 要删除记录的id
     * @return int 返回删除记录条数
     */
    public static function delRiskRuleById($id)
    {
        return RiskRule::deleteAll(['id' => $id]);
    }

    /**
     * 更具条件统计分数
     * @param array $cond 条件数组参数
     * @return mixed 返回统计的结果
     */
    public static function getScoreStatistics($cond = [])
    {
        return RiskRule::find()->where($cond)->sum('score');
    }

    /**
     * 获取所有系统评分规则
     * @return static
     */
    public static function getRiskRule()
    {
        return RiskRule::find()->all();
    }

}