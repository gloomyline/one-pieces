<?php
namespace frontend\services;

use common\models\ArticleModel;
use common\models\NavigationModel;
use frontend\bases\FrontendService;
use Yii;

class NewsService extends FrontendService
{
    /**
     * 获取新闻概要
     * @return array
     */
    public static function getProfileNews()
    {
        $nav =  NavigationModel::findChildrenByPid(Yii::$app->params['article_id']);
        $data = [];
        foreach ($nav as $v) {
            $news = ArticleModel::findAllByCondition(['nav_id' => $v['id'], 'state' => ArticleModel::STATE_SHOW]);
            foreach ($news as $n) {
                $data[sprintf('%s_%s', $n->navigation->name, $n->navigation->type)][] = [
                    'id' => $n['id'],
                    'nav_id' => $n['nav_id'],
                    'title' => $n['title'],
                    'image' => $n['image'] == '' ? './imgs/home-page/news/company-dynamic-state.jpg' : $n['image'],
                    'created_at' => $n['created_at'],
                ];
            }
        }
        return $data;
    }
}