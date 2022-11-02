<input type="hidden" id="perpageproj" value="<?=$perpage?>"/>
<input type="hidden" id="orderbyproj" value="<?=$order?>"/>
<input type="hidden" id="directionproj" value="<?=$direc?>"/>
<input type="hidden" id="totalproj" value="<?=$total?>"/>
<input type="hidden" id="curpageproj" value="<?=$curpage;?>"/>
<input type="hidden" id="postatusviewbrand" value="<?=$brand?>"/>
<div class="postatuses_content">
    <div class="monitor-head inner">
        <div class="adminsearchform">
            <div class="monitorsearch_form">
                <div class="monitorsearch_input">
                    <input id="statussearch" name="statussearch" class="monitorsearch_input" placeholder="Enter order #, customer"/>
                </div>
                <div class="search_action">
                    <a class="find_it" id="find_status" href="javascript:void(0);">
                        Search It
                    </a>
                    <a class="find_it" id="clear_status" href="javascript:void(0);">
                        Clear
                    </a>
                </div>
            </div>
            <div class="monitor_pagesviews" style="width: 525px;">
                <div class="status_orderprivselect">
                    <select class="filter_select" id="status_orderselect">
                        <option value="1"selected="selected">By Last Update</option>
                        <option value="2">By Order #</option>
                        <option value="3">By Order Date</option>
                        <option value="4">By Revenue</option>
                    </select>
                </div>
                <div class="artsorting_select" style="margin-left: 11px;">
                    <select class="filter_select" id="status_datselect">
                        <option value="0">All Orders</option>
                        <option value="1" selected="selected">Last 6 Months</option>
                    </select>
                </div>
                <div class="sorting_select">
                    <select class="filter_select" id="status_options">
                        <option value="" selected="selected">All Orders</option>
                        <option value="notplaced">To Place PO</option>
                        <option value="notapprov">Need Approval (Waiting on Customer)</option>
                        <option value="notprof">To Proof (Need to Make Proof)</option>
                        <option value="notvector">Need Redraw (Waiting on Ravi)</option>
                        <option value="notredr">To Check if Vector (Check if need to send Ravi)</option>
                        <option value="noart">Need Art (Waiting on Customer)</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="table-status">
        <div class="content-status-table" id="statustableinfo"></div>
    </div>
</div>
