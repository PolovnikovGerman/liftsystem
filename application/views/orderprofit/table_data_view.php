<?php $nrec=0;?>
<?php $curdate = $orders[0]['order_date']?>
<?php $drow = 0; ?>
<?php $delivclass = ''?>
<?php foreach ($orders as $row) {?>
    <?php if ($row['order_date']!==$curdate) { ?>
        <?php $delivclass = 'topseparate'?>
        <?php $drow = 0;?>
        <?php $curdate = $row['order_date']?>
    <?php } ?>
    <div class="tr <?=($nrec%2==0 ? 'whitedatarow' : 'greydatarow')?> <?=$row['lineclass']?> <?=$delivclass?> <?=$brand=='SR' ? 'relievers' : ''?>" id="profitord<?=$row['order_id']?>">
        <div class="profitorder_date_data">
            <?=$drow==0 ? $row['order_date'] : '&mdash;'?>
        </div>
        <div class="profitorder_action_data"><?=$row['cancellnk']?></div>
        <div class="profitorder_numorder_data" data-order="<?=$row['order_id']?>"><?=$row['order_num']?></div>
        <div class="profitorder_ordertype <?=$row['ordertype_class']?>"><?=$row['ordertype']=='' ? '&nbsp;' : $row['ordertype']?></div>
        <div class="profitorder_confirm_data"><?=($row['order_confirmation']=='' ? 'historical' : $row['order_confirmation'])?></div>
        <?php if ($brand=='SR') { ?>
            <div class="profitorder_customponum_data" title="<?=$row['customer_ponum']?>"><?=$row['customer_ponum']?></div>
        <?php } ?>
        <div class="profitorder_customer_data"><?=$row['customer_name']?></div>
        <div class="profitorder_qty_data"><?=$row['order_qty']?></div>
        <div class="profitorder_item_data <?=$row['item_class']?>"><?=$row['order_items']?></div>
        <div class="profitorder_item_color <?=$row['coloropt']?>" <?=$row['colordata']?>>
            <?=empty($row['color']) ? '&nbsp;' : $row['color']?>
        </div>
        <div class="profitorder_revenue_data"><?=$row['revenue']?></div>
        <div class="profitorder_balance_data <?=$row['balance_class']?>"><?=$row['balance']?></div>
        <div class="profitorder_shipping_calc" data-order="<?=$row['order_id']?>"><?=$row['input_ship']?></div>
        <div class="profitorder_shipping_data"><?=$row['shipping']?></div>
        <div class="profitorder_shipdate_data"><?=$row['out_shipdate']?></div>
        <div class="profitorder_tax_data"><?=$row['tax']?></div>
        <!-- <div class="profitorder_othercost_data"><?php // echo $row['input_other'] ?></div> -->
        <div class="profitorder_addlnk"><?=$row['add']?></div>
        <div class="profitorder_cog_data <?=$row['cog_class']?>"><?=$row['order_cog']?></div>
        <div class="profitorder_profit_data <?=$row['profit_class']?>"><?=$row['profit']?></div>
        <div class="profitorder_profitperc_data <?=$row['profit_class']?> <?=$row['proftitleclass']?>" <?=$row['proftitle']?>>
            <?=$row['profit_perc']?>
        </div>
        <?php $drow++?>
        <?php $delivclass = ''?>
    </div>
    <?php $nrec++?>
<?php } ?>