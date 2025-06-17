<input type="hidden" id="leadviewbrand" value="<?=$brand?>"/>
<input type="hidden" id="leadviewperpage" value="<?=$perpage?>"/>
<input type="hidden" id="leadviewshowclosed" value="0"/>
<input type="hidden" id="leadviewcurpage" value="<?=$curpage?>"/>
<input type="hidden" id="leadviewuser" value="<?=$user_id?>"/>
<input type="hidden" id="leadviewtotalrec" value="<?=$totalrec?>"/>
<div class="leads_content">
    <div class="leads_headarea">
        <div class="leads_headrow">
            <div class="leads_left_partarea">
                <div class="leads_add">New Lead</div>
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
            </div>
            <div class="leads_center_partarea">
                <div class="lead_datasearch">
                    <img src="/img/icons/magnifier.png"/>
                    <input type="text" class="lead_searchinput" name="leadsearchtemplate" value="" placeholder="Enter Lead #, customer, item ..."/>
                    <?php if ($user_role=='masteradmin') { ?>
                        <div class="leadgreybtn leadsearchall">Search All</div>
                    <?php } ?>
                    <div class="leadgreybtn leadsearchusr"><?=$user_name?>&apos;s</div>
                    <div class="leadgreybtn leadsearchclear">Clear</div>
                </div>
            </div>
            <div class="leads_right_partarea">
                <div class="leads_viewclosedfilter">
                    <div class="leads_viewclosedflag">
                        <i class="fa fa-square-o" aria-hidden="true"></i>
                        <!-- <i class="fa fa-check-square-o" aria-hidden="true"></i> -->
                    </div>
                    <div class="leads_viewclosedlabel">View Closed & Dead</div>
                </div>
            </div>
        </div>
        <div class="leads_headrow">
            <div class="leads_left_partarea">
                <div class="pagination" id="mainleadpagination"></div>
            </div>
            <div class="leads_center_partarea">
                <div class="pagination" id="priorityleadpagination"></div>
            </div>
            <div class="leads_right_partarea">
                <div class="pagination" id="taskleadpagination"></div>
            </div>
        </div>
        <div class="leads_headrow">
            <div class="leads_left_partarea">
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
                    <div class="leadrepl_head">Rep</div>
                </div>
                <div class="leaddataarea leads" id="leadslistdata"></div>
            </div>
        </div>
    </div>
</div>