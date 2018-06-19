<?php
use yii\helpers\Url;
use yii\widgets\LinkPager;
$current_nav_name = '新闻中心';
?>

<div class="content-container container" style="min-height: 400px">
    <div class="nav-wrap" style="margin-top: 24px;">
        <ul class="nav nav-pills nav-justified">
            <?php if (empty($data['current_nav'])): ?>
                <li role="presentation" class="active"><a href="">新闻中心</a></li>
            <?php endif; ?>
            <?php foreach ($data['nav'] as $navList): ?>
                <li role="presentation" class="<?php if ($navList['id'] == $data['current_nav']) { $current_nav_name = $navList['name']; echo 'active'; }?>">
                    <a href="<?= Url::to(['news', 'nav' => $navList['id']]) ?>"><?= $navList['name'] ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="article" style="margin-top: 24px;">
        <div class="article-header clearfix">
            <div class="title pull-left" style="font-size: 16px;"><?=$current_nav_name?> > <?=$data['news']['title']?></div>
            <div class="buttons pull-right">
                <?php if ($data['pre_show'] > 0):?>
                    <a class="btn btn-default" href="<?=$data['current_nav'] ? Url::to(['/news', 'news_id' => $data['pre'], 'nav' => $data['current_nav']]) : Url::to(['/news', 'news_id' => $data['pre']])?>" role="button">上一篇</a>
                <?php endif;?>
                <?php if ($data['next_show']):?>
                    <a class="btn btn-default" href="<?=$data['current_nav'] ? Url::to(['/news', 'news_id' => $data['next'], 'nav' => $data['current_nav']]) : Url::to(['/news', 'news_id' => $data['next']])?>" role="button">下一篇</a>
                <?php endif;?>
                <button type="button" id="back" class="btn btn-primary">返回</button>
            </div>
        </div>
        <div class="article-title text-danger clearfix">
            <h3 class="title pull-left"><?=$data['news']['title']?></h3>
            <p class="article-info pull-right" style="margin-top: 20px;">作者：<?=$data['news']['author']?>&nbsp;&nbsp;&nbsp;&nbsp;发布时间：<?=$data['news']['created_at']?></p>
        </div>
        <div class="article-content" style="margin-top: 24px;margin-bottom: 24px;">
            <!--<p style="text-indent: 2em;">科研力量强大，重视技术创新，对产品结构...</p>-->
            <?=$data['news']['content']?>
        </div>
    </div>
</div>

<?php
$this->registerJs("
$(function () {
  $('button').click(function(){
    window.history.back();
  })
});
", \yii\web\View::POS_END);
