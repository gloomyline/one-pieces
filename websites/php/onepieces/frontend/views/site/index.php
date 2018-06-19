<?php
 use yii\helpers\Html;
 use common\models\NavigationModel;
 use yii\helpers\Url;
$this->registerCssFile(Yii::$app->request->baseUrl."/css/bootstrap-4.0.0.min.css");
$this->title = '厦门万匹思网络科技-首页';
?>
<?=frontend\widgets\banner\BannerWidget::widget()?>

<!-- homepage content -->
<div class="page-content">
    <!-- component real time news -->
    <?=\frontend\widgets\notice\NoticeWidget::widget()?>

    <!-- component details content -->
    <div class="detail-list">
        <section class="news">
            <div class="news-header detail-header text-center">
                <h2 class="primary-title">新闻资讯</h2>
                <div class="separator-line"></div>
                <h5 class="secondary-title">NEWS</h5>
            </div>

            <div class="news-lists container">
                <?php foreach ($news as $k => $new):
                    if ((integer)explode('_', $k)[1] == NavigationModel::TYPE_IMAGE_LIST) {
                ?>
                    <div class="company-dynamic-state news-list">
                        <h5 class="state-title"><?= explode('_', $k)[0] ?><a href="<?=Url::to(['news'])?>" class="more">更多</a></h5>
                        <div class="state-list">
                            <?php $i = 1 ?>
                            <?php foreach ($new as $d): ?>
                                <a class="state-link text-center" href="<?=Url::to(['/news', 'news_id' => $i++, 'nav' => $d['nav_id']])  ?>"  title="<?=Html::encode($d['title'])?>">
                                    <img width="150px" height="100px" src="<?= $d['image'] ?>" alt="" class="state-image">
                                    <p class="name text-center"><?= mb_strlen($d['title']) > 8 ? sprintf('%s%s', mb_substr($d['title'], 0, 7), '...') :  Html::encode($d['title'])?></p>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="latest-contract news-list">
                        <h5 class="contract-title"><?= explode('_', $k)[0] ?><a href="<?=Url::to(['news'])?>" class="more">更多</a></h5>
                        <ul class="contract-list">
                            <?php $i = 1 ?>
                            <?php foreach ($new as $d): ?>
                                <li class="contract-item" title="<?=Html::encode($d['title'])?>">
                                    <a href="<?=Url::to(['/news', 'news_id' => $i++, 'nav' => $d['nav_id']])  ?>"  title="<?=Html::encode($d['title'])?>" class="contract-link"><?=  mb_strlen($d['title']) > 22 ? sprintf('%s%s', mb_substr($d['title'], 0, 22), '...') :  Html::encode($d['title']) ?>  <span class="align-right"><?= $d['created_at'] ?></span></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php } ?>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="core-product">
            <div class="detail-header text-center">
                <h2 class="primary-title">拳头产品</h2>
                <div class="separator-line"></div>
                <h5 class="secondary-title">HIGH-QUALITY PRODUCTS</h5>
            </div>

            <div class="container">
                <ul class="product-list list-unstyled">
                    <li class="product-item consumer-finance">
                        <a href="#" class="product-link">
                            <img src="/imgs/home-page/core-product/consumer-finance-system.jpg" width="100%" height="100%">
                            <div class="mask">
                                <div class="bg"></div>
                                <div class="content text-center">
                                    <div class="item-icon-panel">
                                        <img class="item-icon" src="/imgs/home-page/core-product/icon-consumer-finance.png" width="80" height="80">
                                        <img class="item-icon-hover" src="/imgs/home-page/core-product/icon-consumer-finance.png" width="80" height="80">
                                    </div>
                                    <div class="item-line-panel"><span class="icon-line"></span></div>
                                    <h3 class="item-title">消费金融系统</h3>
                                    <p class="item-desc text-left">消费金融综合系统是一款集电商金融系统、渠道加盟系统、智能信贷系统、规则引擎风控系统、
                                        资金结算系统、智能催收系统为一体的综合业务系统。</p>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="product-item block-chain">
                        <a href="#" class="product-link">
                            <img src="/imgs/home-page/core-product/block-chain.jpg" alt="" width="100%" height="100%">
                            <div class="mask">
                                <div class="bg"></div>
                                <div class="content text-center">
                                    <div class="item-icon-panel">
                                        <img class="item-icon" src="/imgs/home-page/core-product/icon-block-chain.png" width="80" height="80">
                                        <img class="item-icon-hover" src="/imgs/home-page/core-product/icon-block-chain.png" width="80" height="80">
                                    </div>
                                    <div class="item-line-panel"><span class="icon-line"></span></div>
                                    <!-- <h3 class="item-title">区块链</h3> -->
                                    <h3 class="item-title">小贷`</h3>                                    
                                    <!-- <p class="item-desc text-left">区块链+数据挖矿系统、数字货币交易系统、区块链发币服务、OTC场外交易系统、区块链游戏（虚拟宠物）、区块链资讯平台，区块链全套解决方案，助您快速落地区块链应用。</p> -->
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="product-item cash-loan">
                        <a href="#" class="product-link">
                            <img src="/imgs/home-page/core-product/cach-loan.jpg" alt="" width="100%" height="100%">
                            <div class="mask">
                                <div class="bg"></div>
                                <div class="content text-center">
                                    <div class="item-icon-panel">
                                        <img class="item-icon" src="/imgs/home-page/core-product/icon-cash-loan.png" width="80" height="80">
                                        <img class="item-icon-hover" src="/imgs/home-page/core-product/icon-cash-loan.png" width="80" height="80">
                                    </div>
                                    <div class="item-line-panel"><span class="icon-line"></span></div>
                                    <h3 class="item-title">现金贷</h3>
                                    <p class="item-desc text-left">基于大数据智能风控的小额信贷系统，通过整合各种智能化工具、数据、通信、规则实现自动进件、自动审批、自动放款、自动还款、自动催收等环节，使信贷业务的开展实现智能化、可控化、简单化、快速化。</p>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="product-item offline">
                        <a href="#" class="product-link">
                            <img src="/imgs/home-page/core-product/offline-by-stages.jpg" alt="" width="100%" height="100%">
                            <div class="mask">
                                <div class="bg"></div>
                                <div class="content text-center">
                                    <div class="item-icon-panel">
                                        <img class="item-icon" src="/imgs/home-page/core-product/icon-offline.png" width="80" height="80">
                                        <img class="item-icon-hover" src="/imgs/home-page/core-product/icon-offline.png" width="80" height="80">
                                    </div>
                                    <div class="item-line-panel"><span class="icon-line"></span></div>
                                    <h3 class="item-title">线下分期系统</h3>
                                    <p class="item-desc text-left">系统整合了电商、支持线上+线下相结合的O2O模式，推出应用场景化产品购物分期、消费信贷、现金分期、随借随还、现金贷等多种金融产品 ，租房、汽车、教育 、装修、旅游等多样化消费场景。</p>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="product-item internet-banking">
                        <a href="#" class="product-link">
                            <img src="/imgs/home-page/core-product/internet-banking.jpg" alt="" width="100%" height="100%">
                            <div class="mask">
                                <div class="bg"></div>
                                <div class="content text-center">
                                    <div class="item-icon-panel">
                                        <img class="item-icon" src="/imgs/home-page/core-product/icon-internet-banking.png" width="80" height="80">
                                        <img class="item-icon-hover" src="/imgs/home-page/core-product/icon-internet-banking.png" width="80" height="80">
                                    </div>
                                    <div class="item-line-panel"><span class="icon-line"></span></div>
                                    <h3 class="item-title">互联网理财系统</h3>
                                    <p class="item-desc text-left">互联网理财系统弥补了P2P网贷系统投资产品单一化的问题，满足了投资人随投随取、多种产品、多种选择、风险更小、收益更多等贴心需求，让平台运营起来的玩法更加多样。</p>
                                </div>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </section>

        <section class="mobile-terminal">
            <div class="top">
                <div class="bg">
                    <img src="/imgs/home-page/mobile-terminal/top-bg.jpg" width="100%">
                </div>
                <div class="detail-header text-center">
                    <h2 class="primary-title">配套移动端</h2>
                    <div class="separator-line"></div>
                    <h5 class="secondary-title">MOBILE TERMINAL</h5>
                </div>
                <div class="mobile-links-wrap">
                    <ul class="mobile-links list-inline text-center">
                        <li class="mobile-link">
                            <img src="/imgs/home-page/mobile-terminal/icon-1.png" width="50" height="50">
                            <p class="name">WAP网页</p>
                        </li>
                        <li class="mobile-link">
                            <img src="/imgs/home-page/mobile-terminal/icon-2.png" width="50" height="50">
                            <p class="name">Android APP</p>
                        </li>
                        <li class="mobile-link">
                            <img src="/imgs/home-page/mobile-terminal/icon-3.png" width="50" height="50">
                            <p class="name">IOS APP</p>
                        </li>
                        <li class="mobile-link">
                            <img src="/imgs/home-page/mobile-terminal/icon-4.png" width="50" height="50">
                            <p class="name">微信版</p>
                        </li>
                    </ul>
                </div>

            </div>
            <div class="mid">
                <img src="/imgs/home-page/mobile-terminal/bottom-bg.jpg" width="100%">
            </div>
            <div class="bottom container">
                <ul class="extra-descs list-inline">
                    <li class="extra-desc"><img src="/imgs/home-page/mobile-terminal/item-1.png" alt=""></li>
                    <li class="extra-desc"><img src="/imgs/home-page/mobile-terminal/item-2.png" alt=""></li>
                    <li class="extra-desc"><img src="/imgs/home-page/mobile-terminal/item-3.png" alt=""></li>
                    <li class="extra-desc"><img src="/imgs/home-page/mobile-terminal/item-4.png" alt=""></li>
                    <li class="extra-desc"><img src="/imgs/home-page/mobile-terminal/item-5.png" alt=""></li>
                </ul>
            </div>
        </section>

        <?=frontend\widgets\partner\PartnerWidget::widget()?>
    </div>
</div>