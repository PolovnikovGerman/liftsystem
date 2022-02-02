<div class="bl_ship_tax_content">            
    <div class="ship_tax_content_line1">
        <div class="viewmultishipdetails text_blue">edit ship details</div>
        <div class="shipotherparamsarea">
            <?php if ($user_role=='masteradmin') { ?>
                <div class="rushadmindate">
                    <label>Ship in past:</label>
                    <input type="text" readonly="readonly" class="shiprushpast input_border_black" id="rushpast" value=""/>
                </div>
            <?php } ?>
            <div class="rushselectarea <?=$user_role=='masteradmin' ? 'admin' : ''?>">
                <div class="label">Ships on:</div>
                <div class="rushdataselect" id="rushdatalistarea"><?= $rushview ?></div>
            </div>
            <input type="text" class="shiprushcost input_text_right input_border_black" value="<?=number_format($shipping['rush_price'],2)?>"/>
        </div>
    </div>
    <div class="ship_tax_content_line1">
        <div class="multishipadresslist">
            <?=$shipcostview?>
        </div>        
        <div class="shipdetailsarea">
            <div class="label">Shipping</div>
            <div class="dataarea">
                <input type="text" class="shippingcost input_text_right input_border_black" value="<?=number_format(floatval($order['shipping']),2)?>"/>
            </div>
            <div class="labeltax">Sales Tax</div>
            <div class="dataarea">
                <input type="text" class="salestaxcost input_text_right input_border_black" readonly="readonly" value="<?= MoneyOutput($order['tax']) ?>"/>
            </div>            
        </div>
    </div>
</div>