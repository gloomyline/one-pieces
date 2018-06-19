<!-- component NavBar -->
<div id="nav-bar">
    <div class="container">
        <div class="nav-title row">
            <div class="title-left col-lg-8">
                <div class="logo-wrap">
                    <img src="/imgs/navbar/logo.png" width="200" height="50">
                </div>
                <div class="left-content">
                    <h3 class="company-name">厦门万匹思网络科技有限公司</h3>
                    <div class="company-desc" style="padding-top: 20px">
                        <!--<img src="/imgs/navbar/sub-title.png" width="353" height="20">-->
                    </div>
                </div>
            </div>
            <div class="title-right col-lg-4">
                <div class="icon-wrap">
                    <img class="icon-phone" src="/imgs/navbar/icon-phone.png" width="50" height="50">
                </div>
                <div class="right-content">
                    <p class="phone-number">400-178-9698</p>
                    <p class="email">idk@163.com</p>
                </div>
            </div>
        </div>
    </div>
    <div class="nav-wrap">
        <div class="container">
            <ul class="nav text-center">
                <?php foreach($nav as $v): ?>
                    <li class="nav-item" id="<?= sprintf('link-%s',$v['serial'])?>"><a class="nav-link
                      <?php
                        if ($v['link'] != '/' && $v['link'] != '' && $v['link'] != '#') {
                            $reg = "|{$v['link']}*|i"; echo preg_match($reg, sprintf('/%s', $current)) ? 'active' : '';
                        }
                      ?>" <?=$v['is_open'] == 1? 'onclick="javascript:window.open(\''. $v['link'] .'\')"' : 'onclick="javascript:window.location.href=\''. $v['link'] .'\'"' ?>><?= $v['name']?></a></li>
                <?php endforeach; ?>
            </ul>
            <?php foreach($nav as $v): ?>
                <div id="<?=sprintf('dropdown-list-%s', $v['serial'])?>">
                    <div class="list-container container">
                        <?php foreach($v['children'] as $second): ?>
                            <ul class="consumer-finance  text-center">
                                <li class="consumer-finance-item title" <?=$second['is_open'] == 1? 'onclick="javascript:window.open(\''. $second['link'] .'\')"' : 'onclick="javascript:window.location.href=\''. $second['link'] .'\'"' ?>><?=$second['name']?></li>
                                <?php foreach($second['children'] as $third): ?>
                                    <li class="consumer-finance-item"><a <?=$third['is_open'] == 1? 'target="_blank"': ''?> href="<?=$third['link']?>"><?=$third['name']?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>