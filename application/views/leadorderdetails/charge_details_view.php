<?php foreach ($charges as $row) { ?>
    <div class="pay_method_content1_line">
        <div class="pay_method_inputs">
            <input type="text" class="pay_method_input1 input_border_gray chargeinput" placeholder="Amount" data-charge="<?= $row['order_payment_id'] ?>" data-field="amount" value="<?=$row['amount']?>"/>
            <input type="text" class="pay_method_input2 input_border_gray chargeinput" placeholder="Credit Card #" data-charge="<?= $row['order_payment_id'] ?>" data-field="cardnum" value="<?= $row['cardnum'] ?>"/>
            <input type="text" class="pay_method_input3_small input_border_gray chargeinput" data-charge="<?= $row['order_payment_id'] ?>" data-field="exp_month" value="<?= $row['exp_month'] ?>">
            <div style="float: left;">/</div>
            <input type="text" class="pay_method_input3_small input_border_gray chargeinput" data-charge="<?= $row['order_payment_id'] ?>" data-field="exp_year" value="<?= $row['exp_year'] ?>">
            <input type="text" class="pay_method_inputcvc input_border_gray chargeinput" placeholder="cvc" data-charge="<?= $row['order_payment_id'] ?>" data-field="cardcode" value="<?= $row['cardcode'] ?>">
        </div>
        <?php if ($order_id>0) { ?>
            <div class="pay_method_buttonsend" data-charge="<?=$row['order_payment_id']?>">charge</div>            
        <?php } else { ?>
            <div class="pay_method_button">            
                <input type="checkbox" class="autopaycharge" <?= $row['autopay'] == 1 ? 'checked="checked"' : '' ?> data-charge="<?= $row['order_payment_id'] ?>"/>
                <div class="label">auto-charge</div>
            </div>            
        <?php } ?>
    </div>        
<?php } ?>        
