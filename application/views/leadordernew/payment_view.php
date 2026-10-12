<div class="paymentsection_left">
    <div class="po_inputgroup">
        <label for="ordercustomerpo">PO#:</label>
        <input id="ordercustomerpo" class="inpt_billaddressarea" type="text" name="" data-address="<?=$billing['order_billing_id'] ?>" data-fld="customer_ponum" placeholder="CuPo" value="<?=$billing['customer_ponum']?>" <?=$edit==0 ? "readonly" : ""?>/>
    </div>
    <div class="billing_addressarea">
        <div class="sameaddress">
            <input id="samebilladdress" type="checkbox" name="" checked>
            <label for="samebilladdress">Same as shipping address</label>
        </div>
        <div class="inptaddress_country">
            <select <?=$edit==0 ? "disabled" : ""?> name="billing_country" id="billing_country" data-fld="country_id">
                <option value="">Choose Country</option>
                <?php foreach ($countries as $country): ?>
                    <option value="<?=$country['country_id']?>" <?=$country['country_id']==$billing['country_id'] ? 'selected="selected"' : ''?>><?=$country['country_name']?></option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" id="billordercntcode" value="<?=$billing['out_country']?>"/>
        </div>
        <div class="billaddress_box">
            <div class="copyaddress" data-addresstype="billing" data-address="<?=$billing['order_billing_id'] ?>"><i class="fa fa-clone" aria-hidden="true"></i></div>
            <textarea class="fulladdressview" data-addresstype="billing" data-address="<?=$billing['order_billing_id'] ?>"><?=$billingaddress?></textarea>
            <input class="inpt_billaddressarea inptaddress_name" <?=$edit==0 ? 'readonly' : ''?> type="text" name="customer_name"
                   placeholder="Contact Name" data-address="<?=$billing['order_billing_id'] ?>" data-fld="customer_name"
            value="<?=$billing['customer_name']?>"/>
            <input class="inpt_billaddressarea inptaddress_company" <?=$edit==0 ? 'readonly' : ''?> type="text" name="company" placeholder="Company"
                   data-address="<?=$billing['order_billing_id'] ?>" data-fld="company" value="<?=$billing['company']?>"/>
            <input class="inpt_billaddressarea inptaddress_addressline" <?=$edit==0 ? 'readonly' : ''?> type="text" name="address_1" id="billorder_line1"
                   placeholder="Address Line 1" data-address="<?=$billing['order_billing_id'] ?>" data-fld="address_1" value="<?=$billing['address_1']?>"/>
            <input class="inpt_billaddressarea inptaddress_addressline" <?=$edit==0 ? 'readonly' : ''?> type="text" name="address_2"
                   placeholder="Address Line 2" data-address="<?=$billing['order_billing_id'] ?>" data-fld="address_2" value="<?=$billing['address_2']?>"/>
            <input class="inpt_billaddressarea inptaddress_city" <?=$edit==0 ? 'readonly' : ''?> type="text" name="city"
                   placeholder="City" data-address="<?=$billing['order_billing_id'] ?>" data-fld="city" value="<?=$billing['city']?>"/>
            <div data-content='bilstateshow'>
            <?php if (count($states) > 0) : ?>
            <select class="select_addressarea" <?=$edit==0 ? 'disabled' : ''?> name="billing_state" id="billing_state">>
                <option value="">State</option>
                <?php foreach ($states as $state): ?>
                    <option value="<?=$state['state_id']?>" <?=$state['state_id']==$billing['state_id'] ? 'selected' : ''?>><?=$state['state_code']?></option>
                <?php endforeach; ?>
            </select>
            <?php endif; ?>
            </div>
            <input class="inpt_billaddressarea inptaddress_zipcode" <?=$edit==0 ? 'readonly' : ''?> type="text" name="zip"
                   placeholder="Zip" data-address="<?=$billing['order_billing_id'] ?>" data-fld="zip" value="<?=$billing['zip']?>"/>
        </div>
    </div>
</div>
<?php $numpp = 0; ?>
<div class="paymentsection_right">
    <div class="paymenthistory">
        <div class="orddtls_subtitle">Payment History</div>
        <div class="paymnt_viewdeclined" data-order="<?=$order['order_id']?>">View Declined</div>
        <div class="paymenthistory_box">
            <div class="paymhist_tbl">
                <div class="paymhist_tr paymhist_header">
                    <div class="paymhist_td paymhist_date">Date</div>
                    <div class="paymhist_td paymhist_payment">Payment</div>
                    <div class="paymhist_td paymhist_amount">Amount</div>
                </div>
                <div class="paymhist_tbl_body" id="paymhist_tbl_body">
                    <?php $this->load->view('leadordernew/payments_history_view',['payments' => $payments])?>
                </div>
            </div>
            <div class="paymentsattemptsview">
                <div class="artpopupclosewin" id="paymentattemptsclosewin">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" version="1.1" style="shape-rendering:geometricPrecision;text-rendering:geometricPrecision;image-rendering:optimizeQuality;" viewBox="0 0 847 847" x="0px" y="0px" fill-rule="evenodd" clip-rule="evenodd"><g><path class="btn-closemodal-svg" d="M423 592l-196 196c-110,111 -279,-58 -169,-169l196 -196 -196 -196c-110,-110 59,-279 169,-169l196 196 196 -196c111,-110 280,59 169,169l-196 196 196 196c111,111 -58,280 -169,169l-196 -196z"></path></g></svg>
                </div>
                <div class="paymentattempts_container">&nbsp;</div>
            </div>
            <div class="paymentmanualarea">
                <div class="artpopupclosewin" id="paymentmanualclosewin">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" version="1.1" style="shape-rendering:geometricPrecision;text-rendering:geometricPrecision;image-rendering:optimizeQuality;" viewBox="0 0 847 847" x="0px" y="0px" fill-rule="evenodd" clip-rule="evenodd"><g><path class="btn-closemodal-svg" d="M423 592l-196 196c-110,111 -279,-58 -169,-169l196 -196 -196 -196c-110,-110 59,-279 169,-169l196 196 196 -196c111,-110 280,59 169,169l-196 196 196 196c111,111 -58,280 -169,169l-196 -196z"></path></g></svg>
                </div>
                <div class="paymentmanual_container">&nbsp;</div>
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
        <?php if ($edit==1) : ?>
            <div class="orddtls_addmanual">+ add manual payment / refund</div>
        <?php endif; ?>
        <div class="paymentbox">
            <?php $this->load->view('leadordernew/payment_charges_view', ['charges' => $charges, 'payment_user' => $payment_user,  'edit' => $edit]); ?>
        </div>
    </div>
    <div class="balancedueblock"><?=$this->load->view('leadordernew/balancedue_view', ['order' => $order, 'checkoutlink' => $checkoutlink], TRUE)?></div>
</div>