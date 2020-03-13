<div class="netprofitviewcontent">
    <input type="hidden" value="<?=$limitrow?>" id="limitweekshow"/>
    <div class="netprofit_header">
        <div class="netprofit_sortview">
            <span>Sort by:</span>
            <select id="netreportsortorder" class="netprofitsort">
                <option value="profitdate_desc">Date &#9660;</option>
                <option value="profitdate_asc">Date &#9650;</option>
                <option value="sales_desc">Sales &#9660;</option>
                <option value="sales_asc">Sales &#9650;</option>
                <option value="revenue_desc">Revenue &#9660;</option>
                <option value="revenue_asc">Revenue &#9650;</option>
                <option value="grosprofit_desc">Gross Profit &#9660;</option>
                <option value="grosprofit_asc">Gross Profit &#9650;</option>
                <option value="operating_desc">Operating &#9660;</option>
                <option value="operating_asc">Operating &#9650;</option>
                <option value="payroll_desc">US Payroll &#9660;</option>
                <option value="payroll_asc">US Payroll &#9650;</option>
                <option value="advertising_desc">Adwords &#9660;</option>
                <option value="advertising_asc">Adwords &#9650;</option>
                <option value="projects_desc">Int Upwork &#9660;</option>
                <option value="projects_asc">Int Upwork &#9650;</option>
                <option value="w9work_desc">W9 Work &#9660;</option>
                <option value="w9work_asc">W9 Work &#9650;</option>
                <option value="purchases_desc">Purchases &#9660;</option>
                <option value="purchases_asc">Purchases &#9650;</option>
                <option value="totalcost_desc">Total Cost &#9660;</option>
                <option value="totalcost_asc">Total Cost &#9650;</option>
                <option value="netprofit_desc">Net Profit &#9660;</option>
                <option value="netprofit_asc">Net Profit &#9650;</option>
                <option value="netsaved_desc">Savings &#9660;</option>
                <option value="netsaved_asc">Savings &#9650;</option>
                <option value="owners_desc">OD1 &#9660;</option>
                <option value="owners_asc">OD1 &#9650;</option>
                <option value="od2_desc">OD2 &#9660;</option>
                <option value="od2_asc">OD2 &#9650;</option>
            </select>
        </div>
        <div class="netreportview">
            <span>Profit view</span>
            <select id="but-reportview" name="but-reportview" class="netreportviewselect">
                <option value="week">By Week</option>
                <option value="month">By Month</option>
            </select>
        </div>
        <div id="netprofitweekselect">
            <?=$weekselect?>
        </div>
        <div class="radio_button">
            <input type="radio" id="amount_profit" value="amount" name="radio_profit" checked> Amnt
            <input type="radio" id="percent_profit" value="percent" name="radio_profit"> % Only
        </div>
        <div class="but-detailed" style="margin-left: 20px;">
            <select id="but-detailed" name="but-detailed" class="netreportviewselect">
                <option value="Detailed">Detailed</option>
                <option value="Condensed">Condensed</option>
            </select>
        </div>
    </div>
    <div class="table_netprofit">
        <?=$title?>
    </div>
    <div class="chartdataarea">
        <?=$chartdata_view?>
        <div class="comparevaluesarea">
            <div class="comparedatatitle">Statistics for <?=$cur_year?>:</div>
            <div class="comparedatasubtitle">Sales: <?=date('M j', $cur_start)?> - <?=date('M j', $cur_end)?></div>
            <div class="comparedatasubtitle">Expenses: <?=date('M j', $cur_start)?> - <?=date('M j', $cur_end)?></div>
            <div class="comparedataarea">
                <div class="weektotalsheadrow">
                    Compare to
                </div>
                <div class="weektotalsheadrow">
                    <select class="selectcompareyears">
                        <?php foreach ($years as $row) {?>
                            <option value="<?=$row?>"><?=$row?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="comptabledataarea">
                    <?=$pacegrow_view?>
                </div>
            </div>
        </div>
    </div>
    <div class="chartdataarea">
        <div class="netprofitchartsarea" style="width: 620px;">
            <div style="height: 420px; width: 580px;" id="curve_chart" class="grapharea"></div>
            <div style="height: 420px; width: 600px;" id="curve_chart_effinciency" class="grapharea"></div>
        </div>
        <div class="w9totalsarea"><?=$w9purchase?></div>
    </div>
</div>
<input type="hidden" id="netprofitviewbrand" value="<?=$brand?>">
<div id="netprofitviewbrandmenu">
    <?=$top_menu?>
</div>
