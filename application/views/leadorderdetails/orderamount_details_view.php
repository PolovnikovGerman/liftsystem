<?php $numpp = 1; ?>
<?php foreach ($details as $detail) : ?>
    <div class="tabledatasection <?=$detail['printshop']==1 ? 'printdetails' : 'details'?> <?=$numpp%2==0 ? 'whitedatarow' : 'greydatarow'?>" data-amount="<?=$detail['amount_id']?>">
        <?php if ($detail['printshop']==1) : ?>
            <div class="openprintamnt">Open</div>
        <?php else : ?>
            <div class="editamount <?=$edit_mode==1 ? 'actionhide' : ''?>" data-amount="<?=$detail['amount_id']?>"><i class="fa fa-pencil"></i></div>
            <div class="delamount <?=$edit_mode==1 ? 'actionhide' : ''?>" data-amount="<?=$detail['amount_id']?>"><i class="fa fa-trash"></i></div>
        <?php endif; ?>
        <div class="qtyamnt"><?=$detail['qty']?></div>
        <div class="priceamnt"><?=$detail['price']?></div>
        <div class="dateamnt"><?=date('m/d/y', $detail['amount_date'])?></div>
        <div class="typeamnt"><?=$detail['type']?></div>
        <div class="vendoramnt"><?=$detail['vendor']?></div>
        <div class="paymetodamnt"><?=$detail['payment_method']?></div>
        <div class="amountsum"><?=MoneyOutput($detail['amount'], 2)?></div>
        <div class="includeship">
            <?php if ($detail['is_shipping']==1) : ?>
                <i class="fa fa-check-square"></i>
            <?php else: ?>
                <i class="fa fa-square-o"></i>
            <?php endif; ?>
        </div>
        <div class="profitdataperc"><?=$detail['profit_perc']?>%</div>
    </div>
    <?php $numpp++; ?>
<?php endforeach;?>
