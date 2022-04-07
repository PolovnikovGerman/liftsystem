<?php $i = 0;?>
<?php foreach ($vendors as $vendor) { ?>
    <div class="datarow <?=($i%2==0 ? 'whitedatarow' : 'greydatarow')?> <?=$vendor['vendor_status']==1 ? '' : 'nonactive'?>" data-vendor="<?=$vendor['vendor_id']?>" >
        <div class="status">
            <?=$vendor['vendor_status']==1 ? 'Active' : 'Inactive'?>
        </div>
        <div class="type"><?=$vendor['vendor_type']?></div>
        <div class="slug"><?=$vendor['vendor_slug']?></div>
        <div class="name"><?=$vendor['vendor_name']?></div>
        <div class="altname"><?=$vendor['alt_name']?></div>
        <div class="asinumber"><?=empty($vendor['vendor_asinumber']) ? '-' : $vendor['vendor_asinumber']?></div>
        <div class="website <?=empty($vendor['vendor_website']) ? '' : 'vendorwebsiteshow'?>" data-weburl="<?=$vendor['vendor_website']?>">
            <?=$vendor['vendor_website']?>
        </div>
        <div class="phone"><?=$vendor['vendor_phone']?></div>
        <div class="itemqty"><?=intval($vendor['item_qty'])==0 ? '' : QTYOutput($vendor['item_qty'])?></div>
    </div>
    <?php $i++;?>
<?php } ?>
