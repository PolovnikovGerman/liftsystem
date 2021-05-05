<input type="hidden" id="session" value="<?=$session?>"/>
<input type="hidden" id="vendorid" value="<?=$vendor['vendor_id']?>"
<div class="content-row">
    <div class="details-header">
        <div class="details-left-header">
            <?php if ($editmode==0) { ?>
                <div class="statusview <?=$vendor['vendor_status']==1 ? 'active' : 'inactive'?>"><?=$vendor['vendor_status']==1 ? 'Active' : 'Inactive'?></div>
            <?php } else { ?>
                <div class="statusbtn <?=$vendor['vendor_status']==1 ? 'active' : 'inactive'?>"><?=$vendor['vendor_status']==1 ? 'Active' : 'Inactive'?></div>
            <?php } ?>
            <div class="numberview">
                <?php if ($editmode==0) { ?>
                    <?=$vendor['vendor_slug']?>
                <?php } else { ?>
                    <input type="text" class="detailsinpt slug" data-item="vendor_slug" value="<?=$vendor['vendor_slug']?>"/>
                <?php } ?>
            </div>
            <div class="<?=$editmode==0 ? 'nameview' : 'nameedit'?>">
                <?php if ($editmode==0) { ?>
                    <?=$vendor['vendor_name']?>
                <?php } else { ?>
                    <input type="text" class="detailsinpt vendorname" data-item="vendor_name" value="<?=$vendor['vendor_name']?>"/>
                <?php } ?>
            </div>
        </div>
        <div class="details-right-header">
            <div class="modeview"><?=$editmode==0 ? 'View mode' : 'Edit mode'?></div>
            <?php if ($editmode==0) { ?>
                <div class="activatetbtn">Activate</div>
            <?php } else { ?>
                <div class="saveactionbtn">Save</div>
            <?php }?>
        </div>
    </div>
</div>
