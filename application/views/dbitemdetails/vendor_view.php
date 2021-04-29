<div class="itemdetails-vendorinfo">
    <div class="content-row">
        <div class="chapterlabel centerpart">Vendor Info:</div>
    </div>
    <div class="content-row">
        <div class="leadstime-label">
            <div class="lead-label">Stand.</div>
            <div class="lead-label">Rush</div>
            <div class="lead-label" style="margin-top: 0px;">Super Rush</div>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorinfo-label">Vendor:</div>
        <div class="vendorinfo-value vendorname">
            <?php if ($editmode==0) { ?>
                <div class="viewparam"><?=$vendor_item['vendor_name']?></div>
            <?php } else { ?>
                <input type="text" class="vendornameinpt vendorname" id="vendor_name" data-item="vendor_name" value="<?=$vendor_item['vendor_name']?>">
            <?php } ?>
        </div>
        <div class="vendorinfo-label">Ships From Zip:</div>
        <div class="vendorinfo-value vendorzip">
            <?php if ($editmode==0) { ?>
                <div class="viewparam"><?=($vendor_item['vendor_item_zipcode'] ? $vendor_item['vendor_item_zipcode'] : $vendor_item['vendor_zipcode'])?></div>
            <?php } else { ?>
                <input type="text" class="vendordatainpt vendorzip" data-item="vendor_item_zipcode" value="<?=($vendor_item['vendor_item_zipcode'] ? $vendor_item['vendor_item_zipcode'] : $vendor_item['vendor_zipcode'])?>">
            <?php } ?>
        </div>
        <div class="vendorinfo-label">Lead Times:</div>
        <div class="vendorinfo-value leadtimes">
            <?php if ($editmode==0) { ?>
                <div class="viewparam"><?=$item['item_lead_a']?></div>
            <?php } else { ?>
                <input type="text" class="itemlistdetailsinpt leadtimes" data-item="item_lead_a" value="<?=$item['item_lead_a']?>"/>
            <?php } ?>
        </div>
        <div class="vendorinfo-value leadtimes">
            <?php if ($editmode==0) { ?>
                <div class="viewparam"><?=$item['item_lead_b']?></div>
            <?php } else { ?>
                <input type="text" class="itemlistdetailsinpt leadtimes" data-item="item_lead_b" value="<?=$item['item_lead_b']?>"/>
            <?php } ?>
        </div>
        <div class="vendorinfo-value leadtimes">
            <?php if ($editmode==0) { ?>
                <div class="viewparam"><?=$item['item_lead_c']?></div>
            <?php } else { ?>
                <input type="text" class="itemlistdetailsinpt leadtimes" data-item="item_lead_c" value="<?=$item['item_lead_c']?>"/>
            <?php } ?>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorinfo-label">Vendor Item #:</div>
        <div class="vendorinfo-value vendoritemnum">
            <?php if ($editmode==0) { ?>
                <div class="viewparam"><?=$vendor_item['vendor_item_number']?></div>
            <?php } else { ?>
                <input type="text" class="vendornameinpt vendoritemnum" id="vendor_item_number" data-item="vendor_item_number" value="<?=$vendor_item['vendor_item_number']?>">
            <?php } ?>
        </div>
        <div class="vendorinfo-label">Vend Item Name:</div>
        <div class="vendorinfo-value vendoritemname">
            <?php if ($editmode==0) { ?>
                <div class="viewparam"><?=$vendor_item['vendor_item_name']?></div>
            <?php } else { ?>
                <input type="text" class="vendordatainpt vendoritemname" data-item="vendor_item_name" value="<?=$vendor_item['vendor_item_name']?>">
            <?php } ?>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorinfo-label">Note on PO:</div>
        <div class="vendorinfo-value vendoritemnote">
            <?php if ($editmode==0) { ?>
                <div class="viewparam"><?=$vendor_item['vendor_item_notes']?></div>
            <?php } else { ?>
                <textarea type="text" class="vendordatainpt vendoritemnote" data-item="vendor_item_notes"><?=$vendor_item['vendor_item_notes']?></textarea>
            <?php } ?>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorinfo-label">Qty Breaks:</div>
        <?php foreach ($vendor_price as $price) { ?>
            <div class="vendorinfo-value vendoritemqty">
                <?php if ($editmode==0) { ?>
                    <div class="viewparam"><?=empty($price['vendorprice_qty']) ? '&nbsp;' : $price['vendorprice_qty']?></div>
                <?php } else { ?>
                    <input type="text" class="vendorpriceinpt vendorprice_qty" data-item="vendorprice_qty" data-idx="<?=$price['vendorprice_id']?>" value="<?=$price['vendorprice_qty']?>">
                <?php } ?>
            </div>
        <?php } ?>
        <div class="vendorinfo-label">Prints</div>
        <div class="vendorinfo-label">Setup</div>
    </div>
    <div class="content-row">
        <div class="price_subtitle">min</div>
    </div>
    <div class="content-row">
        <div class="vendorinfo-label colorprice">1 Color:</div>
        <?php foreach ($vendor_price as $price) { ?>
            <div class="vendorinfo-value vendoritemqty">
                <?php if ($editmode==0) { ?>
                    <div class="viewparam"><?=empty($price['vendorprice_color']) ? '&nbsp;' : MoneyOutput($price['vendorprice_color'])?></div>
                <?php } else { ?>
                    <input type="text" class="vendorpriceinpt vendorprice_qty" data-item="vendorprice_color" data-idx="<?=$price['vendorprice_id']?>" value="<?=$price['vendorprice_color']?>">
                <?php } ?>
            </div>
        <?php } ?>
        <div class="vendorinfo-value vendoritemspecqty">
            <?php if ($editmode==0) { ?>
                <div class="viewparam"><?=empty($vendor_item['vendor_item_exprint']) ? '&nbsp;' : MoneyOutput($vendor_item['vendor_item_exprint'])?></div>
            <?php } else { ?>
                <input type="text" class="vendorpriceinpt vendorprice_special" data-idx="<?=$item['item_price_id']?>" data-item="vendor_item_exprint" value="<?=$vendor_item['vendor_item_exprint']?>">
            <?php } ?>
        </div>
        <div class="vendorinfo-value vendoritemspecqty">
            <?php if ($editmode==0) { ?>
                <div class="viewparam"><?=empty($vendor_item['vendor_item_setup']) ? '&nbsp;' : MoneyOutput($vendor_item['vendor_item_setup'])?></div>
            <?php } else { ?>
                <input type="text" class="vendorpriceinpt vendorprice_special" data-idx="<?=$item['item_price_id']?>" data-item="vendor_item_setup" value="<?=$vendor_item['vendor_item_setup']?>">
            <?php } ?>
        </div>
    </div>
</div>
