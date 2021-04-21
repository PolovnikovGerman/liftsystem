<input type="hidden" id="dbdetailid" value="<?=$item['item_id']?>"/>
<input type="hidden" id="dbdetailsession" value="<?=$session_id?>"/>
<input type="hidden" id="dbdetailbrand" value="<?=$item['brand']?>"/>
<div class="content-row">
    <div class="itemlistdetails-header">
        <div class="itemlistdetails-left-header">
            <?php if ($editmode==0) { ?>
                <div class="itemstatusview <?=$item['item_active']==1 ? 'active' : 'inactive'?>"><?=$item['item_active']==1 ? 'Active' : 'Inactive'?></div>
            <?php } else { ?>
                <div class="itemstatusbtn <?=$item['item_active']==1 ? 'active' : 'inactive'?>"><?=$item['item_active']==1 ? 'Active' : 'Inactive'?></div>
            <?php } ?>
            <div class="itemnumberview">
                <?php if ($editmode==0) { ?>
                    <?=$item['item_number']?>
                <?php } else { ?>
                    <input type="text" class="itemlistdetailsinpt itemnumber" data-item="item_number" value="<?=$item['item_number']?>"/>
                <?php } ?>
            </div>
            <div class="<?=$editmode==0 ? 'itemnameview' : 'itemnameedit'?>">
                <?php if ($editmode==0) { ?>
                    <?=$item['item_name']?>
                <?php } else { ?>
                    <input type="text" class="itemlistdetailsinpt itemname" data-item="item_name" value="<?=$item['item_name']?>"/>
                <?php } ?>
            </div>
        </div>
        <div class="itemlistdetails-right-header">
            <div class="itemlistmodeview"><?=$editmode==0 ? 'View mode' : 'Edit mode'?></div>
            <?php if ($editmode==0) { ?>
                <div class="itemlistactivatetbtn">Activate</div>
            <?php } else { ?>
                <div class="itemlistsaveactionbtn">Save</div>
            <?php }?>
        </div>
    </div>
</div>