<input type="hidden" id="totalordlists" value="<?=$total?>"/>
<input type="hidden" id="leadordlistpage" value="0"/>
<input type="hidden" id="orderlistsviewbrand" value="<?=$brand?>"/>
<div class="leadord_headarea">
    <div class="leadord_headrow">
        <div class="leadordlist_search">
            <img src="/img/icons/magnifier.png" style="float: left; margin-right: 5px; margin-top: 3px;"/>
            <input class="leadordlst_searchdata" value="" placeholder="Enter order #, customer, email"/>
            <div class="leadorderlst_findall">&nbsp;</div>
            <div class="leadorderlst_clear">&nbsp;</div>
        </div>
        <div class="leadord_emptyspace" style="width: 40px;">&nbsp;</div>
        <div class="leadordlist_filters">
            <div>Display: </div>
            <select class="leadordlistselect usrreplica">
                <option value="0" selected="selected">Missing Qty Only</option>
                <option value="-1">All Orders</option>
            </select>
            <select class="leadordlistselect perpage" id="leadordlistperpage">
                <?php foreach ($perpage as $row) { ?>
                    <option value="<?=$row?>"><?=$row?> records/per page</option>
                <?php } ?>
            </select>
        </div>
        <div class="leadord_pagination" id="leadordlist_pagination"></div>
    </div>
    <div class="leadordlist_datahead">
        <div class="seqnum">#</div>
        <div class="date">Date</div>
        <div class="ordernum">Order</div>
        <div class="customer">Customer</div>
        <div class="qty">Qty</div>
        <div class="itemnumber">Item #</div>
        <div class="item">Item</div>
        <div class="revenue">Revenue</div>
        <div class="edit">
            <div class="leadorderlist_save">&nbsp;</div>
        </div>
    </div>
    <div class="leadordlist_dataarea">&nbsp;</div>
    <div class="leadordlist_datatotal"><?=$total_view?></div>
</div>
