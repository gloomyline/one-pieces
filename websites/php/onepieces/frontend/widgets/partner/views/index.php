<section class="our-customer text-center">
    <div class="detail-header">
        <h2 class="primary-title">我们的客户</h2>
        <div class="separator-line"></div>
        <h5 class="secondary-title">OUR CUSTOMERS</h5>
    </div>
    <p class="desc text-warning">年签约量屡创新高，累计客户2000余家，口碑产品，值得信赖，点击查看。</p>
    <div class="links-wrap container">
        <ul class="customer-links list-unstyled">
            <?php foreach ($partner as $v): ?>
                <li class="customer-link"><a href="<?= $v['link']? $v['link'] : 'javascript:void(0);' ?>" target="<?= $v['link']? '_blank' : '' ?>" title="<?=$v['name']?>"><img src="<?= $v['image']? $v['image'] : '/imgs/home-page/our-customer/default.jpg' ?>" alt="<?= $v['name'] ?>" width="200" height="70"></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>