<div class="paymonitor-approved-dat <?=($order['order_approved']==1 ? 'attachview' : '')?>" id="vattach<?=$order['order_id']?>">
    <?=$order['approved']?>
</div>
<div class="paymonitor-orderdate-dat"><?=$order['order_date']?></div>
<div class="paymonitor-numorder-dat <?=$order['profitclass']?>prof  <?=$order['invpay_class']?>" data-content="<?='Profit '.$order['profit'].'<br/>Profit % '.$order['profit_percent']?>">
    <?=$order['order_num'] ?>
</div>

<div class="paymonitor-customer-dat <?=$order['invpay_class']?>" data-content="<?=$order['customer_name']?>">
    <?=$order['customer_name'] ?>
</div>
<!-- add check symbol-->
<div class="paymonitor_ccfee_dat"><?=$order['cccheck']?></div>
<div class="paymonitor-revenue-dat"><?=$order['revenue'] ?></div>


<div class="paymonitor-inv-dat" id="inv<?=$order['order_id'] ?>">
    <input type="checkbox" class="chkinvinput" name="invited<?=$order['order_id'] ?>" id="invited<?=$order['order_id'] ?>" <?=$order['chkinv']?> />
</div>
<div class="paymonitor-revenue-dat"><?=$order['not_invoiced'] ?></div>
<div class="paymonitor-revenue-dat"><?=$order['invoiced'] ?></div>
<div class="paymonitor-inv-dat paydiv" id="pmnt<?=$order['order_id']?>">
    <input type="checkbox" class="chkpaid" name="paid<?=$order['order_id']?>" id="paid<?=$order['order_id']?>" <?=$order['chkpaym']?>/>
</div>
<?=$order['add_payment']?>
<div class="edit_ordernote" id="ordnote<?=$order['order_id']?>">
    <?php if (empty($order['order_note'])) {?>
        <img src='/img/accounting/edit_grey.png' alt="Edit note" title="Edit Order note"/>
    <?php } else { ?>
        <img src='/img/accounting/edit_blue.png' alt="Edit note" class="ordernotedata" title="<?=$order['order_note']?>"/>
    <?php } ?>
</div>
<div class="paymonitor-revenue-dat notpaiddat <?=$order['paid_class']?>"><?=$order['not_paid'] ?></div>
<div class="paymonitor-code-dat">
    <?=($order['order_code']=='' ? '&nbsp;' : '<div class="paymonitor-codedat" title="'.$order['order_code'].'">'.$order['order_code'].'</div>')?>
</div>