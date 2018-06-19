<?php

/* @var $this yii\web\View */

$this->registerCssFile(Yii::$app->request->baseUrl."/css/resolution.css");
$this->registerCssFile(Yii::$app->request->baseUrl."/css/bootstrap-4.0.0.min.css");
$this->title = '厦门万匹思网络科技-解决方案';
?>
<!-- component Banner -->
<div class="banner-wrap">
    <div id="banner" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            <li data-target="#banner" data-slide-to="0" class="active"></li>
            <li data-target="#banner" data-slide-to="1"></li>
            <li data-target="#banner" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="d-block w-100" src="/imgs/banner/banner-2.jpg" alt="First slide">
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="/imgs/banner/banner-2.jpg" alt="Second slide">
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="/imgs/banner/banner-2.jpg" alt="Third slide">
            </div>
        </div>
        <a class="carousel-control-prev" href="#banner" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#banner" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
</div>
<!-- page  -->
<div class="page-content">
    <!-- component real time news -->
    <?=\frontend\widgets\notice\NoticeWidget::widget()?>

    <!-- component details content -->
    <div class="detail-list">
        <section class="scheme-introduction container">
            <div class="img-wrap">
                <img src="/imgs/resolution-page/introduction.png" width="500" height="400" alt="">
            </div>
            <div class="desc">
                <h3 class="title text-center">方案介绍</h3>
                <p class="text">通过整合线上线下多元资源，以风控管理为中心，打通消费物流平台、
                    消费金融风控平台、支付平台、征信机构、资金机构等平台，实现业务线上流程化、智能化管理</p>
            </div>
        </section>

        <section class="scheme-function">
            <div class="function-header">
                <img src="/imgs/resolution-page/function-header.jpg" width="100%" alt="">
            </div>
            <div class="function-lists text-center">
                <div class="loan function-list">
                    <h5 class="title">贷款自助营销平台</h5>
                    <ul class="list-unstyled">
                        <li>在线签约/人脸识别</li>
                        <li>贷款产品/在线还款</li>
                        <li>贷款及分期申请</li>
                    </ul>
                </div>
                <div class="deal function-list">
                    <h5 class="title">互联网金融资产交易平台</h5>
                    <ul class="list-unstyled">
                        <li>用户管理/产品管理</li>
                        <li>资金管理/运营管理</li>
                        <li>风险管理/财务管理</li>
                    </ul>
                </div>
                <div class="manage function-list">
                    <h5 class="title">金融资产管理系统</h5>
                    <ul class="list-unstyled">
                        <li>资产渠道管理/资产管理</li>
                        <li>理财产品管理/资产打包</li>
                        <li>交易匹配管理</li>
                    </ul>
                </div>
                <div class="server function-list">
                    <h5 class="title">后台管理系统</h5>
                    <ul class="list-unstyled">
                        <li>客户管理/订单管理</li>
                        <li>授信评级/规则引擎</li>
                        <li>风控审核/合同管理</li>
                        <li>放款管理/财务核算</li>
                        <li>贷后管理</li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="process-structure text-center">
            <h3 class="title">消费金融解决方案流程图</h3>
            <div class="img-wrap text-center">
                <img src="/imgs/resolution-page/process.png" width="1200" height="740" alt="">
            </div>
        </section>

    </div>
</div>
