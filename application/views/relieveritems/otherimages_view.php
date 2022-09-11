<div class="otherimagessrcarea">
    <?php $numpp=1;?>
    <?php foreach ($images as $image) { ?>
    <?php if ($numpp==1) { ?>
        <div class="content-row">
    <?php } ?>
        <div class="otherimagesrc">
            <img class="img-responsive" src="<?=$image['item_img_name']?>" alt="Img"/>
        </div>
        <?php $numpp++;?>
        <?php if ($numpp==6) { ?>
            </div>
            <div class="content-row">
        <?php } ?>
        <?php if ($numpp==11) { ?>
            </div>
            <?php break;?>
        <?php } ?>
    <?php } ?>
    <?php if ($numpp%6!==0 && $numpp < 11) { ?>
        </div>
    <?php } ?>
</div>
<?php if ($imgcnt > 10) { ?>
    <div class="otherimagesmore">
        + <?=($imgcnt - 10)?> more
    </div>
<?php } ?>
