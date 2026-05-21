<div class="bl_ship_tax_content">
    <div class="ship_tax_content_line1">
        <div class="viewmultishipdetails text_blue">view ship details</div>
        <div class="shipdocs_label">Docs:</div>
        <div class="shipdocs_empty">
            <?php if (!empty($shipping['shipdoc1_link'])) : ?>
                <div class="shipdocs_link" data-shipdoc="1" data-link="<?=$shipping['shipdoc1_link']?>" data-source="<?=$shipping['shipdoc1_src']?>">Doc1</div>
            <?php else : ?>
                &nbsp;
            <?php endif; ?>
        </div>
        <div class="shipdocs_empty">
            <?php if (!empty($shipping['shipdoc2_link'])) : ?>
                <div class="shipdocs_link" data-shipdoc="2" data-link="<?=$shipping['shipdoc2_link']?>" data-source="<?=$shipping['shipdoc2_src']?>">Doc2</div>
            <?php else : ?>
                &nbsp;
            <?php endif; ?>
        </div>
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
