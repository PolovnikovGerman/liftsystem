<div class="block_1 text_style_2">
    <div class="block_1_text orderdatechange">
        <input type="text" readonly="readonly" class="calendarinpt" id="order_date" value="<?=date('D - M j, Y',$order_date)?>" data-order="<?=date('Y,m,d',$order_date)?>"/>
    </div>
</div>
<div class="block_2">
    <div class="block_2_text">
        <div class="block_2_text1 text_gray">order:</div>
        <div class="block_2_text2 text_style_3 text_bold"><?= $order_num ?></div>
        <div class="block_2_text3 text_gray <?=(empty($order_confirmation) ? '' :'text_bold')?>">
            <?php if ($order_id==0) { ?>
                <?=$order_confirmation?>
            <?php } else { ?>
                <?=(empty($order_confirmation) ? 'historical' : $order_confirmation)?>
            <?php } ?>
        </div>
    </div>
</div>
<div class="block_3">
    <div class="block_3_text">
        <div class="block_2_text1 text_gray">customer:</div>
        <input type="text" class="block_3_input input_border_black inputleadorddata" data-entity="order" data-field="customer_name" value="<?=$customer_name?>"/>
    </div>
</div>
