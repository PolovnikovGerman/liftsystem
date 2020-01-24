<div class="vendprices_qty">
    <div class="vendpricelabel">&nbsp;</div>
    <div class="vendorprice_qty">min</div>
    <?php foreach ($vendprice as $row) {?>
        <div class="vendorprice_qty">
            <input type="text" class="vendorprice_qtyinp vendorinputvalues" data-fld="vendorprice_qty" data-entity="vendor_prices" data-idx="<?=$row['vendorprice_id']?>" value="<?=$row['vendorprice_qty']?>"/>
        </div>
    <?php } ?>
    <div class="vendorprice_qty prints">Prints</div>
    <div class="vendorprice_qty">Setup</div>
</div>
<div class="vendprices_price">
    <div class="vendpricelabel">Blank</div>
    <div class="vendorprice_price">
        <input type="text" class="vendpriceblankval vendorinputvalues" data-entity="vendor" data-fld="vendor_item_blankcost" value="<?=$vendor['vendor_item_blankcost']?>"/>
    </div>
    <?php foreach ($vendprice as $row) {?>
        <div class="vendorprice_price">
            <input type="text" class="vendpriceblankval vendorinputvalues" data-fld="vendorprice_val" data-entity="vendor_prices" data-idx="<?=$row['vendorprice_id']?>" value="<?=$row['vendorprice_val']?>"/>
        </div>
    <?php } ?>
    <div class="vendorprice_price prints">&nbsp;</div>
    <div class="vendorprice_price">&nbsp;</div>
</div>
<div class="vendprices_price_color">
    <div class="vendpricelabel">1 Color</div>
    <div class="vendorprice_price">
        <input type="text" class="vendpricecolorval vendorinputvalues" data-entity="vendor" data-fld="vendor_item_cost" value="<?=$vendor['vendor_item_cost']?>"/>
    </div>
    <?php foreach ($vendprice as $row) {?>
        <div class="vendorprice_price">
            <input type="text" class="vendpricecolorval vendorinputvalues" data-fld="vendorprice_color" data-entity="vendor_prices" data-idx="<?=$row['vendorprice_id']?>" value="<?=$row['vendorprice_color']?>"/>
        </div>
    <?php } ?>
    <div class="vendorprice_price prints">
        <input type="text" class="vendpricecolorval vendorinputvalues" data-entity="vendor" data-fld="vendor_item_exprint" value="<?=$vendor['vendor_item_exprint']?>"/>
    </div>
    <div class="vendorprice_price">
        <input type="text" class="vendpricecolorval vendorinputvalues" data-entity="vendor" data-fld="vendor_item_setup" value="<?=$vendor['vendor_item_setup']?>"/>
    </div>
</div>