<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;
$this->registerCssFile(Yii::$app->request->baseUrl."/css/bootstrap-3.3.7.min.css");
$this->title = $name;
?>
<div class="col-lg-12" style="height: 500px">
    <div class="site-error col-lg-8 col-md-offset-2" style="margin-top: 158px;">

        <h1><?= Html::encode($this->title) ?></h1>

        <div class="alert alert-danger">
            <?= nl2br(Html::encode($message)) ?>
        </div>

        <p>
            web服务器正在处理您的请求时发生上述错误。
        </p>
        <p>
            如果您认为这是服务器错误，请与我们联系。谢谢您!
        </p>

    </div>
</div>
<div style="clear: both"></div>

