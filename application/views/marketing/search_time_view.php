<div class="clearfix"></div>
<div class="window">
    <div class="SearchResults-interview">
        <div class="select_timesearsch" style="clear: both; float: left; width: 368px; margin-left: 5px;">
            <input type="radio" name="time" id="alltime" value="all"/> All Time
            <input type="radio" name="time" id="week" value="thisweek"/> This Week
            <input type="radio" name="time" id="month" value="thismonth"/> This Month
            <input type="radio" name="time" id="custom" value="custom"/> Custom Range:
        </div>
        <div class="SearchResults-blok1">
            <div style="float:left;width: 223px;">
                <input type="text" style="width:75px;" readonly id="d_bgn"/>
                <button style="padding: 0pt; width: 20px; height: 19px;" id="f_btn1">
                    <img src="/img/calendar.gif" style="margin-top: -2px; margin-left: -4px;"/>
                </button>
                to
                <input type="text" style="width:75px;margin-left: 5px;" readonly id="d_end"/>
                <button style="padding: 0pt; width: 20px; height: 19px;" id="f_btn2">
                    <img style="margin-top: -2px; margin-left: -4px;"  src="/img/calendar.gif" disabled="disabled">
                </button>
            </div>
            <div style="float: left; width: 53px; padding-top: 2px; margin-left: 5px;">
                <img src="/img/marketing/show_customrange.png" id="showcustomrange"/>
            </div>
        </div>
    </div>
    <div class="table-SearchResults">
        <table cellspacing="0" cellpadding="0">
            <tr class="table-SearchResults-text1">
                <td style="width:147px;height: 36px;" class="table-01-grey TableTitle-b2 TableTitle-b1">
                    <b>Week of</b>
                </td>
                <td style="width:109px;height: 36px;" class="TableTitle-b1 TableTitle-b2 table-02-grey">
                    <a href="javascript:void(0)" id="day1" onclick="showgraph(this);"><b>Mon</b></a>
                </td>
                <td style="width:115px;height: 36px;" class="TableTitle-b1 TableTitle-b2 table-02-grey">
                    <a href="javascript:void(0)" id="day2" onclick="showgraph(this);"><b>Tue</b></a>
                </td>
                <td style="width:118px;height: 36px;" class="TableTitle-b1 TableTitle-b2 table-02-grey">
                    <a href="javascript:void(0)" id="day3" onclick="showgraph(this);"><b>Wed</b></a>
                </td>
                <td style="width:117px;height: 36px;" class="TableTitle-b1 TableTitle-b2 table-02-grey">
                    <a href="javascript:void(0)" id="day4" onclick="showgraph(this);"><b>Thu</b></a>
                </td>
                <td style="width:118px;height: 36px;" class="TableTitle-b1 TableTitle-b2 table-02-grey">
                    <a href="javascript:void(0)" id="day5" onclick="showgraph(this);"><b>Fri</b></a>
                </td>
                <td style="width:118px;height: 36px;" class="TableTitle-b1 TableTitle-b2 table-02-grey">
                    <a href="javascript:void(0)" id="day6" onclick="showgraph(this);"><b>Sat</b></a>
                </td>
                <td style="width:136px;height: 36px;" class="table-03-grey TableTitle-b2">
                    <a href="javascript:void(0)" id="day0" onclick="showgraph(this);"><b>Sun</b></a>
                </td>
            </tr>
        </table>
    </div>
    <div class="table-dat" id="tabcontent">

    </div>
    <div class="clearfix"></div>
    <div class="table-dat-rowend">
        <div class="tableEnd-SearchResults-1">&nbsp;</div>
        <div style="width:816px;"  class="tableEnd-SearchResults-2">&nbsp;</div>
        <div class="tableEnd-SearchResults-3">&nbsp;</div>
    </div>

</div>