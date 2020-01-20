<div id="vendorpricesidx">
    <?php // foreach ($vendprice as $row) {?>
    <input type="hidden" id="vendorpriceidx" name="vendorpriceidx" value="<?=$vendor['vendorpriceidx']?>"/>
    <?php // } ?>
</div>
<div class="vendprices_qty">
    <div class="vendpricelabel">&nbsp;</div>
    <div class="vendorprice_qty">min</div>
    <?php foreach ($vendprice as $row) {?>
        <div class="vendorprice_qty">
            <input type="hidden" id="old_vendorpriceqty<?=$row['vendorprice_id']?>" value="<?=$row['vendorprice_qty']?>"/>
            <input type="text" id="vendorprice_qty<?=$row['vendorprice_id']?>" name="vendorprice_qty<?=$row['vendorprice_id']?>" class="vendorprice_qtyinp" value="<?=$row['vendorprice_qty']?>"/>
        </div>
    <?php } ?>
    <div class="vendorprice_qty prints">Prints</div>
    <div class="vendorprice_qty">Setup</div>
</div>
<div class="vendprices_price">
    <div class="vendpricelabel">Blank</div>
    <div class="vendorprice_price">
        <input type="hidden" id="old_vendor_item_blankcost" value="<?=$vendor['vendor_item_blankcost']?>"/>
        <input type="text" id="vendor_item_blankcost" name="vendor_item_blankcost" class="vendpriceblankval" value="<?=$vendor['vendor_item_blankcost']?>"/>
    </div>
    <?php foreach ($vendprice as $row) {?>
        <div class="vendorprice_price">
            <input type="hidden" id="old_vendorprice_val<?=$row['vendorprice_id']?>" value="<?=$row['vendorprice_val']?>"/>
            <input type="text" id="vendorprice_val<?=$row['vendorprice_id']?>" name="vendorprice_val<?=$row['vendorprice_id']?>" class="vendpriceblankval" value="<?=$row['vendorprice_val']?>"/>
        </div>
    <?php } ?>
    <div class="vendorprice_price prints">&nbsp;</div>
    <div class="vendorprice_price">&nbsp;</div>
</div>
<div class="vendprices_price_color">
    <div class="vendpricelabel">1 Color</div>
    <div class="vendorprice_price">
        <input type="hidden" id="old_vendor_item_cost" value="<?=$vendor['vendor_item_cost']?>"/>
        <input type="text" name="vendor_item_cost" id="vendor_item_cost" class="vendpricecolorval" value="<?=$vendor['vendor_item_cost']?>"/>
    </div>
    <?php foreach ($vendprice as $row) {?>
        <div class="vendorprice_price">
            <input type="hidden" id="old_vendorprice_color<?=$row['vendorprice_id']?>" value="<?=$row['vendorprice_color']?>"/>
            <input type="text" name="vendorprice_color<?=$row['vendorprice_id']?>" id="vendorprice_color<?=$row['vendorprice_id']?>" class="vendpricecolorval" value="<?=$row['vendorprice_color']?>"/>
        </div>
    <?php } ?>
    <div class="vendorprice_price prints">
        <input type="hidden" id="old_vendor_item_exprint" value="<?=$vendor['vendor_item_exprint']?>"/>
        <input type="text" name="vendor_item_exprint" id="vendor_item_exprint" class="vendpricecolorval" value="<?=$vendor['vendor_item_exprint']?>"/>
    </div>
    <div class="vendorprice_price">
        <input type="hidden" id="old_vendor_item_setup" value="<?=$vendor['vendor_item_setup']?>"/>
        <input type="text" name="vendor_item_setup" id="vendor_item_setup" class="vendpricecolorval" value="<?=$vendor['vendor_item_setup']?>"/>
    </div>
</div>