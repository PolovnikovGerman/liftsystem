<?php $numpp=0;?>
<div class="payments_table payments_table_text">
    <?php foreach ($payments as $row) { ?>
        <div class="payments_table_line">
            <div class="payments_date2"><?=$row['out_date']?></div>
            <div class="payments_payment2 text_blue"><?=$row['out_name']?></div>
            <div class="payments_amnt2 <?=$row['payclass']?>"><?=$row['paysum']?></div>
        </div>        
        <?php $numpp++;?>
    <?php } ?>
<!--    --><?php //if ($numpp<4) { ?>
<!--    --><?php //for ($i=$numpp; $i<4 ; $i++) { ?>
<!--        <div class="payments_table_line">-->
<!--            <div class="payments_date2">&nbsp;</div>-->
<!--            <div class="payments_payment2">&nbsp;</div>-->
<!--            <div class="payments_amnt2">&nbsp;</div>-->
<!--        </div>                -->
<!--    --><?php //} ?>
<!--    --><?php //} ?>
</div>
