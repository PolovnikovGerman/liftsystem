<div class="bl_ship_tax_content">
    <div class="ship_tax_content_line1">
        <div class="viewmultishipdetails text_blue">view ship details</div>
        <div class="shipotherparamsarea">
            <div class="rushselectarea">
                <div class="label">Ships on:</div>
                <div class="rushdataselect" id="rushdatalistarea"><?=$rushview?></div>
            </div>
            <input type="text" class="shiprushcost input_border_black input_text_right" value="<?=MoneyOutput($shipping['rush_price'])?>" readonly="readonly"/>
        </div>        
    </div>    
    <div class="ship_tax_content_line1">
        <div class="multishipadresslist">
            <?=$shipcostview?>
        </div>
        <div class="shipdetailsarea">
            <div class="label">Shipping</div>
            <div class="dataarea">
                <input type="text" class="shippingcost input_text_right input_border_black" value="<?=($order['shipping']==0 ? '' : MoneyOutput($order['shipping']))?>" readonly="readonly"/>
            </div>
            <div class="labeltax">Sales Tax</div>
            <div class="dataarea">
                <input type="text" class="salestaxcost input_text_right input_border_black" value="<?=$order['tax']==0 ? '' : MoneyOutput($order['tax'])?>" readonly="readonly"/>
            </div>
        </div>        
    </div>
</div>
