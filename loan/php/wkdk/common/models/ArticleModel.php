<?php
namespace common\models;

use common\bases\CommonModel;
use Yii;

class ArticleModel extends CommonModel
{
    const TYPE_ACTIVITY = 'activity'; // 活动中心
    const TYPE_PROBLEM = 'problem'; // 常见问题
    const STATE_SHOW = '1'; // 显示
    const STATE_HIDE = '2'; // 隐藏

    /**
     * 获得文章列表
     * @param integer $offset 查询基准
     * @param integer $limit 查询条数
     * @param string $title 文章标题
     * @param string $type 文章类型
     * @return array|\yii\db\ActiveRecord[] 返回文章记录对象
     */
    public static function getArticleList($offset, $limit, $title, $type)
    {
        $articles = Article::find();
        if ($title != '') {
            $articles->andWhere(['like', 'title', $title]);
        }
        if ($type !='') {
            $articles->andWhere(['type' => $type]);
        }
        return [
            'count' => $articles->count(),
            'result' => $articles->orderBy(['id' => SORT_DESC])->offset($offset)->limit($limit)->all()
        ];
    }

    /**
     * 根据id删除文章
     * @param integer $id 文章id
     * @return int 返回删除影响条数
     */
    public static function delArticleById($id) // 通过id删除文章
    {
        return Article::deleteAll(['id' => $id]);
    }

    /**
     * 更具id获取文章内容
     * @param integer $id 文章id
     * @return static 得到一条文章记录对象
     */
    public static function getArticleById($id) // 通过id获取文章
    {
        return Article::findOne(['id' => $id]);
    }
}