<?php

$this->registerCssFile(Yii::$app->request->baseUrl."/css/news.css");
$this->registerCssFile(Yii::$app->request->baseUrl."/css/bootstrap-3.3.7.min.css");
$this->title = '厦门万匹思网络科技-新闻中心';
?>
<div class="page-content" style="margin-top: 158px">
    <div class="content-header">
        <img src="/imgs/news-page/header.jpg" width="100%" alt="">
    </div>
    <?=\frontend\widgets\news\NewsWidget::widget()?>
</div>
