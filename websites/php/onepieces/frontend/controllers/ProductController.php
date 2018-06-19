<?php


namespace frontend\controllers;


use Yii;
use frontend\bases\FrontendController;

class ProductController extends FrontendController
{

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $name = trim($request->get('name', 'wkdk'));
        if (empty($name)) {
            Yii::$app->session->setFlash('error', '页面不存在！');
            return $this->goBack();
        }
        return $this->render($name);
    }

}