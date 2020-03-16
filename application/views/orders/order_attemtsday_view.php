<div class="attempts_duedate">
    <div class="attempt_duedate_title">Checkout Attempts Due <?=date('m/d/Y',$date)?></div>
    <div class="attempt_duedate_tablehead">
        <div class="attemptdue_confirm">Confirm</div>
        <div class="attemptdue_customer">Customer</div>
        <div class="attemptdue_item">Item</div>
        <div class="attemptdue_color">Color</div>
        <div class="attemptdue_qty">QTY</div>
        <div class="attemptdue_sum">Amount</div>
        <div class="attemptdue_email">Email</div>
        <div class="attemptdue_phone">Phone</div>
        <div class="attemptdue_location">GeoIP locator</div>
        <div class="attemptdue_ccdetails">Credit Card</div>
        <div class="attemptdue_artsubmit">Art</div>
        <div class="attemptdue_lastmodified">Last Entered</div>
    </div>
    <div class="attemptdue_tabledat">
        <?php if ($cnt==0) {?>
            <div class="attemptdue_tablerow">No records for this date</div>
        <?php } else { ?>
            <?php foreach ($attempts as $row) {?>
                <div class="attemptdue_tablerow <?=$row['row_class']?>" data-attemptid="<?=$row['attempt_id']?>">
                    <div class="attemptdue_confirm truncateoverflowtext" data-content="<?=$row['confirm']?>"><?=$row['confirm']?></div>
                    <div class="attemptdue_customer truncateoverflowtext" data-content="<?=$row['customer']?>"><?=$row['customer']?></div>
                    <div class="attemptdue_item truncateoverflowtext" data-content="<?=$row['item']?>"><?=$row['item']?></div>
                    <div class="attemptdue_color truncateoverflowtext" data-content="<?=$row['item_color']?>"><?=$row['item_color']?></div>
                    <div class="attemptdue_qty"><?=$row['qty']?></div>
                    <div class="attemptdue_sum"><?=$row['amount']?></div>
                    <div class="attemptdue_email truncateoverflowtext" data-content="<?=$row['email']?>"><?=$row['email']?></div>
                    <div class="attemptdue_phone truncateoverflowtext" data-content="<?=$row['phone']?>"><?=$row['phone']?></div>
                    <div class="attemptdue_location truncateoverflowtext" data-content="<?=$row['customer_location']?>"><?=$row['customer_location']?></div>
                    <div class="attemptdue_ccdetails truncateoverflowtext" data-content="<?=$row['cc_details']?>"><?=$row['cc_details']?></div>
                    <div class="attemptdue_artsubmit"><?=$row['artsubm']?></div>
                    <div class="attemptdue_lastmodified truncateoverflowtext" data-content="<?=$row['last_field']?>"><?=$row['last_field']?></div>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
</div>