<div class="advimagearea">
    <?php if (empty($item['printlocat_example_img'])) { ?>
        <div class="emptyimage edit">
            <div class="newadvimage"  id="newadvimage">&nbsp;</div>
        </div>
    <?php } else { ?>
        <div class="deladvimage"><i class="fa fa-trash-o" aria-hidden="true"></i></div>
        <div class="image">
            <img src="<?=$item['printlocat_example_img']?>" alt="'Advertiser Example"/>
        </div>
    <?php } ?>
</div>
