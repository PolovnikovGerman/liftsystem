<input type="hidden" id="perpageart" value="<?=$perpage?>"/>
<input type="hidden" id="orderbyart" value="<?=$order?>"/>
<input type="hidden" id="directionart" value="<?=$direc?>"/>
<input type="hidden" id="totalart" value="<?=$total?>"/>
<input type="hidden" id="curpageart" value="<?=$curpage;?>"/>
<div class="artorder_head_area">
    <div class="artorder_head_row">
        <div class="monitorsearch_input">
            <input id="artsearch" name="artsearch" class="monitorsearch_input artordersearch" placeholder="Enter order #, customer, email"/>
        </div>
        <div class="search_action">
            <div class="artorder_find" id="find_arts">&nbsp;</div>
            <div class="artorder_clear" id="clear_arts">&nbsp;</div>
        </div>
        <div class="artorder_filter_area">
            <select class="filter_select" id="order_options" name="order_options">
                <option value="" selected="selected">All Orders</option>
                <option value="1">Show Only Rush</option>
            </select>
            <select class="filter_select" id="filter_options" name="filter_options">
                <option value="" selected="selected">All Orders</option>
                <option value="1">Waiting on Customer's Approval</option>
                <option value="2">Need to Make Proof</option>
                <option value="3">Waiting for Ravi Redraw</option>
                <option value="4">Need to Send Ravi</option>
                <option value="5">Waiting for Customer Art</option>
            </select>
        </div>
        <div class="monitor_pagesviews artpageorder">
            <div class="Pagination"></div>
        </div>
    </div>
</div>
<div class="ordertable-art">
    <div class="ordertable-title">
        <div class="addnewordart">&nbsp;</div>
        <div class="ordart_blank">Blank</div>
        <div class="ordart_date">Date</div>
        <div class="ordart_ordernum">Order # </div>
        <div class="ordart_customer">Name</div>
        <div class="ordart_item">Items</div>
        <div class="ordart_art">Art</div>
        <div class="ordart_redrawn">Redr</div>
        <div class="ordart_vector">Vec</div>
        <div class="ordart_proofed">Pro</div>
        <div class="ordart_approve">Apr</div>
        <div class="ordart_code">Code</div>
        <div class="ordart_note">Note</div>
    </div>
    <div class="content-art-table" id="orderlistdata">

    </div>
</div>
