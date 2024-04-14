<?php foreach ($charges as $row) { ?>
    <div class="pay_method_content1_line">
        <div class="pay_method_inputs">
            <input type="text" class="pay_method_input1 input_border_gray chargeinput" placeholder="Amount" data-charge="<?= $row['order_payment_id'] ?>" data-field="amount" value="<?=$row['amount']?>"/>
            <input type="text" class="pay_method_input2 input_border_gray chargeinput" placeholder="Credit Card #" data-charge="<?= $row['order_payment_id'] ?>" data-field="cardnum" value="<?= $row['cardnum_view'] ?>"/>
            <input type="text" class="pay_method_input3_small input_border_gray chargeinput" data-charge="<?= $row['order_payment_id'] ?>" data-field="exp_month" value="<?= $row['exp_month'] ?>">
            <div style="float: left;">/</div>
            <input type="text" class="pay_method_input3_small input_border_gray chargeinput" data-charge="<?= $row['order_payment_id'] ?>" data-field="exp_year" value="<?= $row['exp_year'] ?>">
            <input type="text" class="pay_method_inputcvc input_border_gray chargeinput" placeholder="cvc" data-charge="<?= $row['order_payment_id'] ?>" data-field="cardcode" value="<?= $row['cardcode_view'] ?>">
        </div>
        <?php if ($row['payment_save']==1 && $payment_user==1) { ?>
            <div class="paymentdetails_unlock" data-payid="<?=$row['order_payment_id']?>">
                <i class="fa fa-lock"></i>
            </div>
        <?php } ?>
        <?php if ($order_id>0 && $financeview==1) { ?>
            <div class="pay_method_buttonsend" style="display: <?=$row['payment_save']==0 ? 'block' : 'none'?>" data-charge="<?=$row['order_payment_id']?>">charge</div>
        <?php } else { ?>
            <div class="pay_method_button">            
                <input type="checkbox" class="autopaycharge" <?= $row['autopay'] == 1 ? 'checked="checked"' : '' ?> data-charge="<?= $row['order_payment_id'] ?>"/>
                <div class="label">auto</div>
            </div>            
        <?php } ?>
    </div>        
<?php } ?>        
