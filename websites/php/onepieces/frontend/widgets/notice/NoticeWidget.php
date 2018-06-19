<?php
/**
 * Created by PhpStorm.
 * User: lzh
 * Date: 2018/3/22
 * Time: 15:02
 */

namespace frontend\widgets\notice;


use common\models\ArticleModel;
use yii\base\Widget;

class NoticeWidget extends Widget
{

    public function init()
    {
        parent::init();

    }

    public function run()
    {
        $data = [];
        $notice = ArticleModel::getAllNotice() ?? []; // 获取通知信息
        foreach ($notice as $k => $v) {
            $cond = ['and', ['state' => ArticleModel::STATE_SHOW], ['>=', 'sort', $v['sort']], ['>', 'id', $v['id']]];
            $offset = ArticleModel::countOffset($cond) ?? 0;
            $data [] = [
                'id' => $v['id'],
                'title' => $v['title'],
                'offset' =>  $offset + 1,
                'created_at' => date('Y-m-d', strtotime($v['created_at']))
            ];
        }
        return $this->render('index', ['notice' => $data]);
    }
}