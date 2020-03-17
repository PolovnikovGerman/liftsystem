<?php foreach ($shipcost as $row) { ?>
    <?php if ($row['delflag']==0 && $row['current']==1) { ?>
        <div class="ship_tax_cont2_line">            
            <div class="shiprateshowarea long active">
                <div class="ship_tax_cont2_txt">
                    <div class="ship_tax_cont2_txt1"><?=$row['shipping_method']?> - <?=MoneyOutput($row['shipping_cost'])?></div>
                    <div class="ship_tax_cont2_txt1 text_blue"> - <?=date('D - M j',$row['arrive_date'])?></div>
                </div>            
            </div>
        </div>            
    <?php } ?>
<?php } ?>
<?php foreach ($shipcost as $row) { ?>
    <?php if ($row['delflag']==0 && $row['current']==0) { ?>
        <div class="ship_tax_cont2_line opast">            
            <div class="shiprateshowarea long">
                <div class="ship_tax_cont2_txt">
                    <div class="ship_tax_cont2_txt1"><?=$row['shipping_method']?> - <?=MoneyOutput($row['shipping_cost'])?></div>
                    <div class="ship_tax_cont2_txt1 text_blue"> - <?=date('D - M j',$row['arrive_date'])?></div>
                </div>            
            </div>
        </div>            
    <?php } ?>
<?php } ?>
