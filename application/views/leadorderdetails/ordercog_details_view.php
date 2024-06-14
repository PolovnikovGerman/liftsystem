<div class="ordercogdetailsviewarea <?=$profit_class?>">
    <div class="revenuearea">
        <div class="revenuetitle">Total Revenue:</div>
        <div class="revenueval"><?=MoneyOutput($data['revenue'])?></div>
    </div>
    <div class="costsarea">
        <?php foreach ($data['costs'] as $cost) { ?>
            <div class="datarow">
                <div class="costlabel"><?=$cost['label']?>:</div>
                <div class="costvalue"><?=MoneyOutput($cost['value'])?></div>
            </div>
        <?php } ?>
    </div>
    <div class="costspercarea">
        <?php foreach ($data['costs'] as $cost) { ?>
            <div class="datarow"><?=number_format($cost['proc'],2)?>%</div>
        <?php } ?>
    </div>
    <div class="title">
        <div class="date">Date</div>
        <div class="amnttype">Type</div>
        <div class="vendor">Vendor</div>
        <div class="amountsum">Amount</div>
    </div>
    <div class="dataarea">
        <?php $nrow=0;?>
        <?php foreach ($data['list'] as $row) { ?>
        <div class="datarow <?=($nrow%2==0 ? 'whitedatarow' : 'greydatarow')?>">
            <?php if ($row['printshop']==1) { ?>
                <div class="editamount actionhide">&nbsp;</div>
            <?php } else { ?>
                <div class="editamount <?=$edit_mode==1 ? 'actionhide' : ''?>" data-amount="<?=$row['amount_id']?>"><i class="fa fa-pencil"></i></div>
            <?php } ?>
            <div class="delamount <?=$edit_mode==1 ? 'actionhide' : ''?>" data-amount="<?=$row['amount_id']?>"><i class="fa fa-trash"></i></div>
            <div class="date"><?=date('m/d/y',$row['amount_date'])?></div>
            <div class="amnttype"><?=($row['printshop']==1 ? 'Print Shop' : 'PO' )?></div>
            <div class="vendor"><?=($row['printshop']==1 ? '&mdash;' : $row['vendor_name'])?></div>
            <div class="amountsum"><?=MoneyOutput($row['amount_sum'], 2)?></div>
        </div>
        <?php $nrow++;?>        
        <?php } ?>
    </div>
    <div class="procentsarea">
        <?php foreach ($data['list'] as $row) { ?>
            <div class="datarow"><?=number_format($row['proc'],2)?>%</div>
        <?php } ?>
    </div>
    <div class="totalcogarea">
        <div class="totalcoglabel">Total COG:</div>
        <div class="totalcogvalue"><?=MoneyOutput($data['cog_value'])?></div>
        <div class="totalcogperc"><?=number_format($data['cog_proc'],2)?>%</div>
    </div>
    <div class="profitarea">
        <div class="profitlabel">Profit:</div>
        <div class="profitvalue"><?=MoneyOutput($data['profit_value'])?></div>
    </div>
    <div class="profitperc"><?=number_format($data['profit_proc'],2)?>%</div>
</div>