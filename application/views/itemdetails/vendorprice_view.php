<div class="vendprices_qty">
    <div class="vendpricelabel">&nbsp;</div>
    <div class="vendorprice_qty">min</div>
    <?php foreach ($vendprice as $row) {?>
        <div class="vendorprice_qty"><?=($row['vendorprice_qty']=='' ? '&nbsp;' : $row['vendorprice_qty'])?></div>
    <?php } ?>
    <div class="vendorprice_qty prints">Prints</div>
    <div class="vendorprice_qty">Setup</div>
</div>
<div class="vendprices_price">
    <div class="vendpricelabel">Blank</div>
    <div class="vendorprice_price"><?=(floatval($vendor['vendor_item_blankcost'])==0 ? '&nbsp;' : '$'.($vendor['vendor_item_blankcost']*1000%10==0 ? number_format($vendor['vendor_item_blankcost'],2,'.','') : number_format($vendor['vendor_item_blankcost'],3,'.','')))?></div>
    <?php foreach ($vendprice as $row) {?>
        <div class="vendorprice_price"><?=(floatval($row['vendorprice_val'])==0 ? '&nbsp;' : '$'.($row['vendorprice_val']*1000%10==0 ? number_format($row['vendorprice_val'],2,'.','') : number_format($row['vendorprice_val'],3,'.','')))?></div>
    <?php } ?>
    <div class="vendorprice_price prints">&nbsp;</div>
    <div class="vendorprice_price">&nbsp;</div>
</div>
<div class="vendprices_price_color">
    <div class="vendpricelabel">1 Color</div>
    <div class="vendorprice_price"><?=(floatval($vendor['vendor_item_cost'])==0 ? '&nbsp;' : '$'.($vendor['vendor_item_cost']*1000%10==0 ? number_format($vendor['vendor_item_cost'],2,'.','') : number_format($vendor['vendor_item_cost'],3,'.','')))?></div>
    <?php foreach ($vendprice as $row) {?>
        <div class="vendorprice_price">
            <?=(floatval($row['vendorprice_color'])==0 ? '&nbsp;' : '$'.($row['vendorprice_color']*1000%10==0 ? number_format($row['vendorprice_color'],2,'.','') : number_format($row['vendorprice_color'],3,'.','')))?></div>
    <?php } ?>
    <div class="vendorprice_price prints"><?=(floatval($vendor['vendor_item_exprint'])==0 ? '&nbsp;' : '$'.($vendor['vendor_item_exprint']*1000%10==0 ? number_format($vendor['vendor_item_exprint'],2,'.','') : number_format($vendor['vendor_item_exprint'],3,'.','')))?></div>
    <div class="vendorprice_price"><?=(floatval($vendor['vendor_item_setup'])==0 ? '&nbsp;' : '$'.($vendor['vendor_item_setup']*1000%10==0 ? number_format($vendor['vendor_item_setup'],2,'.','') : number_format($vendor['vendor_item_setup'],3,'.','')))?></div>
</div>