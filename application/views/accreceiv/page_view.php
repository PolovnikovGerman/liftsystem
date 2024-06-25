<div class="accreceiv-content">
    <input type="hidden" id="accreceivebrand" value="<?=$brand?>">
    <input type="hidden" id="accreciveownsort" value="batch_due"/>
    <input type="hidden" id="accreciveowndir" value="asc"/>
    <input type="hidden" id="accreceiverefundsort" value="order_date"/>
    <input type="hidden" id="accreceiverefunddir" value="desc"/>
    <div class="accreceive-title">
        <div class="accreceiv-label">Accounts Receivable</div>
        <div class="accreceiv-period">
            <span>Display: </span>
            <select class="accreceiv-period-select">
                <option value="3">Last 3 Years</option>
                <option value="5">Last 5 Years</option>
                <option value="-1">All Years</option>
            </select>
        </div>
    </div>
    <div class="accreceiv-content-data">
        <div class="accreceiv-totals"></div>
        <div class="accreceiv-details"></div>
    </div>
    <div class="accreceiv-content-right"></div>
</div>
