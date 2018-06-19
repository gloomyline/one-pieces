<?php
/**
 * Created by PhpStorm.
 * User: lzh
 * Date: 2018/3/16
 * Time: 17:14
 */

namespace common\models;


use common\bases\CommonModel;

class BannerModel extends CommonModel
{
    const STATE_SHOW = 1; // 显示
    const STATE_HIDDEN = 2; // 隐藏

    /**
     * 添加banner
     * @param array $data 记录参数
     * @return bool|string
     */
    public static function add($data)
    {
        $model = new Banner();
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
        $model = Banner::findOne(['id' => $id]);
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
        return Banner::deleteAll(['id' => $id]);
    }

    /**
     * 获取所有banner
     * @param integer $offset
     * @param integer $limit
     * @return array [查询条数，返回数据记录]
     */
    public static function getAllBanner($offset, $limit)
    {
        $model = Banner::find();

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
        return Banner::findOne(['id' => $id]);
    }
}