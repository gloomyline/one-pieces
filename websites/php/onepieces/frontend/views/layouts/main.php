<?php
# @Author: AlanWang
# @Date:   2018-03-16T08:56:53+08:00
# @Filename: main.php
# @Last modified by:   AlanWang
# @Last modified time: 2018-03-16T10:30:22+08:00

use yii\helpers\Html;
use yii\web\View;

/* @var $content string */
$this->registerCssFile(Yii::$app->request->baseUrl."/css/navbar.css");
$this->registerCssFile(Yii::$app->request->baseUrl."/css/main.css");

$this->registerJsFile(Yii::$app->request->baseUrl."/js/jquery-3.3.1.min.js",["depends"=>["frontend\assets\AppAsset"], "position"=> View::POS_HEAD]);
$this->registerJsFile(Yii::$app->request->baseUrl."/js/bootstrap-4.0.0.min.js",["depends"=>["frontend\assets\AppAsset"], "position"=> View::POS_HEAD]);
$this->registerJsFile(Yii::$app->request->baseUrl."/js/navbar.js",["depends"=>["frontend\assets\AppAsset"], "position"=> View::POS_END]);

// $this->title = '厦门万匹思网络科技';
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body style="min-width: 1200px">
<?php $this->beginBody() ?>
<?=frontend\widgets\header\HeaderWidget::widget()?>

<?= $content ?>
<?=frontend\widgets\footer\FooterWidget::widget()?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
