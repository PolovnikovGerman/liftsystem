<input type="hidden" id="searchipaddrbrand" value="<?=$brand?>"/>
<div class="searchipaddres_content">
    <div class="searchipaddres_head">
        <div class="select_timesearsch">
            <input type="radio" name="ipaddrsearchradio" id="ipaddres_today" value="thisweek" checked="checked"/> Today
            <input type="radio" name="ipaddrsearchradio" id="ipaddres_week" value="thisweek"/> This Week
            <input type="radio" name="ipaddrsearchradio" id="ipaddres_month" value="thismonth"/> This Month
            <input type="radio" name="ipaddrsearchradio" id="ipaddres_custom" value="custom"/> Custom Range:
        </div>
        <div class="select_custom_period" id="datarangeview_ipaddress">
            <div>
                <input type="text" class="datesearchinpt" id="dbgn_ipaddres"/>
                to
                <input type="text" class="datesearchinpt" id="dend_ipaddres"/>
            </div>
            <div class="customsearchbtn" id="showcustomrange_ipaddress">
                <img id="showcustomrange" src="/img/marketing/show_customrange.png">
            </div>
        </div>
    </div>
    <div class="searchipaddres_dataconatiner">
        <div class="table-dat" id="ipaddressearchcontent">

        </div>
    </div>
</div>

