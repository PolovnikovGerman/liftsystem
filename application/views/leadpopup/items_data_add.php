<?php $nrow=0;?>
<div class="quoteitemtabledatarow" data-quoteitem="<?=$quote_item_id?>">
    <?php foreach ($items as $row) { ?>
        <div class="items_table_line bord_b <?=($nrow%2==0 ? '' : 'items_line_gray')?>">
            <div class="quoteitems_content_add">
                <select class="addnewquoteitem" data-quoteitem="<?=$quote_item_id?>">
                    <option value="">Enter &amp; Select Item</option>
                    <?php foreach ($itemslist as $list) { ?>
                        <option value="<?=$list['item_id']?>"><?=$list['itemnumber']?> &ndash; <?=$list['itemname']?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="quotecolor_adddata">&nbsp;</div>
            <div class="quote_content_addqty">&nbsp;</div>
            <div class="quote_content_addprice">&nbsp;</div>
            <div class="items_content_sub_total2 newquotecoloradd" data-item="<?=$row['item_id']?>" data-quoteitem="<?=$quote_item_id?>">
                <div class="quote_content_addprint" data-quoteitem="<?=$quote_item_id?>">Print Details</div>
            </div>
            <div class="quote_content_cancel" data-quoteitem="<?=$quote_item_id?>"><i class="fa fa-trash"></i></div>
        </div>
        <?php $nrow++;?>
    <?php } ?>
</div>