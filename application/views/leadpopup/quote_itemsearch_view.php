<div class="quote_itemedit_area">
    <div class="quote_itemedit_title">Item</div>
    <div class="quote_itemedit">
        <select class="selectquoteitem" id="quoteitem_id">
            <option value="">Enter & Select Item</option>
            <?php foreach ($items_list as $row) { ?>
                <option value="<?=$row['item_id']?>" <?=($row['item_id']==$item_id ? 'selected="selected"' : '' )?>><?=$row['itemnumber']?> &mdash; <?=$row['itemname']?></option>
            <?php } ?>
        </select>
    </div>
    <div class="quote_itemedit_text">
        <label><?=$quote_item_label?></label>
        <textarea class="quoteitemsvalue"><?=$quote_items?></textarea>
    </div>
    <div class="quote_itemedit_save" data-quote="<?=$quote_id?>">
        <img src="/img/leadorder/saveticket.png" alt="Save"/>
    </div>
</div>