<div class="section" data-section="<?=$label?>">
    <?php if (count($data)==0) { ?>
        &nbsp;
    <?php } else { ?>
        <?php foreach ($data  as $row) {?>
            <div class="nonplacedrow" data-orderid="<?=$row['order_id']?>">
                <div class="purchase-order-profit-data"><?=$row['profit']?></div>
                <div class="purchase-order-profitperc-data"><?=$row['profit_perc']?></div>
                <div class="purchase-order-actions-data" data-order="<?=$row['order_id']?>" <?=($label=='stock' ? 'style="cursor:auto"' : '')?>>
                    <?php if ($label=='stock') { ?>
                        &nbsp;
                    <?php } else { ?>
                        <!-- <i class="fa fa-plus-circle" aria-hidden="true"></i> -->
                        <?=$row['addord'];?>
                    <?php } ?>
                </div>
                <div class="purchase-order-ordnum-data" data-content="<?=$row['potitle']?>"><?=$row['order_num']?></div>
            </div>
        <?php }?>
    <?php } ?>
</div>
