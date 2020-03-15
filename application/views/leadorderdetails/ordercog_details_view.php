<div class="ordercogdetailsviewarea <?=$profit_class?>">
    <div class="title">
        <div class="date">Date</div>
        <div class="amnttype">Type</div>
        <div class="vendor">Vendor</div>
        <div class="amountsum">Amount</div>
    </div>
    <div class="dataarea">
        <?php $nrow=0;?>
        <?php foreach ($data as $row) { ?>
        <div class="datarow <?=($nrow%2==0 ? 'white' : 'grey')?>">
            <div class="date"><?=date('m/d/y',$row['amount_date'])?></div>
            <div class="amnttype"><?=($row['printshop']==1 ? 'Print Shop' : 'PO' )?></div>
            <div class="vendor"><?=($row['printshop']==1 ? '&mdash;' : $row['vendor_name'])?></div>
            <div class="amountsum"><?=MoneyOutput($row['amount_sum'], 2)?></div>
        </div>
        <?php $nrow++;?>        
        <?php } ?>
    </div>
</div>