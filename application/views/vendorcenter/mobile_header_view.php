<div class="row">
    <div class="col-12 text-center">
        <div class="mob-vendornameview"><?=empty($vendor['vendor_name']) ? '&nbsp;' : $vendor['vendor_name']?></div>
        <div class="mob-vendorslugview"><?=empty($vendor['vendor_slug']) ? '&nbsp;' : $vendor['vendor_slug']?></div>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-5 pl-0 pr-0 mob-vendorstatusarea">
        <div class="mob-vendorstatustxt"><?=$vendor['vendor_status']==1 ? 'Active' : 'Inactive'?></div>
        <div class="mob-vendorstatuslabel">Vendor</div>
    </div>
    <div class="mob-vendorarea">&nbsp;</div>
    <div class="col-5 pl-0 pr-0 text-center mob-vendoreditarea">
        <div class="mob-managemodelabel">View<br><span style="margin-left: 15px;">mode<span></div>
        <div class="mob-vendoractivatetbtn">&nbsp;</div>
    </div>
</div>
