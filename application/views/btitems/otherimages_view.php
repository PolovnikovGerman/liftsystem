<div class="otherimagessrcarea">
    <?php $numpp=0;?>
    <?php foreach ($images as $image) { ?>
        <?php if ($numpp%5==0) { ?>
            <div class="content-row">
        <?php } ?>
        <div class="otherimagesrc">
            <img class="img-responsive" src="<?=$image['item_img_name']?>" alt="Img"/>
        </div>
        <?php $numpp++;?>
        <?php if ($numpp%5==0) { ?>
            </div>
            <?php if ($numpp==10) { ?>
                <?php break;?>
            <?php } ?>
        <?php } ?>
    <?php } ?>
    <?php if ($imgcnt > 0 && $numpp%5!==0) { ?>
        </div>
    <?php } ?>
</div>
<?php if ($imgcnt > 10) { ?>
    <div class="otherimagesmore">
        + <?=($imgcnt - 10)?> more
    </div>
<?php } ?>
