<?php $nrow=1;?>
<?php foreach ($orders as $row) {?>    
    <div class="tr <?=(($nrow%2)==0 ? 'whitedatarow' : 'greydatarow')?>" id="paymon<?=$row['order_id']?>">
        <div class="paymonitor-approved-dat <?=($row['order_approved']==1 ? 'attachview' : '')?>" id="vattach<?=$row['order_id']?>"><?=$row['approved']?></div>
        <div class="paymonitor-orderdate-dat"><?=$row['order_date']?></div>
        <div class="paymonitor-numorder-dat <?=$row['profitclass']?>prof <?=$row['invpay_class']?>" data-order="<?=$row['order_id']?>" data-content="<?='Profit '.$row['profit'].'<br/>Profit % '.$row['profit_percent']?>">
            <?=$row['order_num']?>            
        </div>
        <div class="paymonitor-customer-dat <?=$row['invpay_class']?>" data-content="<?=$row['customer_name']?>">
            <?=$row['customer_name']?>
        </div>
        <div class="paymonitor_ccfee_dat">
            <?=$row['cccheck']?>
        </div>
        <div class="paymonitor-revenue-dat"><?=$row['revenue']?></div>
        <div class="paymonitor-inv-dat" id="inv<?=$row['order_id']?>">
            <input type="checkbox" class="chkinvinput" name="invited<?=$row['order_id']?>" id="invited<?=$row['order_id']?>" <?=$row['chkinv']?> />
        </div>
        <div class="paymonitor-revenue-dat"><?=$row['not_invoiced']?></div>
        <div class="paymonitor-revenue-dat"><?=$row['invoiced']?></div>
        <div class="paymonitor-inv-dat paydiv" id="pmnt<?=$row['order_id']?>">
            <input type="checkbox" class="chkpaid" name="paid<?=$row['order_id']?>" id="paid<?=$row['order_id']?>" <?=$row['chkpaym']?>/>
        </div>
            <?=$row['add_payment']?>
        <div class="edit_ordernote" id="ordnote<?=$row['order_id']?>">
            <?php if (empty($row['order_note'])) {?>
                <img src='/img/accounting/edit_grey.png' alt="Edit note" title="Edit Order note"/>
            <?php } else { ?>
                <img src='/img/accounting/edit_blue.png' alt="Edit note" class="ordernotedata" title="<?=$row['order_note']?>"/>
            <?php } ?>
        </div>
        <div class="paymonitor-revenue-dat notpaiddat <?=$row['paid_class']?>"><?=$row['refund']?><?=$row['not_paid']?></div>
        <div class="paymonitor-code-dat">
            <?=($row['order_code']=='' ? '&nbsp;' : '<div class="paymonitor-codedat" title="'.$row['order_code'].'">'.$row['order_code'].'</div>')?>
        </div>
    </div>
    <?php $nrow++;?>
<?php } ?>