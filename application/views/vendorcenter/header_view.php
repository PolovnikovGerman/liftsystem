<input type="hidden" id="session" value="<?=$session?>"/>
<input type="hidden" id="vendorid" value="<?=$vendor['vendor_id']?>"/>
<div class="content-row">
    <div class="vendordetails-header">
        <div class="vendorstatusarea <?=$vendor['vendor_status']==1 ? 'active' : 'inactive' ?>">
            <div class="vendorstatustxt"><?=$vendor['vendor_status']==1 ? 'Active' : 'Inactive'?></div>
            <div class="vendorstatuslabel">Vendor</div>
        </div>
        <div class="vendornameview <?=$vendor['vendor_status']==1 ? 'active' : 'inactive' ?>">
            <?=empty($vendor['vendor_name']) ? '&nbsp;' : $vendor['vendor_name']?>
        </div>
        <div class="vendorslugview <?=$vendor['vendor_status']==1 ? 'active' : 'inactive' ?>">
            <?=empty($vendor['vendor_slug']) ? '&nbsp;' : $vendor['vendor_slug']?>
        </div>
        <div class="vendoreditarea">
            <div class="managemodelabel"><?=$editmode==0 ? 'View mode' : 'Edit mode'?></div>
            <?php if ($editmode==0) { ?>
                <div class="vendoractivatetbtn">Edit</div>
            <?php } else { ?>
                <div class="vendorsaveactionbtn">Save</div>
            <?php }?>
        </div>
    </div>
</div>
