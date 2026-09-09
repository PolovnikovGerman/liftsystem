<div class="paymentsection_left">
    <div class="po_inputgroup">
        <label for="ordercustomerpo">PO#:</label>
        <input id="ordercustomerpo" type="text" name="" placeholder="CuPo" value="<?=$billing['customer_ponum']?>" <?=$edit==0 ? "readonly" : ""?>/>
    </div>
    <div class="billing_addressarea">
        <div class="sameaddress">
            <input id="samebilladdress" type="checkbox" name="" checked>
            <label for="samebilladdress">Same as shipping address</label>
        </div>
        <div class="inptaddress_country">
            <select <?=$edit==0 ? "disabled" : ""?> name="billing_country" id="billing_country">
                <option value="">Choose Country</option>
                <?php foreach ($countries as $country): ?>
                    <option value="<?=$country['country_id']?>" <?=$country['country_id']==$billing['country_id'] ? 'selected="selected"' : ''?>><?=$country['country_name']?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="billaddress_box">
            <div class="copyaddress"><i class="fa fa-clone" aria-hidden="true"></i></div>
            <input class="inpt_addressarea inptaddress_name" <?=$edit==0 ? 'readonly' : ''?> type="text" name="" placeholder="Contact Name" value="<?=$billing['customer_name']?>"/>
            <input class="inpt_addressarea inptaddress_company" <?=$edit==0 ? 'readonly' : ''?> type="text" name="" placeholder="Company" value="<?=$billing['company']?>"/>
            <input class="inpt_addressarea inptaddress_addressline" <?=$edit==0 ? 'readonly' : ''?> type="text" name="" placeholder="Address Line 1" value="<?=$billing['address_1']?>"/>
            <input class="inpt_addressarea inptaddress_addressline" <?=$edit==0 ? 'readonly' : ''?> type="text" name="" placeholder="Address Line 2" value="<?=$billing['address_2']?>"/>
            <input class="inpt_addressarea inptaddress_city" <?=$edit==0 ? 'readonly' : ''?> type="text" name="" placeholder="City" value="<?=$billing['city']?>"/>
            <?php if (count($states) > 0) : ?>
            <select class="select_addressarea" <?=$edit==0 ? 'disabled' : ''?> name="billing_state" id="billing_state">>
                <option value="">State</option>
                <?php foreach ($states as $state): ?>
                    <option value="<?=$state['state_id']?>" <?=$state['state_id']==$billing['state_id'] ? 'selected' : ''?>><?=$state['state_code']?></option>
                <?php endforeach; ?>
            </select>
            <?php endif; ?>
            <input class="inpt_addressarea inptaddress_zipcode" <?=$edit==0 ? 'readonly' : ''?> type="text" name="" placeholder="City" value="<?=$billing['zip']?>"/>
        </div>
    </div>
</div>
<?php $numpp = 0; ?>
<div class="paymentsection_right">
    <div class="paymenthistory">
        <div class="orddtls_subtitle">Payment History</div>
        <div class="paymnt_viewdeclined">View Declined</div>
        <div class="paymenthistory_box">
            <div class="paymhist_tbl">
                <div class="paymhist_tr paymhist_header">
                    <div class="paymhist_td paymhist_date">Date</div>
                    <div class="paymhist_td paymhist_payment">Payment</div>
                    <div class="paymhist_td paymhist_amount">Amount</div>
                </div>
                <?php foreach ($payments as $payment): ?>
                    <div class="paymhist_tr <?=$numpp%2==0 ? 'whitedatarow' : 'greydatarow'?>">
                        <div class="paymhist_td paymhist_date"><?=$payment['out_date']?></div>
                        <div class="paymhist_td paymhist_payment"><?=$payment['out_name']?></div>
                        <div class="paymhist_td paymhist_amount"><?=$payment['paysum']?></div>
                    </div>
                    <?php $numpp++; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="totalpaymnt">
        <div class="totalpaymnt_txt">Total Payments:</div>
        <div class="totalpaymnt_price"><?=MoneyOutput($order['payment_total'])?></div>
    </div>
</div>
<div class="paymentsection_bottom">
    <div class="paymentblock">
        <div class="orddtls_subtitle">PAYMENT</div>
        <?php if ($edit==0) : ?>
            <div class="orddtls_addmanual">+ add manual payment / refund</div>
        <?php endif; ?>
        <div class="paymentbox">
            <?php foreach ($charges as $charge): ?>
            <div class="paymentbox_card">
                <input class="paymentcard_price" type="text" name="" placeholder="$0.00" <?=$edit==0 ? 'readonly' : ''?> value="<?=$charge['amount']?>"/>
                <input class="paymentcard_number" type="text" name="" placeholder="XXXX-XXXX-XXXX-XXXX" <?=$edit==0 ? 'readonly' : ''?> value="<?=$charge['cardnum_view']?>"/>
                <input class="paymentcard_date" type="text" name="" placeholder="DD" <?=$edit==0 ? 'readonly' : ''?> value="<?=$charge['exp_month']?>"/>
                <div class="paymentcard_txt">/</div>
                <input class="paymentcard_month" type="text" name="" placeholder="YY" <?=$edit==0 ? 'readonly' : ''?> value="<?=$charge['exp_year']?>"/>
                <input class="paymentcard_cvc" type="text" name="" placeholder="CVC" <?=$edit==0 ? 'readonly' : ''?> value="<?=$charge['cardcode_view']?>"/>
                <div class="paymentcard_lock"><i class="fa fa-lock" aria-hidden="true"></i></div>
            </div>
            <?php endforeach; ?>
            <?php if ($edit==1) : ?>
                <div class="btn_addcard">+add credit card</div>
            <?php endif; ?>
        </div>
    </div>
    <div class="balancedueblock">
        <div class="balanceduebox">
            <?php if ($order['payment_total']<$order['revenue']): ?> : ?>
                <div class="balancedue_link">
                    <div class="balancedue_linkicon">
                        <img src="/img/leadorder/icon-link-grey.svg">
                    </div>
                </div>
                <div class="balancedue_send"><i class="fa fa-envelope-o" aria-hidden="true"></i></div>
                <div class="balancedue">
                    <div class="balancedue_txt">Balance Due:</div>
                    <div class="balancedue_price"><?=MoneyOutput($order['revenue']-$order['payment_total'])?></div>
                </div>
            <?php else: ?>
                <div class="balancedue">
                    <div class="balancedue_txt">Total Due:</div>
                    <div class="balancedue_price">PAID</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>