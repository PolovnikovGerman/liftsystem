<div class="multitrackdatabody <?=$completed==1 ? 'completed' : ''?>">
    <?php $numpp = 1;?>
    <?php foreach ($trackings as $tracking) { ?>
        <div class="trackdatarow editmode <?=$completed==1 ? 'completed' : ''?>" data-track="<?=$tracking['tracking_id']?>">
            <input type="hidden" class="trackcodehidden" data-track="<?=$tracking['tracking_id']?>" data-orderitem="<?=$order_item?>" data-color="<?=$item_color?>" value="<?=$tracking['trackcode']?>"/>
            <div class="trackqty editmode">
                <input type="text" class="trackqtyinpt" data-track="<?=$tracking['tracking_id']?>" data-orderitem="<?=$order_item?>" data-color="<?=$item_color?>" value="<?=$tracking['qty']?>"/>
            </div>
            <div class="trackdate editmode">
                <input type="text" class="trackdateinpt" data-track="<?=$tracking['tracking_id']?>" data-orderitem="<?=$order_item?>" data-color="<?=$item_color?>" value="<?=empty($tracking['trackdate']) ? date('m/d/Y') : date('m/d/Y', $tracking['trackdate'])?>"/>
            </div>
            <div class="trackservice editmode">
                <select class="trackserviceinpt" data-track="<?=$tracking['tracking_id']?>" data-orderitem="<?=$order_item?>" data-color="<?=$item_color?>">
                    <option value=""></option>
                    <option value="UPS" <?=$tracking['trackservice']=='UPS' ? 'selected="selected"' : ''?>>UPS</option>
                    <option value="FedEx" <?=$tracking['trackservice']=='FedEx' ? 'selected="selected"' : ''?>>FedEx</option>
                    <option value="DHL" <?=$tracking['trackservice']=='DHL' ? 'selected="selected"' : ''?>>DHL</option>
                    <option value="USPS" <?=$tracking['trackservice']=='USPS' ? 'selected="selected"' : ''?>>USPS</option>
                    <option value="Van" <?=$tracking['trackservice']=='Van' ? 'selected="selected"' : ''?>>Van</option>
                    <option value="Pickup" <?=$tracking['trackservice']=='Pickup' ? 'selected="selected"' : ''?>>Pickup</option>
                    <option value="Courier" <?=$tracking['trackservice']=='Courier' ? 'selected="selected"' : ''?>>Courier</option>
                    <option value="Other" <?=$tracking['trackservice']=='Other' ? 'selected="selected"' : ''?>>Other</option>
                </select>
            </div>
            <div class="trackcode editmode">
                <input type="text" class="trackcodeinpt" data-track="<?=$tracking['tracking_id']?>" data-orderitem="<?=$order_item?>" data-color="<?=$item_color?>" value="<?=$tracking['trackcode']?>"/>
            </div>
            <div class="trackcodecopy" data-track="<?=$tracking['tracking_id']?>" data-orderitem="<?=$order_item?>" data-color="<?=$item_color?>">
                <i class="fa fa-copy"></i>
            </div>
            <?php if ($numpp > 1) { ?>
                <div class="trackcoderemove" data-track="<?=$tracking['tracking_id']?>" data-orderitem="<?=$order_item?>" data-color="<?=$item_color?>">
                    <i class="fa fa-trash-o"></i>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
    <div class="trackdatarow editmode">
        <span class="addnewtrack" data-orderitem="<?=$order_item?>" data-color="<?=$item_color?>">[add new]</span>
    </div>
</div>