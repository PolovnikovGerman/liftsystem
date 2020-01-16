<form id="sortitemsequence">
    <ul id="dbitemsortable" class="itemsortable">
        <?php foreach ($items as $row) { ?>
            <li class="sequenceitem_data display_<?=$itemperrow?>">
                <input type="hidden" id="item_<?=$row['item_id']?>" name="item_<?=$row['item_id']?>" value="<?=$row['item_id']?>" />
                <div class="sequenceitem_details">
                    <div class="seqtitle"><?=$row['item_sequence']?></div>
                    <div class="itemseqmove">
                        <span>Move to</span>
                        <input type="text" class="moveitemseq" data-item="<?=$row['item_id']?>">
                    </div>
                </div>
                <div class="itemseqimage">
                    <img src="<?=$row['item_image']?>" alt="<?=$row['item_name']?>" />
                    <div class="itemnumber"><?=$row['item_number']?></div>
                </div>
                <div class="sequenceitem_details">
                    <div class="itemname"><?=$row['item_name']?></div>
                </div>
                <div class="sequenceitem_details">
                    <div class="saleitemmark">
                        <input type="checkbox" class="salechkbox" data-item="<?=$row['item_id']?>" <?=$row['item_sale']==1 ? 'checked="checked"' : ''?>/>
                        <span>Sale</span>
                    </div>
                    <div class="newitemmark">
                        <input type="checkbox" class="newitemchkbox" data-item="<?=$row['item_id']?>" <?=$row['item_new']==1 ? 'checked="checked"' : ''?>/>
                        <span>New </span>
                    </div>
                </div>
            </li>
        <?php } ?>
    </ul>
</form>