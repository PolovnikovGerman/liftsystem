<input type="hidden" id="searchkeywordbrand" value="<?=$brand?>"/>
<div class="searchkeywords_content">
    <div class="searchckeywords_head">
        <div class="select_timesearsch">
            <input type="radio" name="keywordsearchradio" id="today_keywords" value="thisweek" checked="checked"/> Today
            <input type="radio" name="keywordsearchradio" id="week_keywords" value="thisweek"/> This Week
            <input type="radio" name="keywordsearchradio" id="month_keywords" value="thismonth"/> This Month
            <input type="radio" name="keywordsearchradio" id="custom_keywords" value="custom"/> Custom Range:
        </div>
        <div class="select_custom_period" id="datarangeview_keywords">
            <div>
                <input type="text" class="datesearchinpt" readonly id="dbgn_keywords"/>
                <span> to </span>
                <input type="text" class="datesearchinpt" readonly id="dend_keywords"/>
            </div>
            <div class="customsearchbtn" id="showcustomrange_keywords">
                <img src="/img/marketing/show_customrange.png"/>
            </div>
        </div>
        <div class="display-results">
            Displaying
            <input type="radio" value="0" id="allresults" name="keywordresultdisp" checked="checked"/> All
            <input type="radio" value="1" id="positive" name="keywordresultdisp"/> Results Only
            <input type="radio" value="2" id="negative" name="keywordresultdisp"/> No Results
        </div>
    </div>
    <div class="searchkeyword_dataconatiner">
        <div class="table-dat" id="keywordsearchcontent" style="clear: both;float: left;  width: 56666px;">

        </div>
    </div>
</div>
