<div class="leadtotaldetailsarea">
    <div class="ordertotaldetailrow">
        <div class="ordertotallabel">Leads : <?= $total ?></div>
        <div class='ordertotalperiod'><?= $label ?></div>
    </div>
    <div class="leadtotalhead">
        <div class="leadnumber">Lead #</div>
        <div class="leaddate">Date</div>
        <div class="leadvalue">Rev</div>
        <div class="leadcustomer">Customer</div>
        <div class="leadqty">QTY</div>
        <div class="leaditem">Item</div>
    </div>
    <div class="leadtotaldataarea">
        <?php $numpp=0;?>
        <?php foreach ($leads as $row) { ?>
            <div class="leadtotaldatarow <?=($numpp%2==0 ? 'greydatarow' : 'whitedatarow')?>">
                <div class="leadnumber" data-lead="<?=$row['lead_id']?>">L<?=$row['lead_number']?></div>
                <div class="leaddate"><?=date('m/d/y', $row['lead_date'])?></div>
                <div class="leadvalue"><?=$row['out_value']?></div>
                <div class="leadcustomer"><?=$row['contact']?></div>
                <div class="leadqty"><?=$row['lead_itemqty']?></div>
                <div class="leaditem <?=$row['lead_itemclass']?>"><?=$row['out_lead_item']?></div>
            </div>
            <?php $numpp++; ?>
        <?php } ?>
    </div>
</div>