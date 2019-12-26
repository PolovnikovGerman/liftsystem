<!-- Search panel -->
<input type="hidden" id="perpage"  value="<?= $perpage ?>"/>
<input type="hidden" id="totalrec" value="<?= $total_rec ?>"/>
<input type="hidden" id='orderby' value="<?= $order_by ?>"/>
<input type="hidden" id="direction" value="<?= $direction ?>"/>
<input type="hidden" id="curpage" value="<?= $cur_page ?>"/>
<div class="generealorder_content">
    <div class="headrow">
        <div class="searchform">
            <img src="/img/icons/magnifier.png">
            <input placeholder="Enter order #, customer, email" value="" class="generealorder_search" id="monitorsearch"/>
            <div class="findall" id="find_ord">&nbsp;</div>
            <div class="clearsrch" id="clear_ord">&nbsp;</div>
        </div>
        <div class="filtersarea">
            <div class="sorting_select">
                <select class="filter_select" id="generalorder_options" name="order_options">
                    <option value="" selected="selected">All Orders</option>
                    <option value="1">Show Only Rush</option>
                </select>
            </div>
            <div class="sorting_select">
                <select class="filter_select" id="generalfilter_options" name="filter_options">
                    <option value="" selected="selected">All Orders</option>
                    <option value="1">Waiting on Customer's Approval</option>
                    <option value="2">Need to Make Proof</option>
                    <option value="3">Waiting for Ravi Redraw</option>
                    <option value="4">Need to Send Ravi</option>
                    <option value="5">Waiting for Customer Art</option>
                </select>
            </div>
        </div>
    </div>
    <div class="headrow">
        <div class="addneworder">&nbsp;</div>
        <div class="monitor_pagesviews">
            <div class="Pagination"></div>
        </div>

    </div>
    <div class="tablehead">
        <div class="date">Date</div>
        <div class="ordernum">Order #</div>
        <div class="orderconf">Conf #</div>
        <div class="customer">Customer</div>
        <div class="item">Items</div>
        <div class="art">Art</div>
        <div class="art">Redr</div>
        <div class="art">Vec</div>
        <div class="art">Pro</div>
        <div class="approve">Apr</div>
        <div class="ordercode">Code</div>
        <div class="ordernote">Note</div>
        <div class="revenue">Revenue</div>
        <div class="salesrepl">Sales Rep</div>
    </div>
    <div class="tabledata">&nbsp;</div>
</div>
