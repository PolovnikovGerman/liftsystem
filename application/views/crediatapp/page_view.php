<div class="creditapp_popup">
    <input type="hidden" id="crapptotals" value="<?=$totals?>"/>
    <input type="hidden" id="crappperpage" value="<?=$perpage?>"/>
    <input type="hidden" id="crappcurrentpage" value="0"/>
    <div class="title">Credit Account List</div>
    <div class="filterdata">
        <div class="searchtemplatedata">
            <input class="creditapptemplate" placeholder="Search Customer, Phone, Email" />
        </div>
        <div class="searchbtn">&nbsp;</div>
        <div class="cleansearchbtn">&nbsp;</div>
        <div class="filterdata">
            <select class="filterdataselect">
                <option value="">All Statuses</option>
                <option value="pending">Only Pending</option>
                <option value="approved">Only Approved</option>
                <option value="rejected">Only Rejected</option>
            </select>                    
        </div>
        <div class="filterdatalabel">Display:</div>
    </div>
    <div class="creditappdatahead">
        <div class="addnew">&nbsp;</div>
        <div class="status">status</div>
        <div class="customer">Customer</div>
        <div class="abbrev">Abbreviation</div>
        <div class="phone">Phone</div>
        <div class="email">Email</div>
        <div class="notes">Notes</div>
        <div class="revision">Rev by</div>
        <div class="doclnk">Doc</div>        
    </div>
    <div class="creditappdataarea"></div>
</div>