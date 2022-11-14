<?php $nrow=0;?>
<?php foreach ($data as $row) { ?>
<div class="datarow <?=($nrow%2 ? 'whitedatarow' : 'greydatarow')?>" data-category="<?=$row['netprofit_category_id']?>">
    <div class="deedcell" data-category="<?=$row['netprofit_category_id']?>">
        <i class="fa fa-pencil" aria-hidden="true"></i>
    </div>
    <div class="category_name"><?=$row['category_name']?></div>
    <?php $nrow++;?>
</div>
<?php } ?>