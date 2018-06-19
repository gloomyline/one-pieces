<?php

use yii\widgets\LinkPager;
use yii\helpers\Url;

?>
<nav class="nav">
    <ul class="nav-list list-inline text-center">
        <?php if (empty($data['current_nav'])): ?>
            <li class="nav-item active"><a href="" class="nav-link">所有案例</a></li>
        <?php endif; ?>

        <?php foreach ($data['nav'] as $navList): ?>
            <li class="nav-item <?php if ($navList['id'] == $data['current_nav']) echo 'active' ?>"><a
                        href="<?= Url::to(['example', 'nav' => $navList['id']]) ?>"
                        class="nav-link"><?= $navList['name'] ?></a></li>
        <?php endforeach; ?>
    </ul>
</nav>
<div class="example-content">
    <div class="examples">
        <?php if (!empty($data['body'])): ?>
            <?php foreach ($data['body'] as $list): ?>
                <a href="<?= Url::to($list['link']) ?>" target="_blank">
                    <div class="example">
                        <div class="img-wrap">
                            <img src="<?= $list['image'] ?>" width="300" height="200" alt="">
                        </div>
                        <p class="name"><?= $list['name'] ?></p>
                    </div>
                </a>

            <?php endforeach; ?>
        <?php else: ?>
            暂无数据
        <?php endif; ?>
    </div>
    <!-- examples page nav -->
    <nav aria-label="Page navigation" style="text-align: center">
        <?php if ($this->context->page): ?>
            <?= LinkPager::widget(['pagination' => $data['page'], 'options' => ['class' => 'pagination pagination-lg'], ]); ?>
        <?php endif; ?>
    </nav>
</div>
