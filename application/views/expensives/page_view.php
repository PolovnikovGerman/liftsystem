<input type="hidden" id="expensivesviewbrand" value="<?=$brand?>">
<div class="expensivesviewarea">
    <div class="datarow">
        <div class="expensivesviewtitle">
            Reoccurring Expense Chart
        </div>
        <div class="expensivesview-addnewbtn">
            <i class="fa fa-plus"></i>
            New
        </div>
        <div class="expensivesviewsort">
            <span>Sort by:</span>
            <select id="expensivesviewsort">
                <option value="perc_desc">% &#9660;</option>
                <option value="perc_asc">% &#9650;</option>
                <option value="date_desc">Day / date &#9660;</option>
                <option value="date_asc">Day / date &#9650;</option>
                <option value="method_desc">Method &#9660;</option>
                <option value="method_asc">Method &#9650;</option>
            </select>
        </div>
    </div>
    <div class="datarow">
        <div class="expensivesviewtablehead">
            <div class="expensive-deeds">&nbsp;</div>
            <div class="expensive-annually">Annually</div>
            <div class="expensive-monthly">Monthly</div>
            <div class="expensive-weekly">Weekly</div>
            <div class="expensive-date">Date/ Day</div>
            <div class="expensive-method">Method</div>
            <div class="expensive-description">Description</div>
            <div class="expensive-quoter">4 / 12</div>
            <div class="expensive-yearly">Yearly</div>
            <div class="expensive-percent">%</div>
        </div>
    </div>
    <div id="expensivesviewtable"></div>
    <div class="datarow">
        <div class="expensivesviewfooter">
            <div class="expensive-grandtotal">Grand Total:</div>
            <div class="expensive-quoter">
                <span>4/12 Total:</span>
                <div class="expensive-totalamount" id="expanse-quoter-total"></div>
            </div>
            <div class="expensive-yearly">
                <span>Annual Total:</span>
                <div class="expensive-totalamount" id="expanse-year-total"></div>
            </div>
            <div class="expensive-percent">
                <span>Monthly Total:</span>
                <div class="expensive-totalamount" id="expanse-month-total"></div>
            </div>

        </div>
    </div>
</div>