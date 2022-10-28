<input type="hidden" id="totalcustomform" value="<?=$total_rec?>"/>
<input type="hidden" id="perpagecustomform" value="<?=$perpage?>"/>
<input type="hidden" id="curpagecustomform" value="<?=$cur_page?>"/>
<input type="hidden" id="sortcustomform" value="<?=$order_by?>"/>
<input type="hidden" id="sortdircustomform" value="<?=$direction?>"/>
<input type="hidden" id="customformviewbrand" value="<?=$brand?>"/>

<div class="customform_content">
    <div class="customform_header">
        <div class="label">Display:</div>
        <select id="customform_status" class="status_select">
            <option value="1" selected>Not assigned</option>
            <option value="">All Custom Forms</option>
        </select>
        <input type="text" id="customformsearch" class="search_input" placeholder="Customer,company, email.."/>

        <a class="find_customformbnt" id="find_customform" href="javascript:void(0);">Search It</a>
        <a class="find_customformbnt" id="clear_customform" href="javascript:void(0);">Clear</a>
        <div class="customform_pagination" id="customformpagination"></div>
    </div>
    <div class="customform_tabtitle">
        <div class="numrec">#</div>
        <div class="websys">
            <select class="customformhideincl" id="customformhideincl">
                <option value="1" selected="selected">Not Hidden</option>
                <option value="">All</option>
            </select>
        </div>
        <div class="status">Status</div>
        <div class="date">Date</div>
        <div class="customname">Name</div>
        <div class="custommail">Email</div>
        <div class="customphone">Phone</div>
        <div class="itemdescription">Description</div>
        <div class="itemqty">QTY</div>
        <div class="eventdate">Event</div>
    </div>
    <div class="customform_tabledat"></div>
</div>
