<!-- component Banner -->
<div class="banner-wrap">
    <div id="banner" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            <?php  foreach ($banner as $v): ?>
                <li data-target="#banner" data-slide-to="<?= $v['serial'] ?>" class="<?php echo $v['serial'] == 0 ? 'active' : ''?>"></li>
            <?php endforeach;  ?>
        </ol>
        <div class="carousel-inner">
            <?php  foreach ($banner as $v): ?>
                <div class="carousel-item <?php echo $v['serial'] == 0 ? 'active' : ''?>">
                    <img height="800px" class="d-block w-100" src="<?= $v['image'] ?>" alt="<?= $v['name'] ?>">
                </div>
            <?php endforeach;  ?>
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