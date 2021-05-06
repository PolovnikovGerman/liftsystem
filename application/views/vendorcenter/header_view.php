<input type="hidden" id="session" value="<?=$session?>"/>
<input type="hidden" id="vendorid" value="<?=$vendor['vendor_id']?>"
<div class="content-row">
    <div class="vendordetails-header">
        <div class="vendordetails-left-header">
            <?php if ($editmode==0) { ?>
                <div class="vendorstatusview <?=$vendor['vendor_status']==1 ? 'active' : 'inactive'?>"><?=$vendor['vendor_status']==1 ? 'Active' : 'Inactive'?></div>
            <?php } else { ?>
                <div class="vendorstatusbtn <?=$vendor['vendor_status']==1 ? 'active' : 'inactive'?>"><?=$vendor['vendor_status']==1 ? 'Active' : 'Inactive'?></div>
            <?php } ?>
            <div class="vendornumberview">
                <?php if ($editmode==0) { ?>
                    <?=empty($vendor['vendor_slug']) ? '&nbsp;' : $vendor['vendor_slug']?>
                <?php } else { ?>
                    <input type="text" class="vendordetailsinpt slug" data-item="vendor_slug" value="<?=$vendor['vendor_slug']?>"/>
                <?php } ?>
            </div>
            <div class="<?=$editmode==0 ? 'vendornameview' : 'vendornameedit'?>">
                <?php if ($editmode==0) { ?>
                    <?=empty($vendor['vendor_name']) ? '&nbsp;' : $vendor['vendor_name']?>
                <?php } else { ?>
                    <input type="text" class="vendordetailsinpt vendorname" data-item="vendor_name" value="<?=$vendor['vendor_name']?>"/>
                <?php } ?>
            </div>
        </div>
        <div class="vendordetails-right-header">
            <div class="vendormodeview"><?=$editmode==0 ? 'View mode' : 'Edit mode'?></div>
            <?php if ($editmode==0) { ?>
                <div class="vendoractivatetbtn">Activate</div>
            <?php } else { ?>
                <div class="vendorsaveactionbtn">Save</div>
            <?php }?>
        </div>
    </div>
</div>
