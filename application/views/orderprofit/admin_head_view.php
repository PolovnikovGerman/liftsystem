<div class="profitorder-content">
    <input type="hidden" id="orderbytab1" value="<?=$order?>"/>
    <input type="hidden" id="directiontab1" value="<?=$direc?>"/>
    <input type="hidden" id="totaltab1" value="<?=$total?>"/>
    <input type="hidden" id="curpagetab1" value="<?=$curpage;?>"/>
    <div class="profitorder-head-row">
        <div class="legend"><?=$legend?></div>
        <div>
            <select id="order_filtr" class="profitorder-filtrer">
                <option value="0">Display All</option>
                <option value="1">Display Projected Only</option>
                <option value="2">Green Profit Only</option>
                <option value="3">White Profit Only</option>
                <option value="4">Orange Profit Only</option>
                <option value="5">Red Profit Only</option>
                <option value="8">Maroon Profit Only</option>
                <option value="6">Black Profit Only</option>
                <option value="7">Canceled Orders Only</option>
            </select>
        </div>
        <div><?=$perpage_view?></div>
    </div>
    <div class="profitorder-head-row">
        <div class="searchformprofitord">
            <div>
                <input type="text" id="profitsearch" class="search_input" placeholder="Enter order #, amount, customer"/>
            </div>
            <div class="find_id" id="find_profit">Search It</div>
            <div class="find_id" id="clear_profit">Clear</div>
        </div>
        <div class="accounting_pagination" id="profitorders_pagination"></div>
    </div>
    <div class="profitorder-head-row">
        <div class="profit_filterdate_area <?= $adminview == 1 ? 'adminview' : '' ?>">
            <div class="labeltxt">Display:</div>
            <div class="checkpint">
                <input type="radio" id="profitdatetypechoise1" name="profitdatetypechoise" value="yearcheck"
                       checked="checked"/>
            </div>
            <div class="selectorderyear">
                <select class="selectorderyeardat active">
                    <?php foreach ($years as $year) { ?>
                        <option value="<?= $year['key'] ?>"><?= $year['label'] ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="selectordermonth">
                <select class="selectordermonthdat active">
                    <option value="" selected="selected">All Months</option>
                    <?php for ($i = 1; $i < 13; $i++) { ?>
                        <option value="<?= $i ?>"><?= date('F', mktime(0, 0, 0, $i, 10)); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="checkpint">
                <input type="radio" id="profitdatetypechoise2" name="profitdatetypechoise" value="datecheck">
            </div>
            <div class="dateslabel">
                Custom
            </div>
            <div class="datesinpt">
                <input type="text" class="profitorder_dateinpt" id="customdatebgn" autocomplete="off" disabled="disabled"/>
            </div>
            <div class="dateslabelto">to</div>
            <div class="datesinpt">
                <input type="text" class="profitorder_dateinpt" id="customdateend" autocomplete="off" disabled="disabled"/>
            </div>
            <div class="selectshiplocation">
                <select class="selectshiplocationdat">
                    <option value="0" selected="selected">All Locations</option>
                    <option value="223">United States</option>
                    <option value="39">Canada</option>
                    <option value="-1">Other Country</option>
                </select>
            </div>
            <div class="selectstatelocation">&nbsp;</div>
            <div class="selectordertypes">
                <select class="selectordertypesdat">
                    <option value="">All Orders</option>
                    <option value="new">New Only</option>
                    <option value="repeat">Repeat Only</option>
                </select>
            </div>
            <?php if ($adminview == 1) { ?>
                <div class="exportdatacall">&nbsp;</div>
            <?php } ?>
        </div>
    </div>
    <div class="profitorder-head-row">
        <div id="profittotals_title" class="totalallrow-title">&nbsp;</div>
        <div class="orders-table">
            <div class="profitorder_addaction">
                <a id="addnew" href="javascript:void(0);" class="searchbtn">
                    <img src="/img/leads/addnew_btn.png"/>
                </a>
            </div>
            <div id="orders-total-row" style="float: right!important;"></div>
        </div>
    </div>
    <div class="orders-table-title">
        <div class="profitorder_date">Date</div>
        <div class="profitorder_action">&nbsp;</div>
        <div class="profitorder_numorder activesortdesc">Order #</div>
        <div class="profitorder_confirm">Conf #</div>
        <div class="profitorder_customer">Customer</div>
        <div class="profitorder_qty">QTY</div>
        <div class="profitorder_item">Item</div>
        <div class="profitorder_color">Color</div>
        <div class="profitorder_revenue">Revenue</div>
        <div class="profitorder_shipping">Shipping</div>
        <div class="profitorder_shipdate">Date</div>
        <div class="profitorder_tax">Tax</div>
        <div class="profitorder_cog">COG</div>
        <div class="profitorder_profit">PROFIT</div>
        <div class="profitorder_profitperc">%</div>
    </div>
    <div class="orderprofitcontent-table" id="tableinfotab1"></div>
    <!-- FOOTER - Admin AREA -->
    <div id="ordertotalscntarea">
        <div class="orders_totals_table">
            <!-- <div class="orders_totals_row"> -->
            <div class="date">&nbsp;</div>
            <?php for ($i = 0; $i <= 6; $i++) { ?>
                <div class="order">
                    <div class="label">Order</div>
                    <div class="labelqty">QTY</div>
                    <div class="labelperc">%</div>
                </div>
                <div class="revenue">Revenue</div>
                <div class="profit">Profit</div>
            <?php } ?>
            <!--</div> -->
        </div>
        <div class="profitordertotalsarea">
            <?= $bottom_view ?>
        </div>
    </div>

</div>
<input type="hidden" id="profitordersbrand" value="<?=$brand?>">
<?=$top_menu?>

