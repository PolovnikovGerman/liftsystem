<input type="hidden" value="<?=$limitrow?>" id="limitweekshow"/>
<input type="hidden" id="netprofitviewbrand" value="<?=$brand?>">
<input type="hidden" id="netprofitviewtype" value="amount"/>
<div class="netprofitviewarea">
    <div class="netprofitviewtitle">
        <div class="datarow">
            <div class="netprofitheadoptionlabel">Sort by</div>
            <div class="netprofitsortselect">
                <select id="netreportsortorder">
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
            <div class="netprofitheadoptionlabel">
                From:
            </div>
            <div class="netprofitweekselect">
                <select id="weekselectfrom">
                    <option value=""></option>
                    <?php foreach ($weeklists as $weeklist) { ?>
                        <option value="<?=$weeklist['id']?>"><?=$weeklist['label']?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="netprofitheadoptionlabel">
                Until:
            </div>
            <div class="netprofitweekselect">
                <select id="weekselectuntil">
                    <option value=""></option>
                    <?php foreach ($weeklists as $weeklist) { ?>
                        <option value="<?=$weeklist['id']?>"><?=$weeklist['label']?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="netprofitheadocheck first active">
                <i class="fa fa-check-circle-o" aria-hidden="true"></i>
            </div>
            <div class="netprofitheadochecklabel">Amnt</div>
            <div class="netprofitheadocheck">
                <i class="fa fa-circle-o" aria-hidden="true"></i>
            </div>
            <div class="netprofitheadochecklabel">Amnt</div>
            <div class="netprofitviewdataselect">
                <select>
                    <option value="detail">Detailed</option>
                </select>
            </div>
        </div>
        <div class="datarow">
            <div class="netprofitsalesdatalabel">Sales Data is for All Brands</div>
        </div>
        <div class="datarow">
            <div class="netprofit-table-head">
                <div class="weekname">Week</div>
                <div class="sales">Sales</div>
                <div class="revenue">Revenue</div>
                <div class="grossprofit">Gross Profit</div>
                <div class="profitperc">%</div>
                <div class="operating">Operating</div>
                <div class="ads">Ads</div>
                <div class="payroll">Payroll</div>
                <div class="upwork">Upwork</div>
                <div class="w9work">W9 Work</div>
                <div class="discretionary">Discretionary</div>
                <div class="totalcost">Total Cost</div>
                <div class="totalcostperc">%</div>
                <div class="netprofit">Net Profit</div>
                <div class="netprofitperc">%</div>
                <div class="rightpart invest">Invest</div>
                <div class="rightpart investperc">%</div>
                <div class="rightpart od">OD</div>
                <div class="rightpart odperc">%</div>
                <div class="rightpart retained">Retained</div>
                <div class="rightpart retainedperc">%</div>
                <div class="rightpart emptyspace">&nbsp;</div>
            </div>
        </div>
        <div class="datarow">
            <div class="netprofit-running"></div>
        </div>
    </div>
    <div class="netprofitviewdata"></div>
    <div class="expandnetprofittableview">+ Expand to 26 weeks</div>
    <div class="collapsenetprofittableview">- Collapse to 13 weeks</div>
    <div class="netprofitchartdata">
        <div class="netprofitchartdata_title">
            <div class="netprofitheadoptionlabel">Brand</div>
            <div class="netprofitbrandselect">
                <select>
                    <option>Both Brands</option>
                </select>
            </div>
            <div class="netprofitheadoptionlabel compareyear">Compare</div>
            <div class="netprofitperiodselect">
                <select>
                    <option>Full Years</option>
                </select>
            </div>
        </div>
        <div class="weektotalsdataarea">
        </div>
    </div>
    <div class="netprofitexpensesarea">
    </div>
</div>