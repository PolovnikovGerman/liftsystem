<input type="hidden" id="order_id" name="order_id" value="<?=$order_id?>"/>
<input type="hidden" id="datedue" name="date_due" value=""/>
<input type="hidden" id="is_canceled" name="is_canceled" value="<?=$is_cancel?>"/>
<div class="batchpopup">
    <div class="batchselectunit">
        <div class="batchselectunit_title">Which Batch?</div>
        <input type="text" id="batchselect" class="selectbatchunit" readonly="readonly"/>
        <div class="batchpaymetod_title">Pay Method:</div>
        <select class="batchpaymetod" name="paymethod" id="paymethod">
            <option value="v">Visa</option>
            <option value="m">MasterCard</option>                    
            <option value="d">Discover</option>
            <option value="a">American Express</option>
            <option value="o">Others</option>
            <option value="t">Terms</option>
            <option value="w">Write off</option>
        </select>
    </div>
    <div class="batchselectpay">
        <div class="batchpaymetod_amount">Amount:</div>
        <input type="text" class="amountvalue" name="amount" id="amount" value="<?=$amount?>"/>
        <div class="bathpaydatedue">&nbsp;</div>
        <div class="batchpaynote">Click here to write note</div>
        <div class="batchpaynotelnk"><img src="/img/accounting/list.png" alt="Note"/></div>
        
    </div>
    <div class="batchpaynote_content">
        <textarea id="batch_note" name="batch_note" class="batchnote"><?=$batch_note?></textarea>
    </div>
    <!-- Total for batch -->
    <div class="batchpaymentresults">
        <div class="batchpopmentresults_value">&nbsp;</div>
    </div>
    <!-- table with results -->
    <div class="batchdaytabledat">
        <div class="batchpoptable"></div>
    </div>
    
    <div class="savebatch" id="savebatch" style="display: none;">Save</div>
    
</div>
