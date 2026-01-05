<input type="hidden" id="totalcustomform" value="<?=$total_rec?>"/>
<input type="hidden" id="perpagecustomform" value="<?=$perpage?>"/>
<input type="hidden" id="curpagecustomform" value="<?=$cur_page?>"/>
<input type="hidden" id="sortcustomform" value="<?=$order_by?>"/>
<input type="hidden" id="sortdircustomform" value="<?=$direction?>"/>
<input type="hidden" id="customformviewbrand" value="<?=$brand?>"/>
<input type="hidden" id="customformviewtype" value="table"/>
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
        <div class="numinorder"># in order</div>
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
    <div id="customform_tabledat"></div>
</div>
<div class="customform_total_content">
    <div class="datarow">
        <div class="customform_total_switcher">Chart</div>
    </div>
    <div id="customformtotal_tableview">
        <div class="customform_total_header">
            <div class="total_weeknum">Week of</div>
            <div class="total_day">Mo</div>
            <div class="total_day">Tu</div>
            <div class="total_day">We</div>
            <div class="total_day">Th</div>
            <div class="total_day">Fr</div>
            <div class="total_day">Sa</div>
            <div class="total_day">Su</div>
            <div class="total_totals">Total</div>
        </div>
        <div class="customform_total_tabledat" id="customform_total_tabledat"></div>
    </div>
    <div id="customformtotal_chartview" class="customformtotal_chartview">
        <canvas id="myChart"></canvas>
    </div>
</div>