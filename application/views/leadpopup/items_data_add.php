<?php $nrow=0;?>
<?php foreach ($items as $row) { ?>
    <div class="quoteitemtabledatarow <?=($nrow%2==0 ? 'whitedatarow' : 'greydatarow')?>">
        <div class="quoteitems_content_add">
            <select class="addnewquoteitem" data-quoteitem="<?=$quote_item_id?>">
                <option value="">Enter &amp; Select Item</option>
                <?php foreach ($itemslist as $list) { ?>
                    <option value="<?=$list['item_id']?>"><?=$list['itemnumber']?> &ndash; <?=$list['itemname']?></option>
                <?php } ?>
            </select>
        </div>
        <div class="quoteitemcolor_adddata">&nbsp;</div>
        <div class="quoteitems_content_addqty">&nbsp;</div>
        <div class="quoteitems_content_addprice">&nbsp;</div>
        <div class="quoteitemrowsubtotal newitem" data-quoteitem="<?= $quote_item_id ?>">
            <div class="quoteitems_content_addprint" data-quoteitem="<?=$row['quote_item_id']?>">Print Details</div>
        </div>
        <div class="items_content_cancel" data-quoteitem="<?=$row['quote_item_id']?>"><i class="fa fa-trash"></i></div>
    </div>
    <?php $nrow++;?>
<?php } ?>
<?=$imprintview?>
