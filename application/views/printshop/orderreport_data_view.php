<?php $numpp=1;?>
<?php foreach ($orders as $row) { ?>
<div class="datarow <?=$numpp%2==0 ? 'greydatarow' : 'whitedatarow'?> <?=$row['orderclass']?>" data-report="<?=$row['printshop_income_id']?>">
    <div class="numpp"><?=$row['numpp']?></div>
    <div class="delrecord" data-reportnum="<?=$row['order_num']?>" data-report="<?=$row['printshop_income_id']?>">
    <i class="fa fa-trash" aria-hidden="true"></i>
    </div>
    <div class="orderdate"><?=$row['order_date']?></div>
    <div class="ordernum editorderreport" data-report="<?=$row['printshop_income_id']?>" data-profitview="/fulfillment/ordereport_profit?d=<?=$row['order_id']?>&amnt=<?=$row['printshop_income_id']?>">
        <?=$row['order_num']?>
    </div>
    <div class="customer"><?=$row['customer']?></div>
    <div class="itemname" data-content="<?=$row['item_name']?>"><?=$row['item_name']?></div>
    <div class="itemcolor" data-content="<?=$row['color']?>"><?=$row['color']?></div>
    <div class="shipped"><?=QTYOutput($row['shipped'])?></div>
    <div class="kepted"><?=QTYOutput($row['kepted'])?></div>
    <div class="misprints"><?=QTYOutput($row['misprint'])?></div>
    <div class="misprintproc"><?=$row['misprint_proc']?></div>
    <div class="totalqty"><?=QTYOutput($row['total_qty'])?></div>
    <div class="costea" <?=$row['details']?>>
        <?=number_format($row['price'],3)?>
        <?php if ($row['countincome'] > 1) { ?>
            <div class="multiprice">&nbsp;</div>
        <?php } ?>
    </div>
    <div class="addlcost"><?=number_format($row['extracost'],2)?></div>
    <div class="totalea"><?=number_format($row['totalea'],3)?></div>
    <div class="totaladdlcost"><?=MoneyOutput($row['extraitem'])?></div>
    <div class="itemscost"><?=MoneyOutput($row['costitem'])?></div>
    <div class="oranplate"><?=QTYOutput($row['oranplate'],2)?></div>
    <div class="blueplate"><?=QTYOutput($row['blueplate'],2)?></div>
    <div class="beigeplate"><?=QTYOutput($row['beigeplate'],2)?></div>
    <div class="totalplate"><?=QTYOutput($row['totalplates'],2)?></div>
    <div class="platecost"><?=MoneyOutput($row['platescost'])?></div>
    <div class="totalcost"><?=MoneyOutput($row['itemstotalcost'])?></div>
    <div class="misprintcost"><?=  MoneyOutput($row['misprintcost'])?></div>
</div>
<?php $numpp++;?>
<?php } ?>