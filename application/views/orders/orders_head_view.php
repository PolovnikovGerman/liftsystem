<input type="hidden" id="totalleadorders" value="<?=$total?>"/>
<input type="hidden" id="leadorderpage" value="0"/>
<input type="hidden" id="leadorderactivate" value="<?=$activesearch?>"/>
<input type="hidden" id="ordersviewbrand" value="<?=$brand?>"/>
<div class="leadord_headarea">
    <div class="leadord_headrow">
        <div class="leadord_search">
            <img src="/img/icons/magnifier.png"/>
            <input class="leadord_searchdata" value="<?=$search?>" placeholder="Enter order #, customer, email, Cust PO #"/>
            <div class="leadorder_findall">&nbsp;</div>
            <div class="leadorder_clear">&nbsp;</div>
        </div>
        <div class="leadord_emptyspace" style="width: 15%">&nbsp;</div>
        <div class="leadord_filters">
            <div>Display: </div>
            <select class="leadord_filterselect usrreplica">
                <option value="-1" <?=($current_user==-1 ? 'selected="selected"' : '')?>>Website Only</option>
                <?php foreach ($users as $urow) {?>
                    <option value="<?=($urow['user_id'])?>" <?=($urow['user_id']==$current_user ? 'selected="selected"' : '')?>>
                        <?=($urow['user_leadname']=='' ? $urow['user_name'] : $urow['user_leadname'])?> Only
                    </option>
                <?php } ?>
                <option value="0">Unassigned</option>
                <option value="-2" <?=($current_user==-2 ? 'selected="selected"' : '')?>>View All</option>
            </select>
            <select class="leadord_filterselect perpage" id="leadorderperpage">
                <?php foreach ($perpage as $row) { ?>
                    <option value="<?=$row?>" <?=$row==$default_perpage ? 'selected' : ''?>><?=$row?> records/per page</option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="leadord_headrow">
        <div class="lead_neworder"><img src="/img/leads/addnew_btn.png" alt="Add"/></div>
        <div class="lead_orderlegendarea">
            <div class="lead_orderlegend">
                <img src="/img/leads/legend_proj.png"/>
                <div class="lead_orderlegendlabel">PROJ</div>
            </div>
            <div class="lead_orderlegend long">
                <img src="/img/leads/legend_lose.png"/>
                <div class="lead_orderlegendlabel long">Lose $$</div>
            </div>
            <div class="lead_orderlegend long">
                <img src="/img/leads/legend_verybad.png"/>
                <div class="lead_orderlegendlabel long">Very Bad</div>
            </div>
            <div class="lead_orderlegend">
                <img src="/img/leads/legend_bad.png"/>
                <div class="lead_orderlegendlabel">Bad</div>
            </div>
            <div class="lead_orderlegend long">
                <img src="/img/leads/legend_bellowavg.png"/>
                <div class="lead_orderlegendlabel long">Below Avg</div>
            </div>
            <div class="lead_orderlegend">
                <img src="/img/leads/legend_target.png"/>
                <div class="lead_orderlegendlabel">Target</div>
            </div>
            <div class="lead_orderlegend">
                <img src="/img/leads/legend_great.png"/>
                <div class="lead_orderlegendlabel">Great</div>
            </div>
        </div>
        <div class="leadorder_pagination"></div>
    </div>
    <div class="leadorder_datahead <?=$brand=='SR' ? 'relievers' : ''?>">
        <div class="date">Date</div>
        <div class="ordernum">Order #</div>
        <div class="confirmnum">Conf #</div>
        <?php if ($brand=='SR') { ?>
            <div class="customerponumber">Cust PO #</div>
        <?php } ?>
        <div class="customer">Customer</div>
        <div class="qty">Qty</div>
        <div class="itemcolor">Color</div>
        <div class="item">Item</div>
        <div class="revenue">Revenue</div>
        <div class="balance">Balance</div>
        <div class="usrrepl">Sales Rep</div>
        <div class="ordclass">Class</div>
        <div class="artstage">Art Status</div>
        <div class="points">Points</div>
        <div class="ordstatus">Fulfilled</div>
        <!-- Status -->
    </div>
    <div class="leadorder_dataarea <?=$brand=='SR' ? 'relievers' : ''?>">&nbsp;</div>
</div>
<div class="ordertraciknglegend">
    <div class="datarow">
        <div class="trackinglegendarea" style="width: 37%">
            <div class="trackinglabelarea ontimelabel"><i class="fa fa-square"></i></div>
            <div class="trackinglabeltitle">On Time</div>
        </div>
        <div class="trackinglegendarea" style="width: 63%">
            <div class="trackinglabelarea todaytimelabel"><i class="fa fa-square"></i></div>
            <div class="trackinglabeltitle">Today</div>
        </div>
    </div>
    <div class="datarow">
        <div class="trackinglegendarea" style="width: 37%">
            <div class="trackinglabelarea latelabel"><i class="fa fa-square "></i></div>
            <div class="trackinglabeltitle">Late</div>
        </div>
        <div class="trackinglegendarea" style="width: 63%">
            <div class="trackinglabelarea completedlabel"><i class="fa fa-square-o"></i></div>
            <div class="trackinglabeltitle">100% completed</div>
        </div>
    </div>
</div>