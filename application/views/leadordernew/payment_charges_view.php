<?php foreach ($charges as $charge): ?>
    <div class="paymentbox_card">
        <input class="chargeinput paymentcard_price" type="text" name="amount"  data-charge="<?=$charge['order_payment_id'] ?>" data-fld="amount" placeholder="$0.00" <?=$edit==0 ? 'readonly' : ''?> value="<?=$charge['amount']?>"/>
        <input class="chargeinput paymentcard_number" type="text" name="cardnum_view" data-charge="<?=$charge['order_payment_id'] ?>" data-fld="cardnum" placeholder="XXXX-XXXX-XXXX-XXXX" <?=$edit==0 ? 'readonly' : ''?> value="<?=$charge['cardnum_view']?>"/>
        <input class="chargeinput paymentcard_date" type="text" name="exp_month" data-charge="<?=$charge['order_payment_id'] ?>" data-fld="exp_month" placeholder="MM" <?=$edit==0 ? 'readonly' : ''?> value="<?=$charge['exp_month']?>"/>
        <div class="paymentcard_txt">/</div>
        <input class="chargeinput paymentcard_month" type="text" name="exp_year" data-charge="<?=$charge['order_payment_id'] ?>" data-fld="exp_year" placeholder="YY" <?=$edit==0 ? 'readonly' : ''?> value="<?=$charge['exp_year']?>"/>
        <input class="chargeinput paymentcard_cvc" type="text" name="cardcode_view" data-charge="<?=$charge['order_payment_id'] ?>" data-fld="cardcode" placeholder="CVC" <?=$edit==0 ? 'readonly' : ''?> value="<?=$charge['cardcode_view']?>"/>
        <?php if ($charge['payment_save']==1 && $payment_user==1) : ?>
            <div class="paymentcard_lock" data-payid="<?=$charge['order_payment_id']?>"><i class="fa fa-lock" aria-hidden="true"></i></div>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
<?php if ($edit==1) : ?>
    <div class="btn_addcard">+add credit card</div>
<?php endif; ?>
