<div class="batches_duedate">
    <div class="batches_duedate_title">Batches Due <?=date('m/d/Y',$date)?></div>
    <div class="batches_duedate_tablehead">
        <div class="batchdue_date">Date</div>
        <div class="batchdue_paymetod">Method</div>
        <div class="batchdue_sum">Sum</div>
        <div class="batchdue_ordernum">Order #</div>
        <div class="batchdue_customer">Customer</div>
    </div>
    <div class="batchdue_tabledat">
        <?php if ($cnt==0) {?>
        <div class="batchdue_tablerow">No records for this date</div>
        <?php } else { ?>
            <?php foreach ($batches as $row) {?>
            <div class="batchdue_tablerow <?=$row['rowclass']?>">
                <div class="batchdue_date"><?=$row['batch_date']?></div>
                <div class="batchdue_paymetod"><?=$row['paymeth']?></div>
                <div class="batchdue_sum_dat <?=$row['paysum_class']?>"><?=$row['paysum']?></div>
                <div class="batchdue_ordernum"><?=$row['order_num']?></div>
                <div class="batchdue_customer_dat"><?=$row['customer_name']?></div>                
            </div>
            <?php } ?>
        <?php } ?>
    </div>
</div>