<?php
/**
 * Created by PhpStorm.
 * User: lzh
 * Date: 2018/3/16
 * Time: 17:14
 */

namespace common\models;


use common\bases\CommonModel;

class PartnerModel extends CommonModel
{
    const STATE_SHOW = 1; // 显示
    const STATE_HIDDEN = 2; // 隐藏
    public static function add($data)
    {
        $model = new Partner();
        $model->setAttributes($data);
        $result = $model->save();
        if ($result) {
            return $model->id;
        }
        return false;
    }

    /**
     * 修改记录
     * @param integer $id
     * @param array $data
     * @return bool
     */
    public static function update($id, $data)
    {
        $model = Partner::findOne(['id' => $id]);
        $model->setAttributes($data);
        $result = $model->save();
        if ($result) {
            return true;
        }
        return false;
    }

    /**
     * 根据id删除记录
     * @param integer $id
     * @return int 成功删除条数
     */
    public static function delById($id)
    {
        return Partner::deleteAll(['id' => $id]);
    }

    /**
     * 获取所有的导航
     * @param integer $offset
     * @param integer $limit
     * @return array [查询条数，返回数据记录]
     */
    public static function getAllPartner($offset, $limit)
    {
        $model = Partner::find();
        return [
            'count' => $model->count(),
            'result' => $model->offset($offset)->limit($limit)->orderBy(['sort' => SORT_DESC])->all()
        ];
    }

    /**
     * 根据id查找合作伙伴
     * @param integer $id
     * @return static
     */
    public static function findOneById($id)
    {
        return Partner::findOne(['id' => $id]);
    }
}