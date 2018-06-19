<?php
namespace common\models;

use common\bases\CommonModel;
use Yii;

class AntiFraudModel extends CommonModel
{
    /**
     * 根据用户id获取反欺诈信息
     * @param integer $userId 用户id
     */
    public static function getByUserId($userId)
    {
        return AntiFraud::findOne(['user_id' => $userId]);
    }

     /**
     * 添加用户反欺诈信息
     * @param $data 反欺诈数据
     * @return bool|string 成功返回id失败返回false
     */
    public static function add($data)
    {
        $model = new AntiFraud();
        $model->setAttributes($data);
        $result = $model->save();
        if ($result) {
            return $model->id;
        }
        return false;
    }

    /**
     * 根据ID更新用户反欺诈信息
     * @param $userId 用户id
     * @param $data 反欺诈数据
     * @return bool 更新成功返回true，失败返回false
     */
    public static function updateById($id, $data)
    {
        $model = AntiFraud::findOne(['id' => $id]);
        $model->setAttributes($data);
        $result = $model->save();
        if ($result) {
            return true;
        }
        return false;
    }
}