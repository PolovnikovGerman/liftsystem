<input type='hidden' id='online_totalrec' value="<?=$total_rec?>"/>
<input type="hidden" id='online_orderby' value="<?=$order_by?>"/>
<input type="hidden" id="online_direction" value="<?=$direction?>"/>
<input type="hidden" id="online_curpage" value="<?=$cur_page?>"/>
<input type="hidden" id="online_perpage" value="<?=$perpage?>"/>
<input type="hidden" id="last_orderid" value="<?=$last_order?>"/>
<input type="hidden" id="last_cartid" value="<?=$last_cart?>"/>
<input type="hidden" id="onlineordersbrand" value="<?=$brand?>"/>
<div class="onlineorders_content">
    <div class="searchbox">
        <div class="srchtitle_replica">
            Our#, Rep
        </div>
        <div class="srchinpt_replica">
            <input id="online_replica" type="text" class="onlinereplica"/>
        </div>
        <div class="srchtitle_confirm">
            Confirmation
        </div>
        <div class="srchinpt_confirm">
            <input id="online_confirm" type="text" class="onlineconfirm"/>
        </div>
        <div class="srchtitle_customer">
            Customer
        </div>
        <div class="srchinpt_customer">
            <input id="online_customer" type="text" class="onlinecustomer"/>
        </div>
        <div id="find_onlines" class="find_online">
            Search It
        </div>
        <div id="clear_onlines" class="find_online">
            Clear
        </div>
    </div>
    <div id="onlinePagination" class="leadorder_pagination"></div>

    <div class="online-orders-head">
        <div class="status">Status</div>
        <div class="order_number">Our #</div>
        <div class="order_replica">Rep</div>
        <div class="order_date">Date</div>
        <div class="order_confirm">Confirmation</div>
        <div class="customer_name">Name</div>
        <div class="customer_company">Company</div>
        <div class="order_item">Item</div>
        <div class="order_amount">Amount</div>
        <div class="order_export">Export</div>
    </div>
    <div class="onlineorderslist" id="onlinetabinfo"></div>
</div>
