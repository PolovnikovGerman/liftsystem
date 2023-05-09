<form id="paymentdataform">
    <div class="manualpayment">
        <div class="paymentdata">        
            <div class="paytype input_border_gray">
                <div class="paytypevalue">
                    <input type="radio" class="paymenttypeinput" name="paytype" value="payment" <?= ($type == 'payment' ? 'checked="checked"' : '') ?> />
                </div>
                <div class="paytypelabel">Payment</div>
                <div class="paytypevalue">
                    <input type="radio" class="paymenttypeinput" name="paytype" value="refund" <?= ($type == 'refund' ? 'checked="checked"' : '') ?> />                
                </div>
                <div class="paytypelabel">Refund</div>
            </div>
            <div class="paymentdetails head">
                <div class="replica">Rep</div>
                <div class="date">Date</div>
                <div class="paytipe">Type</div>
                <div class="paynum">Payment #</div>
                <div class="payamnt">Amount</div>
            </div>
            <div class="paymentdetails">
                <div class="replica">
                    <input class="input_border_black paydatadetails payrepl" readonly="readonly" data-fldname="replica" value="<?= $replica ?>"/>
                </div>
                <div class="date">
                    <input class="input_border_black paydatadetails paydate" readonly="readonly" data-fldname="date" value="<?= $date ?>"/>
                </div>
                <div class="paytipe">
                    <select class="input_border_black paydatadetails paymenttype">
                        <option value="">...</option>
                        <option value="Check">Check</option>
                        <option value="Wire">Wire</option>
                        <option value="ACH">ACH</option>
                        <option value="Manual CC">Manual CC</option>
                        <option value="Paypal">Paypal</option>
                        <option value="Cash">Cash</option>
                        <option value="WriteOFF">WriteOFF</option>
                        <option value="Internal">Internal</option>
                    </select>
                </div>
                <div class="paynum">
                    <input class="input_border_black paydatadetails paynum" data-fldname="paynum" value="<?= $paynum ?>"/>
                </div>
                <div class="payamnt">
                    <input class="input_border_black paydatadetails payamount" data-fldname="amount" value="<?= $amount ?>"/>
                </div>
            </div>
            <div class="paymentdatasave">&nbsp;</div>
        </div>
    </div>
</form>
