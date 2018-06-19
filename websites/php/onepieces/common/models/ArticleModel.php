<?php
/**
 * Created by PhpStorm.
 * User: lzh
 * Date: 2018/3/20
 * Time: 9:53
 */

namespace common\models;


use common\bases\CommonModel;

class ArticleModel extends CommonModel
{
    const STATE_SHOW = 1; // 显示
    const STATE_HIDDEN = 2; // 隐藏

    const NOTICE_TRUE = 1; // 显示在公告栏
    const NOTICE_FALSE = 2; //  不显示在公告

    /**
     * 添加
     * @param array $data 记录参数
     * @return bool|string
     */
    public static function add($data)
    {
        $model = new Article();
        $model->setAttributes($data);
        $result = $model->save(); // print_r($model->getErrors());exit();
        if ($result) {
            return $model->id;
        }
        return false;
    }

    public static function update($id, $data)
    {
        $model = Article::findOne(['id' => $id]);
        $model->setAttributes($data);
        $result = $model->save();
        if ($result) {
            return true;
        }
        return false;
    }

    public static function delById($id)
    {
        return Article::deleteAll(['id' => $id]);
    }

    /**
     * 获取所有的案例
     * @param integer $offset
     * @param integer $limit
     * @param integer $navId
     * @return array [查询条数，返回数据记录]
     */
    public static function getAllArticle($offset, $limit, $navId)
    {
        $model = Article::find();
        if ($navId) {
            $model->andWhere(['nav_id' => $navId]);
        }

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
        return Article::findOne(['id' => $id]);
    }

    /**
     * 获取所有待显示通知
     */
    public static function getAllNotice()
    {
        return Article::find()->where(['state' => ArticleModel::STATE_SHOW, 'notice' => ArticleModel::NOTICE_TRUE])
                              ->orderBy(['sort' => SORT_DESC])
                              ->all();
    }

    /**
     * 按条件查询文章信息
     * @param array $where 查询条件
     * @param integer $limit 查询的记录数
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function findAllByCondition($where, $limit = 3)
    {
        return Article::find()->with('navigation')->where($where)->limit($limit)->orderBy(['sort' => SORT_DESC, 'id'=>SORT_DESC])->all();
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
        $model = new Article();
        //查询语句
        $select = ['id', 'title', 'created_at'];
        $query = $model->find()
            ->select($select)
            ->where($cond)
            ->orderBy($orderBy);
        //获取分页数据
        $res = $model->getPages($query, $curPage, $pageSize);
        return $res;
    }

    /**
     * 获得新闻详情
     * @param integer $offset
     * @param integer $limit
     * @param integer $navId 导航id
     * @return array
     */
    public static function getOneRecord($offset, $limit, $navId) {
        $model = Article::find()->where(['state' => self::STATE_SHOW]);
        if ($navId) {
            $model->andWhere(['nav_id' => $navId]);
        }
        return [
            'count' => $model->count(),
            'result' =>  $model->offset($offset)->limit($limit)->orderBy(['sort' => SORT_DESC, 'id'=>SORT_DESC])->asArray()->one()
        ];
    }

    /**
     * 根据条件获得偏移量
     * @param array $cond 查询条件
     * @return int|string 满足天剑的结果数
     */
    public static function countOffset($cond)
    {
        return Article::find()->where($cond)->count();
    }
}