<?php

use yii\widgets\LinkPager;
$this->registerCssFile(Yii::$app->request->baseUrl."/css/example.css");
$this->registerCssFile(Yii::$app->request->baseUrl."/css/bootstrap-4.0.0.min.css");
$this->registerCssFile(Yii::$app->request->baseUrl."/css/bootstrap-3.3.7.min.css");
$this->title = '厦门万匹思网络科技-案例中心';
?>
<div class="header" style="margin-top: 156px">
    <img src="/imgs/example-page/header.jpg" width="100%" alt="">
</div>

<div class="page-content">
    <div class="example-display container">
        <div class="header">
            <div class="bg">
                <img src="/imgs/example-page/display-header.jpg" width="100%" alt="">
            </div>
        </div>
        <?=\frontend\widgets\example\ExampleWidget::widget();?>
    </div>
