<?php $nrow=0;?>
<?php foreach ($vendors as $vendor) {?>
    <div class="vendorsdatarow <?=($nrow%2==0 ? 'whitedatarow' : 'greydatarow')?>">
        <div class="vendrecmanage deletevend" data-vendor="<?=$vendor['vendor_id']?>">
            <i class="fa fa-trash-o" aria-hidden="true"></i>
        </div>
        <div class="vendrecmanage editvendor" data-vendor="<?=$vendor['vendor_id']?>">
            <i class="fa fa-pencil" aria-hidden="true"></i>
        </div>
        <div class="vendorname"><?=$vendor['vendor_name']?></div>
        <div class="vendorinc">
            <input type="checkbox" class="vendpayincl" value="1" data-vendorid="<?=$vendor['vendor_id']?>" <?=($vendor['payinclude']==1 ? 'checked="checked"' : '')?>/>
        </div>
        <div class="vendzipcode"><?=$vendor['vendor_zipcode']?></div>
        <div class="vendcalendar"><?=$vendor['calendar_name']?></div>
    </div>
    <?php $nrow++;?>
<?php } ?>

