<div class="bl_ship_tax_content">
    <div class="ship_tax_content_line1">
        <div class="viewmultishipdetails text_blue">view ship details</div>
        <div class="shipdocs_label multyship">Ship Docs:</div>
        <?php if (!empty($shipping['shipdoc1_link'])) : ?>
            <div class="shipdocs_link multyship view" data-shipdoc="1" data-link="<?=$shipping['shipdoc1_link']?>" data-source="<?=$shipping['shipdoc1_src']?>">
                <?php if ($shipping['shipdoc1_type']=='pdf') : ?>
                    <i class="fa fa-file-pdf-o"></i>
                <?php elseif ($shipping['shipdoc1_type']=='word') : ?>
                    <i class="fa fa-file-word-o"></i>
                <?php else : ?>
                    <i class="fa fa-file-excel-o"></i>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($shipping['shipdoc2_link'])) : ?>
            <div class="shipdocs_link multyship view" data-shipdoc="2" data-link="<?=$shipping['shipdoc2_link']?>" data-source="<?=$shipping['shipdoc2_src']?>">
                <?php if ($shipping['shipdoc2_type']=='pdf') : ?>
                    <i class="fa fa-file-pdf-o"></i>
                <?php elseif ($shipping['shipdoc2_type']=='word') : ?>
                    <i class="fa fa-file-word-o"></i>
                <?php else : ?>
                    <i class="fa fa-file-excel-o"></i>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="shipotherparamsarea">
            <div class="rushselectarea multyship">
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
