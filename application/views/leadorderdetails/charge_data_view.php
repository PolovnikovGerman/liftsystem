<div class="pay_method_content1">
    <div class="pm_content1_tx text_blue">Finance Notes:</div>
    <input type="text" class="pay_method_input4 input_border_gray" value="<?=$order['finance_notes']?>"/>
</div>
<div class="pay_method_content2">
    <div class="pay_methods_area">
    <?php foreach ($charges as $row) { ?>
    <div class="pay_method_content1_line">
        <div class="pay_method_inputs">
            <input type="text" class="pay_method_input1 input_border_gray leftalign" readonly="readonly" placeholder="Amount" value="<?=$row['out_amount']?>"/>
            <input type="text" class="pay_method_input2 input_border_gray leftalign" readonly="readonly" placeholder="Credit Card #" data-charge="<?=$row['order_payment_id']?>" value="<?=$row['cardnum_view']?>"/>
            <input type="text" class="pay_method_input3 input_border_gray" readonly="readonly" value="<?=$row['exp_date']?>"/>
            <input type="text" class="pay_method_inputcvc input_border_gray leftalign" readonly="readonly" placeholder="cvc" data-charge="<?=$row['order_payment_id']?>" value="<?=$row['cardcode_view']?>"/>
        </div>
        <!--
        <div class="pay_method_button">
            <input type="checkbox" class="autopay" disabled="disabled" <?=$row['autopay']==1 ? 'checked="checked"' : ''?>/>
            <div class="label">auto-charge</div>            
        </div>
        -->
        <?php if ($row['payment_save']==1 && $payment_user==1) { ?>
            <div class="paymentdetails_unlock" data-payid="<?=$row['order_payment_id']?>">
                <i class="fa fa-lock"></i>
            </div>
        <?php } ?>
    </div>
    <?php } ?>        
    </div>
</div>
<div class="pay_method_content3"><?=$balanceview?></div>
