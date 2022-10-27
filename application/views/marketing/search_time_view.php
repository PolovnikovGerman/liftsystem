<input type="hidden" id="searchtimebrand" value="<?=$brand?>"/>
<div class="searchtime_content">
    <div class="searhctime_head">
        <div class="select_timesearsch">
            <!-- <input type="radio" name="time" id="alltime" value="all"/> All Time -->
            <input type="radio" name="timesearchradio" id="timesearchweek" value="thisweek" checked="checked"/> This Week
            <input type="radio" name="timesearchradio" id="timesearchmonth" value="thismonth"/> This Month
            <input type="radio" name="timesearchradio" id="timesearchcustom" value="custom"/> Custom Range:
        </div>
        <div class="select_custom_period" id="datarangeview">
            <div>
                <input type="text" class="datesearchinpt" readonly id="dbgn_timesearch"/>
                <span> to </span>
                <input type="text" class="datesearchinpt" readonly id="dend_timesearch"/>
            </div>
            <div class="customsearchbtn" id="showcustomrange">
                <img src="/img/marketing/show_customrange.png"/>
            </div>
        </div>
    </div>
    <div class="searchtime_dataconatiner">
        <div class="datacontainer_head weekdates">Week of</div>
        <div class="datacontainer_head weekday">Mon</div>
        <div class="datacontainer_head weekday">Tue</div>
        <div class="datacontainer_head weekday">Wed</div>
        <div class="datacontainer_head weekday">Thu</div>
        <div class="datacontainer_head weekday">Fri</div>
        <div class="datacontainer_head weekday">Sat</div>
        <div class="datacontainer_head weekdayend">Sun</div>

        <div class="datacontainer_content" id="timesearchresultcontent">

        </div>
    </div>
</div>
