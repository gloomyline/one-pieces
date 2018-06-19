<?php
namespace frontend\controllers;

use frontend\bases\FrontendController;
use frontend\services\NewsService;
use Yii;

/**
 * Site controller
 */
class SiteController extends FrontendController
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
    /**
     * 首页
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $param = [
            'news' => [],
        ]; // 参数初始化

        $param['news'] = NewsService::getProfileNews(); // 新闻中心
        return $this->render('index', $param);
    }


    // 解决方案
    public function actionResolution()
    {
        return $this->render('resolution');
    }

    // 案例中心
   public function actionExample()
   {
       return $this->render('example');
   }

   // 关于我们
   public function actionAboutUs()
   {
       return $this->render('about');
   }

    // 新闻中心
    public function actionNews()
    {
        return $this->render('news');
    }

}
