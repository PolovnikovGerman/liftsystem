<div class="bl_ship_tax_content">            
    <div class="ship_tax_content_line1">
        <div class="viewmultishipdetails text_blue">edit ship details</div>
        <div class="shipdocs_label">Docs:</div>
        <div class="shipdocsarea" data-shipdoc="1">
            <?php if (!empty($shipping['shipdoc1_link'])) : ?>
                <div class="shipdocs_link" data-shipdoc="1" data-link="<?=$shipping['shipdoc1_link']?>" data-source="<?=$shipping['shipdoc1_src']?>">Doc1</div>
                <div class="shipdocs_delete" data-shipdoc="1"><i class="fa fa-trash-o"></i></div>
            <?php else : ?>
                <div class="shipdocs_empty">
                    <div class="shipdocs_addbtn" id="shipdocadd1" data-shipdoc="1">Add</div>
                </div>
            <?php endif; ?>
        </div>
        <div class="shipdocsarea" data-shipdoc="2">
            <?php if (!empty($shipping['shipdoc2_link'])) : ?>
                <div class="shipdocs_link" data-shipdoc="2" data-link="<?=$shipping['shipdoc2_link']?>" data-source="<?=$shipping['shipdoc2_src']?>">Doc2</div>
                <div class="shipdocs_delete" data-shipdoc="2"><i class="fa fa-trash-o"></i></div>
            <?php else : ?>
                <div class="shipdocs_empty">
                    <div class="shipdocs_addbtn" id="shipdocadd2" data-shipdoc="2">Add</div>
                </div>
            <?php endif; ?>
        </div>
        <div class="shipotherparamsarea">
            <div class="rushselectarea">
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