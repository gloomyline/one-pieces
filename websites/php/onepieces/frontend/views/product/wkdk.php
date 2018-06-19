<?php
$this->registerCssFile(Yii::$app->request->baseUrl."/css/bootstrap-4.0.0.min.css");
$this->registerCssFile(Yii::$app->request->baseUrl."/css/product.css");
$this->title = '厦门万匹思网络科技-产品中心';
?>
<!-- component Banner -->
<?=\frontend\widgets\banner\BannerWidget::widget()?>

<!-- page product content -->
<div class="page-content">
    <div id="nav-box">
        <div class="nav">
            <table class="con-conter text-center" cellspacing="0" cellpadding="0" border="0" width="100%">
                <tbody>
                <tr>
                    <td class="active"><a class="current" href="javascript:;">产品介绍</a></td>
                    <td><a href="javascript:;">系统特色</a></td>
                    <td><a href="javascript:;">业务模式</a></td>
                    <td><a href="javascript:;">配套接口</a></td>
                    <td><a href="javascript:;">配套移动端</a></td>
                    <td><a href="javascript:;">功能结构</a></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="content-container">
        <div id="productIntroduction" class="tab-panel-hook product-introduction">
            <div class="item-title">
                <h2 class="text" style="width: 122px;">产品介绍</h2>
                <div class="line"></div>
                <h3 class="e-text">PRODUCT INTRODUCTION</h3>
            </div>
            <div class="product-content">
                <div class="top-labels">
                    <span class="label-item">系统特色</span>
                    <span class="label-item">解决方案</span>
                    <span class="label-item">配套接口</span>
                    <span class="label-item">配套移动端</span>
                </div>
                <div class="center-info">
                    <!-- <img src="./product_introduction_paragraph.png" alt=""> -->
                    <p class="info-text"><span class="large">悟空互金</span>小贷系统是悟空互金针对互联网金融公司、传统小贷公司新型小贷业务需求，研发的一款基于移动端的纯信用小额贷款的产品，能够搭建小额信用贷款APP和微信端，定制开发小额信用贷款APP和微信端贷款解决方案。该系统以大数据、云计算、金融科技等为基础，融合“互联网+金融+电商”三大属性，传统小贷OA管理模式。系统基于大数据分析，结合反欺诈系统及风控能力对借款人资信进行评估，实现授信放款。还可根据不同产品设置不同的还款方式、费用标准、风控模型和审批流程，并嵌入异常操作检测、数据加密、手机动态口令、多种密码保护等策略，保障平台稳定运行。</p>
                </div>
                <div class="bottom-logos">
                    <div class="version logo-item">
                        <div class="logo">
                            <img src="/imgs/product-page/product_ver.png">
                        </div>
                        <div class="desc">
                            <span class="title">系统标准版本号</span></br>
                            <span class="text">v2.0</span>
                        </div>
                    </div>
                    <div class="registration logo-item">
                        <div class="logo">
                            <img src="/imgs/product-page/product_reg.png">
                        </div>
                        <div class="desc">
                            <span class="title">知识产权登记号</span></br>
                            <span class="text">2016SR277489</span>
                        </div>
                    </div>
                    <div class="support logo-item">
                        <div class="logo">
                            <img src="/imgs/product-page/product_sup.png">
                        </div>
                        <div class="desc">
                            <span class="title">支持版本类型</span></br>
                            <span class="text">员工贷、学生贷、业主贷<br />现金贷、消费贷等</span>
                        </div>
                    </div>
                    <div class="union logo-item">
                        <div class="logo">
                            <img src="/imgs/product-page/product_uni.png">
                        </div>
                        <div class="desc">
                            <span class="title">业务整合类型</span></br>
                            <span class="text">线上线下整合、资产端多业务整合</br>资金端业务整合</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-img-wrap">
                <img src="/imgs/product-page/bg_production_introduction.jpg" alt="">
            </div>
        </div>

        <div id="systemFeature" class="tab-panel-hook system-feature">
            <div class="bg-img-wrap">
                <img src="/imgs/product-page/bg_system_feture.jpg" alt="">
            </div>
            <div class="system-title">
                <div class="item-title" class="unique">
                    <h2 class="text" style="width: 122px;">系统特色</h2>
                    <div class="line"></div>
                    <h3 class="e-text">SYSTEM FEATURE</h3>
                </div>
            </div>
            <div class="system-content">
                <div class="content-wrap">
                    <div class="order-item">
                        <div class="icon-wrap">
                            <i class="icon icon-auto"></i>
                        </div>
                        <div class="item-title">
                            <p class="title-text">无人工</p>
                            <p class="title-text">系统自动审</p>
                        </div>
                        <div class="item-content">
                            <p class="content-text">根据准入条件、反欺诈规则以及客户行为分析等几方面，可无人工参与，自动判断，自动审核，甄别，达到“秒批”的。</p>
                        </div>
                    </div>

                    <div class="order-item">
                        <div class="icon-wrap">
                            <i class="icon icon-thunder"></i>
                        </div>
                        <div class="item-title">
                            <p class="title-text">风控</p>
                            <p class="title-text">放贷</p>
                        </div>
                        <div class="item-content">
                            <p class="content-text">结合大量第三方征信数据以及风控评估模型达到自动评分授信，实现放贷。</p>
                        </div>
                    </div>

                    <div class="order-item">
                        <div class="icon-wrap">
                            <i class="icon icon-light"></i>
                        </div>
                        <div class="item-title">
                            <p class="title-text">秒批秒贷</p>
                            <p class="title-text">到账</p>
                        </div>
                        <div class="item-content">
                            <p class="content-text">利用第三方支付手段，实现实时放款与还款，快捷、方便，完成审批。</p>
                        </div>
                    </div>

                    <div class="order-item">
                        <div class="icon-wrap">
                            <i class="icon icon-num"></i>
                        </div>
                        <div class="item-title">
                            <p class="title-text">新业务上线</p>
                            <p class="title-text">10分钟</p>
                        </div>
                        <div class="item-content">
                            <p class="content-text">贷款产品、审批流程配置，操作剪短、上手需10分钟新业务立刻上线运营。</p>
                        </div>
                    </div>

                    <div class="order-item">
                        <div class="icon-wrap">
                            <i class="icon icon-auto"></i>
                        </div>
                        <div class="item-title">
                            <p class="title-text">实施简单</p>
                            <p class="title-text">高成效</p>
                        </div>
                        <div class="item-content">
                            <p class="content-text">同时支持微信、安卓、IOS，开发支持应用环境，一劳永逸，成本。</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div id="bussiness-mode" class="tab-panel-hook bussiness-mode">
            <div class="bg-img-wrap">
                <img src="/imgs/product-page/bg_bussiness_mode.png">
            </div>
            <div class="bussiness-title item-title" class="unique">
                <h2 class="text" style="width: 122px;">业务模式</h2>
                <div class="line"></div>
                <h3 class="e-text">THE BUSINESS MODEL</h3>
            </div>
            <div class="content-img-wrap">
                <img src="/imgs/product-page/img_bussiness_mode.png" alt="">
            </div>
        </div>

        <div id="thirdPartyInterface" class="tab-panel-hook third-party-interface">
            <div class="bg-img">
                <img src="/imgs/product-page/third_party_interface_bg.png" alt="">
            </div>
            <div class="inter-face-content">
                <div class="item-title" class="unique">
                    <h2 class="text" style="width: 122px;">配套接口</h2>
                    <div class="line"></div>
                    <h3 class="e-text">THIRT-PARTY INTERFACE</h3>
                </div>
                <div class="img-wrap">
                    <img src="/imgs/product-page/third_party_img.png">
                </div>
            </div>
        </div>

        <div id="mobileTerminal" class="tab-panel-hook mobile-terminal">
            <div class="mobile-terminal-wrap">
                <div class="bg-img">
                    <img src="/imgs/product-page/mobile_terminal_bg.jpg" alt="">
                </div>
                <div class="mobile-title item-title unique">
                    <h2 class="text" style="width: 160px;">配套移动端</h2>
                    <div class="line"></div>
                    <h3 class="e-text">MOBILE TERMINAL</h3>
                </div>
                <div class="support-function-box">
                    <div class="support-item-wrap">
                        <div class="support-item">
                            <span class="icon wap"></span>
                            <p class="name">WAP网页</p>
                        </div>

                        <div class="support-item">
                            <span class="icon android"></span>
                            <p class="name">Android APP</p>
                        </div>

                        <div class="support-item">
                            <span class="icon ios"></span>
                            <p class="name">IOS APP</p>
                        </div>

                        <div class="support-item">
                            <span class="icon wechat"></span>
                            <p class="name">微信版</p>
                        </div>
                    </div>
                </div>
            </div>
            <div id="ultimateSecurity" class="site-safe">
                <img src="/imgs/product-page/web_site_img_safe.jpg" alt="">
            </div>
            <div class="site-introduction">
                <div class="introduction-wrap">
                    <div class="introduction-item">
                        <img src="/imgs/product-page/website_introduction_item_1.png" alt="">
                    </div>
                    <div class="introduction-item">
                        <img src="/imgs/product-page/website_introduction_item_2.png" alt="">
                    </div>
                    <div class="introduction-item">
                        <img src="/imgs/product-page/website_introduction_item_3.png" alt="">
                    </div>
                    <div class="introduction-item">
                        <img src="/imgs/product-page/website_introduction_item_4.png" alt="">
                    </div>
                    <div class="introduction-item">
                        <img src="/imgs/product-page/website_introduction_item_5.png" alt="">
                    </div>
                </div>
            </div>
        </div>

        <div id="functional-list" class="tab-panel-hook functional-list">
            <div class="item-title" :class="{unique: flag}">
                <h2 class="text" style="width: 200px;">功能清单</h2>
                <div class="line"></div>
                <h3 class="e-text">FUNCTION LIST</h3>
            </div>
            <div class="function-table text-center" style="padding-top: 62px;">
                <img width="1120" height="1180" src="imgs/product-page/functional-list.jpg">
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
  $(document).ready(function () {
    // Bind click events to every nav link, when click, scroll to corresponding tab-panel
    $('#nav-box .nav tr>td').click(function() {
      var selectedNavIndex = $('#nav-box .nav tr>td').index(this)
      var $selectedTabPanel = $('.content-container>.tab-panel-hook:eq(' + selectedNavIndex + ')')
      if (!$selectedTabPanel.length) return

      $('body,html').animate({ scrollTop: $selectedTabPanel.offset().top - 60 }, 200)
    })

    // Bind scroll event to window, when scroll, fix the nav-box and select corresponding nav link
    var DT = $('#nav-box').offset().top;
    $(window).scroll(function() {
      var wt = $(window).scrollTop();
      var l = $('.content-container>.tab-panel-hook');
      var s = l.length - 1;
      if (wt < DT || wt >= l.last().offset().top + l.last().height() + 70) {
        $('#nav-box').removeClass('fixed');
        $('#nav-bar').show();
        $('#nav-box .nav tr>td:first').addClass('active').siblings().removeClass('active');
      } else {
        $('#nav-box').addClass('fixed');
        $('#nav-bar').hide();
        for (var i = 0; i < s; i++) {
          if (wt >= parseInt(l.eq(i).offset().top - 70) && wt < parseInt(l.eq(i + 1).offset().top - 70)) {
            s = i;
            break;
          }
        }
        $('#nav-box .nav tr>td:eq(' + s + ')').addClass('active').siblings().removeClass('active');
      }
    });
  })
</script>