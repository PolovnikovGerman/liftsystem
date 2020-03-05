<!-- <input type="hidden" id="perpagetab1" value="<?=$perpage?>"/> -->
<input type="hidden" id="orderbytab1" value="<?=$order?>"/>
<input type="hidden" id="directiontab1" value="<?=$direc?>"/>
<input type="hidden" id="totaltab1" value="<?=$total?>"/>
<input type="hidden" id="curpagetab1" value="<?=$curpage;?>"/>
<div class="profitorder-head">
    <div class="legend">
        <?=$legend?>
    </div>
    <div class="pagesviews">
        <div class="order_filtr">
            <select id="order_filtr" name="order_filtr" class="order_filtr_select">
                <option value="0">Display All</option>
                <option value="1">Display Projected Only</option>
                <option value="2">Green Profit Only</option>
                <option value="3">White Profit Only</option>
                <option value="4">Orange Profit Only</option>
                <option value="5">Red Profit Only</option>
                <option value="8">Maroon Profit Only</option>
                <option value="6">Black Profit Only</option>
            </select>
        </div>
        <div class="orderprof_selectperpage"><?=$perpage_view?></div>

    </div>
    <div class="proforder_adminsearch">
        <div class="searchformprofitord">
            <?=$searchform?>
        </div>
        <div class="proford_pagination">&nbsp;</div>
    </div>
</div>
<div class="orders-table">
    <div class="orders-total-row" id="orders-total-row">
        <?=$total_row?>
    </div>
    <div class="orders-table-title">
        <div class="profitorder_action">
            <a id="addnew" href="javascript:void(0);" class="searchbtn"><em></em><span>add new</span><b></b></a>
        </div>
        <div class="profitorder_date">Date</div>
        <div class="profitorder_email">Email</div>
        <div class="profitorder_numorder activesortdesc">Order #</div>
        <div class="profitorder_customer">Name</div>
        <div class="profitorder_revenue">Revenue</div>
        <div class="profitorder_shipping">Shipping</div>
        <div class="profitorder_tax">Sales Tax</div>
        <div class="profitorder_othercost">CC</div>
        <div class="profitorder_cog">COG</div>
        <div class="profitorder_profit">PROFIT</div>
        <div class="profitorder_profitperc">%</div>
    </div>
    <div class="orderprofitcontent-table" id="tableinfotab1"></div>
</div>
<div id="editcog" style="display:none;clear: both; float: left; width:220px; height: 220px">
    <div class="editcogform" style="float: left; width: 206px; height: 140px; background-color: #FFFFFF;">

    </div>
</div>