<div class="bl_ship_tax_content">            
    <div class="ship_tax_content_line1">
        <div class="viewmultishipdetails text_blue">edit ship details</div>
        <div class="shipdocs_label multyship">Ship Docs:</div>
        <div class="shipdocsarea" data-shipdoc="1">
            <?php if (!empty($shipping['shipdoc1_link'])) : ?>
                <div class="shipdocs_link multyship" data-shipdoc="1" data-link="<?=$shipping['shipdoc1_link']?>" data-source="<?=$shipping['shipdoc1_src']?>">
                    <?php if ($shipping['shipdoc1_type']=='pdf') : ?>
                        <i class="fa fa-file-pdf-o"></i>
                    <?php elseif ($shipping['shipdoc1_type']=='word') : ?>
                        <i class="fa fa-file-word-o"></i>
                    <?php else : ?>
                        <i class="fa fa-file-excel-o"></i>
                    <?php endif; ?>
                </div>
                <div class="shipdocs_delete multyship" data-shipdoc="1"><i class="fa fa-times"></i></div>
            <?php else : ?>
                <div class="shipdocs_empty">
                    <div class="shipdocs_addbtn multyship" id="shipdocadd1" data-shipdoc="1">Add</div>
                </div>
            <?php endif; ?>
        </div>
        <div class="shipdocsarea" data-shipdoc="2">
            <?php if (!empty($shipping['shipdoc2_link'])) : ?>
                <div class="shipdocs_link multyship" data-shipdoc="2" data-link="<?=$shipping['shipdoc2_link']?>" data-source="<?=$shipping['shipdoc2_src']?>">
                    <?php if ($shipping['shipdoc2_type']=='pdf') : ?>
                        <i class="fa fa-file-pdf-o"></i>
                    <?php elseif ($shipping['shipdoc2_type']=='word') : ?>
                        <i class="fa fa-file-word-o"></i>
                    <?php else : ?>
                        <i class="fa fa-file-excel-o"></i>
                    <?php endif; ?>
                </div>
                <div class="shipdocs_delete multyship" data-shipdoc="2"><i class="fa fa-times"></i></div>
            <?php else : ?>
                <div class="shipdocs_empty">
                    <div class="shipdocs_addbtn multyship" id="shipdocadd2" data-shipdoc="2">Add</div>
                </div>
            <?php endif; ?>
        </div>
        <div class="shipotherparamsarea">
            <div class="rushselectarea multyship">
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