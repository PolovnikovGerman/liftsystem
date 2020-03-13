<div class="pay_method_content1">
    <div class="pm_content1_tx text_blue">Finance Notes:</div>
    <input type="text" class="pay_method_input4 input_border_gray inputleadorddata" data-entity="order" data-field="finance_notes" value="<?=$order['finance_notes']?>"/>
</div>
<div class="pay_method_content2">    
    <div class="pay_methods_area">
    <?php foreach ($charges as $row) { ?>
        <div class="pay_method_content1_line">
            <div class="pay_method_inputs">
                <input type="text" class="pay_method_input1 input_border_gray chargeinput leftalign" placeholder="Amount" data-charge="<?=$row['order_payment_id']?>" data-field="amount" value="<?=$row['amount']?>"/>
                <input type="text" class="pay_method_input2 input_border_gray chargeinput leftalign" placeholder="Credit Card #" data-charge="<?=$row['order_payment_id']?>" data-field="cardnum" value="<?=$row['cardnum']?>"/>
                <input type="text" class="pay_method_input3_small input_border_gray chargeinput" data-charge="<?=$row['order_payment_id']?>" data-field="exp_month" value="<?=$row['exp_month']?>"/>
                <div style="float: left; margin-left: 2px; margin-top: 2px;">/</div>
                <input type="text" class="pay_method_input3_smallyear input_border_gray chargeinput leftalign" data-charge="<?=$row['order_payment_id']?>" data-field="exp_year" value="<?=$row['exp_year']?>"/>
                <input type="text" class="pay_method_inputcvc input_border_gray chargeinput leftalign" placeholder="cvc" data-charge="<?=$row['order_payment_id']?>" data-field="cardcode" value="<?=$row['cardcode']?>"/>
            </div>
            <?php if ($financeview==1) { ?>
            <div class="pay_method_buttonsend" data-charge="<?=$row['order_payment_id']?>">charge</div>
            <?php } ?>
        </div>        
    <?php } ?>        
    </div>
    <div class="pay_method_content1_line">
        <div class="pay_method_add text_gray addcreditcard">+add credit card</div>
    </div>
</div>
<div class="pay_method_content3"><?=$balanceview?></div>
