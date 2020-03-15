<div class="vendoritempricearea">
    <div class="title"><?=$data['item_number']?>, <?=$data['item_name']?>, Vendor - <?=$data['vendor_name']?></div>
    <div class="vedoritempricetop">
        <div class="label">Vendor #</div>
        <div class="vendoritemnum"><?=$data['vendor_item_number']?></div>
    </div>
    <div class="vedoritempricesarea">
        <div class="pricerow">
            <div class="label">&nbsp;</div>
            <div class="labelqty">Min</div>
            <?php foreach ($prices as $row) { ?>
            <div class="labelqty"><?=$row['vendorprice_qty']?></div>            
            <?php } ?>
            <div class="labelqty">Prints</div>
            <div class="labelqty">Setup</div>
        </div>
        <div class="pricerow">
            <div class="label">Blank</div>
            <div class="priceqty"><?=($data['vendor_item_blankcost']==0 ? '&nbsp;' : MoneyOutput($data['vendor_item_blankcost'],2))?></div>
            <?php foreach ($prices as $row) { ?>
            <div class="priceqty"><?=$row['vendorprice_val']==0 ? '&nbsp;' : MoneyOutput($row['vendorprice_val'],2)?></div>            
            <?php } ?>
            <div class="priceqty">&nbsp;</div>
            <div class="priceqty">&nbsp;</div>
        </div>
        <div class="pricerow">
            <div class="label">Color</div>
            <div class="priceqty"><?=($data['vendor_item_cost']==0 ? '&nbsp;' : MoneyOutput($data['vendor_item_cost'],2))?></div>
            <?php foreach ($prices as $row) { ?>
            <div class="priceqty"><?=$row['vendorprice_color']==0 ? '&nbsp;' : MoneyOutput($row['vendorprice_color'],2)?></div>            
            <?php } ?>
            <div class="priceqty"><?=$data['vendor_item_exprint']==0 ? '&nbsp;' : MoneyOutput($data['vendor_item_exprint'],2)?></div>
            <div class="priceqty"><?=$data['vendor_item_setup']==0 ? '&nbsp;' : MoneyOutput($data['vendor_item_setup'],2)?></div>
        </div>
    </div>
</div>