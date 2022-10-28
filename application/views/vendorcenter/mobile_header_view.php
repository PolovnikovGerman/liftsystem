<div class="row">
    <div class="col-12 text-center">
        <div class="mob-vendornameview"><?=empty($vendor['vendor_name']) ? '&nbsp;' : $vendor['vendor_name']?></div>
        <div class="mob-vendorslugview"><?=empty($vendor['vendor_slug']) ? '&nbsp;' : $vendor['vendor_slug']?></div>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-5 pl-0 pr-0 mob-vendorstatusarea <?=$vendor['vendor_status']==1 ? '' : 'non-active'?>">
        <div class="mob-vendorstatustxt"><?=$vendor['vendor_status']==1 ? 'Active' : 'Inactive'?></div>
        <div class="mob-vendorstatuslabel">Vendor</div>
    </div>
    <div class="mob-vendorarea <?=$vendor['vendor_status']==1 ? '' : 'non-active'?>">&nbsp;</div>
    <div class="col-5 pl-0 pr-0 text-center mob-vendoreditarea">
        <div class="mob-managemodelabel"><?=$editmode==1 ? 'Edit' : 'View'?><br>
            <span style="margin-left: 15px;">mode<span></div>
        <?php if ($editmode==0) { ?>
            <div class="mob-vendoractivatetbtn">&nbsp;</div>
        <?php } else { ?>
            <div class="mob-vendorsaveactionbtn">&nbsp;</div>
        <?php } ?>

    </div>
</div>
