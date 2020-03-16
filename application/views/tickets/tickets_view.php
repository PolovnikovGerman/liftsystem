<input type="hidden" id="totalrec" value="<?=$total?>"/>
<input type="hidden" id="perpage" value="<?=$perpage?>"/>
<input type="hidden" id="curpage" value="<?=$curpage?>"/>
<input type="hidden" id="orderby" value="<?=$orderby?>"/>
<input type="hidden" id="direction" value="<?=$direction?>"/>
<div class="ticketcontent">
    <div class="ticket_sorttile">
        <div class="ticket_searchinpt">
            <input id="ticketsearch" name="ticketsearch" class="ticketsearch_input" value="Enter order # customer issue vendor"/>
        </div>
        <div class="ticketsearch_action">
            <a class="find_it" id="find_tick" href="javascript:void(0);">
                Search It
            </a>
            <a class="find_it" id="clear_tick" href="javascript:void(0);">
                Clear
            </a>    
        </div>        
        <div class="ticket_adjfilter">
            <select class="ticketfilters" id="ticket_adjfiltr" name="ticket_adjfiltr">
                <option value="" selected="selected">Show Adj and Non-Adj</option>
                <option value="1">Show Only Adjust</option>                
            </select>
        </div>
        <div class="ticket_filter">
            <select class="ticketfilters" id="ticket_filter" name="ticket_filter">
                <option value="">Show All</option>
                <option value="1" selected="selected">Show Only Open</option>
                <option value="2">Show Closed</option>
            </select>
        </div>        
    </div>
    <div class="ticketview_title">
        <div class="customer_ticket_title">
            <div class="newticket" style="float: left; text-align: left; width: 90px;">
                <a class="searchbtn" href="javascript:void(0);" id="addnewtick" style="float: left;"><em></em><span>Add new</span><b></b></a>
            </div>                        
            Customer Tickets:
        </div>
        <div class="ticket_attachm_title">&nbsp;</div>
        <div class="vendor_ticket_title">Vendor Tickets:</div>
        <div class="Pagination"></div>
    </div>
    <!--  id="ticket_table" -->
    <div class="ticket_table">
        <div class="ticket_table_title">
            <div class="ticket_number">Ticket #</div>
            <div class="ticket_dateview">Date</div>
            <div class="customer_title">
                <div class="ticket_type">Type</div>
                <div class="ticket_order">Order #</div>
                <div class="ticket_customer">Customer</div>
                <div class="ticket_issue">Issue</div>
            </div>
            <div class="ticket_attachment">Attachments</div>
            <div class="vendor_title">
                <div class="vendor_cost">Cost</div>
                <div class="vendor_name">Vendor</div>
                <div class="vendor_issue">Issue</div>
            </div>
        </div>
        <div id="tickettable_dat" class="ticket_table_dat"></div>
    </div>
</div>
<div id="ticketdata" style="display: none; width: 950px; height: 490px;"></div>