<?php foreach ($shipcost as $row) { ?>
    <?php if ($row['delflag']==0 && $row['current']==1) { ?>
        <div class="ship_tax_cont2_line" data-shipcost="<?=$row['order_shipcost_id']?>" data-shipadr="<?=$shipadr?>">
            <input type="radio" name="<?=(isset($costname) ? $costname : 'shippingrate')?>" value="<?=$row['shipping_method']?>" checked="checked" class="ship_tax_radio" data-shipcost="<?=$row['order_shipcost_id']?>" data-shipadr="<?=$shipadr?>"/>
            <div class="shiprateshowarea active" data-shipcost="<?=$row['order_shipcost_id']?>" data-shipadr="<?=$shipadr?>">
                <div class="ship_tax_cont2_txt">
                    <div class="ship_tax_cont2_txt1"><?=$row['shipping_method']?> - <?=MoneyOutput($row['shipping_cost'])?></div>
                    <div class="ship_tax_cont2_txt1 text_blue"> - <?=date('D - M j', $row['arrive_date'])?></div>
                </div>            
            </div>
        </div>            
    <?php } ?>
<?php } ?>
<?php foreach ($shipcost as $row) { ?>
    <?php if ($row['delflag']==0 && $row['current']==0) { ?>
        <div class="ship_tax_cont2_line opast" data-shipcost="<?=$row['order_shipcost_id']?>" data-shipadr="<?=$shipadr?>">
            <input type="radio" name="<?=(isset($costname) ? $costname : 'shippingrate')?>" value="<?=$row['shipping_method']?>" class="ship_tax_radio" data-shipcost="<?=$row['order_shipcost_id']?>" data-shipadr="<?=$shipadr?>"/>
            <div class="shiprateshowarea" data-shipcost="<?=$row['order_shipcost_id']?>" data-shipadr="<?=$shipadr?>">
                <div class="ship_tax_cont2_txt">
                    <div class="ship_tax_cont2_txt1"><?=$row['shipping_method']?> - <?=MoneyOutput($row['shipping_cost'])?></div>
                    <div class="ship_tax_cont2_txt1 text_blue"> - <?=date('D - M j', $row['arrive_date'])?></div>
                </div>
            </div>
        </div>            
    <?php } ?>
<?php } ?>
