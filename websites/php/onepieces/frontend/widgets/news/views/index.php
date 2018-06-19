<?php
use yii\helpers\Url;
use yii\widgets\LinkPager;
?>

<div class="content-container container" style="min-height: 400px">
    <div class="nav-wrap" style="margin-top: 24px;">
        <ul class="nav nav-pills nav-justified">
            <?php if (empty($data['current_nav'])): ?>
                <li role="presentation" class="active"><a href="">新闻中心</a></li>
            <?php endif; ?>
            <?php foreach ($data['nav'] as $navList): ?>
                <li role="presentation" class="<?php if ($navList['id'] == $data['current_nav']) echo 'active' ?>">
                    <a href="<?= Url::to(['news', 'nav' => $navList['id']]) ?>"><?= $navList['name'] ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="news-list">
        <?php if (!empty($data['body'])): ?>
            <ul class="news-list list-group" style="margin-top: 24px">
                <?php foreach ($data['body'] as $list): ?>
                    <li class="list-group-item clearfix">
                        <p class="news-item pull-left"><a href="<?=$data['current_nav'] ? Url::to(['/news', 'news_id' => $data['start_offset']++, 'nav' => $data['current_nav']]) : Url::to(['/news', 'news_id' => $data['start_offset']++])?>"><?= $list['title'] ?></a></p>
                        <p class="time pull-right"><?= $list['created_at'] ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
        <ul class="news-list list-group" style="margin-top: 24px">
            <li class="list-group-item clearfix">
                暂无数据
            </li>
        </ul>

        <?php endif; ?>
        <nav aria-label="Page navigation" style="text-align: center">
            <?php if ($this->context->page): ?>
                <?= LinkPager::widget(['pagination' => $data['page'], 'options' => ['class' => 'pagination pagination-lg'], ]); ?>
            <?php endif; ?>
        </nav>
    </div>
</div>
