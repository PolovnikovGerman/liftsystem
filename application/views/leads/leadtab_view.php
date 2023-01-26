<!-- Init values -->
<input type="hidden" id="totallead" value="<?=$totalrec?>"/>
<input type="hidden" id="perpagelead" value="<?=$perpage?>"/>
<input type="hidden" id="curpagelead" value="<?=$curpage?>"/>
<input type="hidden" id="showfuturereport" value="0"/>
<input type="hidden" id="totalcuryearorders" value="<?=$totalorders?>"/>
<input type="hidden" id="leadsveiwbrand" value="<?=$brand?>"/>
<div class="leads_content">
    <!-- Search page -->
    <div class="leads_headarea">
        <div class="leads_headrow">
            <div class="leads_add">&nbsp;</div>
            <div class="leads_selectreplarea">
                <select class="leads_sortselect leads_replica"  id="leads_replica">
                    <?php if ($user_role=='masteradmin') { ?>
                        <option value="">All Sales Reps</option>
                        <?php foreach ($replicas as $row) {?>
                            <option value="<?=$row['user_id']?>" <?=($row['user_id']==$user_id ? 'selected="selected"' : '')?> ><?=$row['user_name']?></option>
                        <?php } ?>
                    <?php } else { ?>
                        <?php foreach ($replicas as $row) {?>
                            <?php if ($row['user_id']==$user_id) { ?>
                                <option value="<?=$row['user_id']?>" <?=($row['user_id']==$user_id ? 'selected="selected"' : '')?> ><?=$row['user_name']?></option>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                </select>
            </div>
            <div class="leads_legends">
                <div class="lead_legend">
                    <div class="lead_legmapimg">
                        <img src="/img/leads/kvadrat-white.png" alt="White"/>
                    </div>
                    <div class="lead_legend_text">Open</div>
                </div>
                <div class="lead_legend">
                    <div class="lead_legmapimg">
                        <img src="/img/leads/kvadrat-blue.png" alt="Blue"/>
                    </div>
                    <div class="lead_legend_text">Closed</div>
                </div>
                <div class="lead_legend">
                    <div class="lead_legmapimg">
                        <img src="/img/leads/kvadrat-pink.png" alt="Blue"/>
                    </div>
                    <div class="lead_legend_text">Dead</div>
                </div>
            </div>
            <div class="leads_sortings">
                <select id="sortprior" class="leads_sortselect sortprior">
                    <option value="1" selected="selected">Open, Priority & Soon</option>
                    <option value="6">Ordering Soon Only</option>
                    <option value="2">Priority Only</option>
                    <option value="3">Closed Only</option>
                    <option value="4">Dead Only</option>
                    <option value="">View All Leads</option>
                </select>
                <select id="sorttime" class="leads_sortselect sorttime">
                    <option value="1">Last Updated</option>
                    <option value="2">When Created</option>
                </select>
            </div>
        </div>
        <div class="leads_headrow">
            <div class="lead_datasearch">
                <img src="/img/icons/magnifier.png"/>
                <input type="text" class="lead_searchinput" value="" placeholder="Enter Lead #, customer, item ..."/>
                <div class="leadsearchall">&nbsp;</div>
                <div class="leadsearchusr"><?=$user_name?>&apos;s</div>
                <div class="leadsearchclear">&nbsp;</div>
            </div>
            <div class="leadlist_pagination">&nbsp;</div>
        </div>
    </div>
    <div class="lead_dataareas">
        <div class="lead_dataarealeft">
            <div class="lead_listdata_head">
                <div class="leadnumber">Lead #</div>
                <div class="leaddate">Date</div>
                <div class="leadvalue">Value</div>
                <div class="leadcustomer">Customer</div>
                <div class="leadqty">QTY</div>
                <div class="leaditem">Item</div>
                <div class="leadrep">Rep</div>
            </div>
            <div class="lead_listdata">&nbsp;</div>
        </div>
        <div class="lead_dataarearight">
            <div class="leadclosed_label">% Leads Closed:</div>
            <div id="leadclosedtotalarea"><?=$right_content?></div>
            <div id="leadcloseddataarea">&nbsp;</div>
        </div>
    </div>
</div>
