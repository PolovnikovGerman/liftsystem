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
        <div class="datarow <?=($nrow%2==0 ? 'whitedatarow' : 'greydatarow')?>">
            <div class="editamount <?=$edit_mode==1 ? 'actionhide' : ''?>" data-amount="<?=$row['amount_id']?>"><i class="fa fa-pencil"></i></div>
            <div class="delamount <?=$edit_mode==1 ? 'actionhide' : ''?>" data-amount="<?=$row['amount_id']?>"><i class="fa fa-trash"></i></div>
            <div class="date"><?=date('m/d/y',$row['amount_date'])?></div>
            <div class="amnttype"><?=($row['printshop']==1 ? 'Print Shop' : 'PO' )?></div>
            <div class="vendor"><?=($row['printshop']==1 ? '&mdash;' : $row['vendor_name'])?></div>
            <div class="amountsum"><?=MoneyOutput($row['amount_sum'], 2)?></div>
        </div>
        <?php $nrow++;?>        
        <?php } ?>
    </div>
</div>