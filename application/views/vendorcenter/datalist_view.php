<?php $i = 0;?>
<?php foreach ($vendors as $vendor) { ?>
    <div class="datarow <?=($i%2==0 ? 'whitedatarow' : 'greydatarow')?>">
        <div class="editdata" data-vendor="<?=$vendor['vendor_id']?>">
            <i class="fa fa-pencil" aria-hidden="true"></i>
        </div>
        <div class="numpp"><?=$vendor['numpp']?></div>
        <div class="type"><?=$vendor['vendor_type']?></div>
        <div class="slug"><?=$vendor['vendor_slug']?></div>
        <div class="name"><?=$vendor['vendor_name']?></div>
        <div class="country"><?=$vendor['country_name']?></div>
        <div class="website"><?=$vendor['vendor_website']?></div>
        <div class="ouraccount"><?=$vendor['our_account_number']?></div>
        <div class="contact"><?=$vendor['contact_name']?></div>
        <div class="phone"><?=$vendor['contact_phone']?></div>
        <div class="email"><?=$vendor['contact_email']?></div>
        <div class="itemqty"><?=intval($vendor['item_qty'])==0 ? '' : QTYOutput($vendor['item_qty'])?></div>
    </div>
    <?php $i++;?>
<?php } ?>
