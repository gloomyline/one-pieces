<?php
/**
 * Created by PhpStorm.
 * User: lzh
 * Date: 2018/3/15
 * Time: 10:36
 */

namespace common\models;


use common\bases\CommonModel;

class NavigationModel extends CommonModel
{

    const IS_SHOW = 1; // 显示
    const IS_NOT_SHOW = 2; // 不显示
    const TYPE_TEXT_LIST = 1; // 类型：文本列表
    const TYPE_IMAGE_LIST = 2; // 类型：图片列表
    const TYPE_PAGE = 3; // 类型：页面
    const TYPE_CONTENT = 4; // 类型：内容
    const IS_OPEN_TRUE = 1; // 是否跳转链接 是
    const IS_OPEN_FALSE = 2; // 是否跳转链接 否
    const TOP_NAV = 0; // 一级导航pid

    /**
     * 添加导航
     * @param array $data 参数
     * @return bool|string 成功返回id失败返回false
     */
    public static function add($data)
    {
        $model = new Navigation();
        $model->setAttributes($data);
        $result = $model->save();
        if ($result) {
            return $model->id;
        }
        return false;
    }

    /**
     * 更具id编辑导航记录
     * @param integer $navigationId 导航id
     * @param array $data 参数
     * @return bool 编辑成功返回true，失败返回false
     */
    public static function update($navigationId, $data)
    {
        $model = Navigation::findOne(['id' => $navigationId]);
        $model->setAttributes($data);
        $result = $model->save();
        if ($result) {
            return true;
        }
        return false;
    }

    /**
     * 删除记录
     * @param integer $id 记录id
     * @return int
     */
    public static function delById($id)
    {
        return Navigation::deleteAll(['id' => $id]);
    }

    /**
     * 获取所有的导航
     * @param integer $offset
     * @param integer $limit
     * @param int $pid 上级id
     * @return array [查询条数，查询结果集]
     */
    public static function getAllNav($offset, $limit, $pid = self::TOP_NAV)
    {
        $model = Navigation::find()->where(['pid' => $pid]);
        return [
            'count' => $model->count(),
            'result' => $model->offset($offset)->limit($limit)->orderBy(['sort' => SORT_DESC])->all()
        ];
    }

    /**
     * 根据id查找记录
     * @param integer $id
     * @return static
     */
    public static function findOneById($id)
    {
        return Navigation::findOne(['id' => $id]);
    }

    /**
     * 根据上级id查找记录
     * @param integer $pid
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function findChildrenByPid($pid)
    {
        return Navigation::find()->where(['pid' => $pid])->orderBy(['sort' => SORT_DESC])->all();
    }

    /**
     * 通过父ID 查询可显示子导航
     * @param integer $pid 父ID
     * @param int $sort 排序规则
     * @param null $limit 限制条数
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function findShowChildrenByPid($pid, $limit = null, $sort = SORT_DESC)
    {
        $model = Navigation::find()->where(['pid' => $pid, 'is_show' => NavigationModel::IS_SHOW ]);
        if ($limit) {
            $model->limit($limit);
        }
        return $model->orderBy(['sort' => $sort])->all();
    }
}