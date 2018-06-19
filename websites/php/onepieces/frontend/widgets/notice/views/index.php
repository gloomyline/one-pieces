<?php
/**
 * Created by PhpStorm.
 * User: lzh
 * Date: 2018/3/22
 * Time: 15:01
 */
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div id="real-time-news">
    <div class="container">
        <div class="title">通知：</div>
        <div class="marquee">
            <span class="begin">
                <?php if (!empty($notice)):?>
                    <?php foreach ($notice as $list):?>
                        <a href="<?=Url::to(['/news', 'news_id' => $list['offset']])?>" target="_blank"><?=Html::decode($list['title'])?>&nbsp;&nbsp;<?=$list['created_at']?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php endforeach;?>
                <?php endif;?>
            </span>
            <span class="end"></span>
        </div>
        <div class="more"><a href="<?=Url::to(['/news'])?>">更多...</a></div>
    </div>
</div>


<?php
use yii\web\View;
$this->registerJsFile(Yii::$app->request->baseUrl."/js/marquee.js");
$this->registerJsFile(Yii::$app->request->baseUrl."/js/main.js");
?>