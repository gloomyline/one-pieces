<?php
/**
 * Created by PhpStorm.
 * User: Lzh
 * Date: 2018/3/19
 * Time: 14:58
 */

namespace common\models;


use common\bases\CommonModel;

class ExampleModel extends CommonModel
{
    const STATE_SHOW = 1; // 显示
    const STATE_HIDDEN = 2; // 隐藏

    /**
     * 添加案例
     * @param array $data 记录参数
     * @return bool|string
     */
    public static function add($data)
    {
        $model = new Example();
        $model->setAttributes($data);
        $result = $model->save();
        if ($result) {
            return $model->id;
        }
        return false;
    }

    /**
     * 更新记录
     * @param integer $id 记录id
     * @param array $data 记录参数
     * @return bool 编辑成功返回true失败返回false
     */
    public static function update($id, $data)
    {
        $model = Example::findOne(['id' => $id]);
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
     * @return int 成功删除记录条数
     */
    public static function delById($id)
    {
        return Example::deleteAll(['id' => $id]);
    }

    /**
     * 获取所有的案例
     * @param integer $offset
     * @param integer $limit
     * @return array [查询条数，返回数据记录]
     */
    public static function getAllExample($offset, $limit)
    {
        $model = Example::find();

        return [
            'count' => $model->count(),
            'result' => $model->offset($offset)->limit($limit)->orderBy(['sort' => SORT_DESC, 'id'=>SORT_DESC])->all()
        ];
    }

    /**
     * 根据id查找记录
     * @param integer $id
     * @return static
     */
    public static function findOneById($id)
    {
        return Example::findOne(['id' => $id]);
    }

    /**
     * 查询条件
     * @param $cond
     * @param int $curPage
     * @param int $pageSize
     * @param array $orderBy
     * @return array
     */
    public static  function  getList($cond, $curPage = 1, $pageSize = 10, $orderBy = ['id'=>SORT_DESC])
    {
        $model = new Example();
        //查询语句
        $select = ['id', 'image', 'name', 'link'];
        $query = $model->find()
            ->select($select)
            ->where($cond)
            ->orderBy($orderBy);
        //获取分页数据

        $res = $model->getPages($query, $curPage, $pageSize);
        return $res;
    }
}