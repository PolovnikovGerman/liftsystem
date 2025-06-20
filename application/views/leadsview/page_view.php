<input type="hidden" id="leadviewbrand" value="<?=$brand?>"/>
<input type="hidden" id="leadviewperpage" value="<?=$perpage?>"/>
<input type="hidden" id="leadviewshowclosed" value="0"/>
<input type="hidden" id="leadviewcurpage" value="<?=$curpage?>"/>
<input type="hidden" id="leadviewuser" value="<?=$user_id?>"/>
<input type="hidden" id="leadviewtotalrec" value="<?=$totalrec?>"/>
<input type="hidden" id="leaddatasort" value="1"/>
<input type="hidden" id="leadpriorsort" value="1"/>
<input type="hidden" id="leadtasksort" value="1"/>
<input type="hidden" id="leadnewleadsort" value="1"/>
<input type="hidden" id="ordermissinfosort" value="1"/>
<div class="leads_content">
    <div class="leads_headarea">
        <div class="leads_headrow mainleadheader">
            <div class="leads_add">New Lead</div>
            <div class="lead_datasearch">
                <img src="/img/icons/magnifier.png"/>
                <input type="text" class="lead_searchinput" name="leadsearchtemplate" value="" placeholder="Enter Lead #, customer, item ..."/>
                <?php if ($user_role=='masteradmin') { ?>
                    <div class="leadgreybtn leadsearchall">Search All</div>
                <?php } ?>
                <div class="leadgreybtn leadsearchusr" data-user="<?=$user_id?>"><?=$user_name?>&apos;s</div>
                <div class="leadgreybtn leadsearchclear">Clear</div>
            </div>
            <div class="leads_selectreplarea">
                <label for="leads_replica">View Leads of:</label>
                <select class="leads_sortselect leads_replica"  id="leads_replica">
                    <?php if ($user_role=='masteradmin') : ?>
                        <option value="">All Sales Reps</option>
                    <?php endif; ?>
                    <?php $curstatus = $replicas[0]['user_status'];?>
                    <?php foreach ($replicas as $row) :?>
                        <?php if ($row['user_status']!==$curstatus) : ?>
                            <option disabled>-----------</option>
                            <?php $curstatus=$row['user_status'];?>
                        <?php endif; ?>
                        <option value="<?=$row['user_id']?>" <?=($row['user_id']==$user_id ? 'selected="selected"' : '')?> ><?=$row['user_name']?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="leads_viewclosedfilter">
                <div class="leads_viewclosedflag">
                    <i class="fa fa-square-o" aria-hidden="true"></i>
                </div>
                <div class="leads_viewclosedlabel">View Closed & Dead</div>
            </div>
        </div>
        <div class="leads_headrow">
            <div class="leads_section_area">
                <div class="leaddata_header newleads">
                    <div class="leaddata_label">New Leads:</div>
                </div>
                <div class="leaddata_subheader">
                    <div class="leadnumber_head">Lead #</div>
                    <div class="leadcustomer_head">Customer</div>
                    <div class="leadqty_head">QTY</div>
                    <div class="leaditem_head">Item</div>
                </div>
                <div class="leaddataarea newleads" id="newleadslistdata"></div>
            </div>
            <div class="leads_section_area">
                <div class="leaddata_header tasks">
                    <div class="leaddata_label">Tasks:</div>
                </div>
                <div class="leaddata_subheader">
                    <div class="leadnumber_head">Lead #</div>
                    <div class="leadcustomer_head">Customer</div>
                    <div class="leadqty_head">QTY</div>
                    <div class="leaditem_head">Item</div>
                </div>
                <div class="leaddataarea tasks" id="leadstasksdata"></div>
            </div>
            <div class="leads_section_area">
                <div class="leaddata_header priority">
                    <div class="leaddata_label">Priority:</div>
                </div>
                <div class="leaddata_subheader">
                    <div class="leadnumber_head">Lead #</div>
                    <div class="leadcustomer_head">Customer</div>
                    <div class="leadqty_head">QTY</div>
                    <div class="leaditem_head">Item</div>
                </div>
                <div class="leaddataarea priority" id="leadsprioritydata"></div>
                <div class="leaddata_header ordermissinfo">
                    <div class="leaddata_label">Orders Missing Info:</div>
                </div>
                <div class="leaddata_subheader">
                    <div class="leadnumber_head">Lead #</div>
                    <div class="leadcustomer_head">Customer</div>
                    <div class="leadqty_head">QTY</div>
                    <div class="leaditem_head">Item</div>
                </div>
                <div class="leaddataarea ordermissinfo" id="ordermissinfodata"></div>
            </div>
            <div class="leads_section_area lastsection">
                <div class="leaddata_header leads">
                    <div class="leaddata_label">Leads:</div>
                    <div class="leadsdata_sorting_area">
                        <div class="leadsdata_sorting">
                            <div class="leadsort_updatedate leads">
                                <i class="fa fa-check-circle-o" aria-hidden="true"></i>
                            </div>
                            <div class="leadsort_label">Last Updated</div>
                        </div>
                        <div class="leadsdata_sorting">
                            <div class="leadsort_createdate leads">
                                <i class="fa fa-circle-thin" aria-hidden="true"></i>
                            </div>
                            <div class="leadsort_label">Date Created</div>
                        </div>
                    </div>
                </div>
                <div class="leaddata_subheader">
                    <div class="leadnumber_head">Lead #</div>
                    <div class="leadcustomer_head">Customer</div>
                    <div class="leadqty_head">QTY</div>
                    <div class="leaditem_head">Item</div>
                </div>
                <div class="leaddataarea leads" id="leadslistdata"></div>
            </div>
        </div>
    </div>
</div>