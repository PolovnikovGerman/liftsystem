<input type="hidden" id="leadviewbrand" value="<?=$brand?>"/>
<input type="hidden" id="leadviewremindcustom" value="0"/>
<input type="hidden" id="leadviewremindrichy" value="0"/>
<input type="hidden" id="leadviewremindmonth" value="<?=$month?>"/>
<input type="hidden" id="leadviewperpage" value="<?=$perpage?>"/>
<input type="hidden" id="leadviewcurpage" value="<?=$curpage?>"/>
<input type="hidden" id="leadviewuser" value="<?=$user_id?>"/>
<input type="hidden" id="leadviewtotalrec" value="<?=$totalrec?>"/>
<input type="hidden" id="leadviewshowclosed" value="0"/>
<input type="hidden" id="leaddatasort" value="1"/>
<input type="hidden" id="leadpriorsort" value="1"/>

<div class="leads_content">
    <div class="leads_content_left">
        <div class="newunassign_header">NEW / Unassigned Interest</div>
        <div class="newunassign_taskheader emptycontent" data-task="sbcustomform">
            <div class="newunassign_tasks_label">Custom SB Form:</div>
            <div class="newunassign_tasks_total" data-task="sbcustomform">0 New</div>
        </div>
        <div class="newunassign_tasksubheader emptycontent" data-task="sbcustomform">
            <div class="newunassign_date">Date</div>
            <div class="sbcustomform_customer">Customer</div>
            <div class="sbcustomform_qty">QTY</div>
            <div class="sbcustomform_item">Item</div>
        </div>
        <div class="newunassign_tasktable emptycontent" id="sbcustomformstable">
            <div class="datarow whitedatarow">
                <div class="newunassign_emptydata">No new records</div>
            </div>
        </div>
        <div class="newunassign_taskheader emptycontent" data-task="webquestions">
            <div class="newunassign_tasks_label">Web Questions:</div>
            <div class="newunassign_tasks_total" data-task="webquestions">0 New</div>
        </div>
        <div class="newunassign_tasksubheader emptycontent" data-task="webquestions">
            <div class="newunassign_date">Date</div>
            <div class="webquestion_email">Email</div>
            <div class="webquestion_webpage">Webpage</div>
            <div class="webquestion_message">Message</div>
        </div>
        <div class="newunassign_tasktable emptycontent" id="webquestiontable">
            <div class="datarow whitedatarow">
                <div class="newunassign_emptydata">No new records</div>
            </div>
        </div>
        <div class="newunassign_taskheader emptycontent" data-task="onlinequotes">
            <div class="newunassign_tasks_label">Online Quotes:</div>
            <div class="newunassign_tasks_total" data-task="onlinequotes">0 New</div>
        </div>
        <div class="newunassign_tasksubheader emptycontent" data-task="onlinequotes">
            <div class="newunassign_date">Date</div>
            <div class="onlinequotes_customer">Customer</div>
            <div class="onlinequotes_qty">QTY</div>
            <div class="onlinequotes_item">Item</div>
        </div>
        <div class="newunassign_tasktable emptycontent" id="onlinequotetable">
            <div class="datarow whitedatarow">
                <div class="newunassign_emptydata">No new records</div>
            </div>
        </div>
        <div class="newunassign_taskheader emptycontent" data-task="proofrequests">
            <div class="newunassign_tasks_label">Proof Requests:</div>
            <div class="newunassign_tasks_total" data-task="proofrequests">0 New</div>
        </div>
        <div class="newunassign_tasksubheader emptycontent" data-task="proofrequests">
            <div class="newunassign_date">Date</div>
            <div class="proofrequests_customer">Customer</div>
            <div class="proofrequests_qty">QTY</div>
            <div class="proofrequests_item">Item</div>
        </div>
        <div class="newunassign_tasktable emptycontent" id="proofrequesttable">
            <div class="datarow whitedatarow">
                <div class="newunassign_emptydata">No new records</div>
            </div>
        </div>
        <div class="repeatremandheader emptycontent">
            <div class="repeatremand_label">Repeat Reminders:</div>
            <div class="repeatremand_filter">
                <div class="repeatremand_filter_check" data-filtr="revenue"><i class="fa fa-square-o" aria-hidden="true"></i></div>
                <div class="repeatremand_filter_label">$1000+ Only</div>
            </div>
            <div class="repeatremand_filter">
                <div class="repeatremand_filter_check" data-filtr="custom"><i class="fa fa-square-o" aria-hidden="true"></i></div>
                <div class="repeatremand_filter_label">Custom SB Only</div>
            </div>
        </div>
        <div class="repeatremand_subheader emptycontent">
            <div class="repeatremand_hide">Hide</div>
            <div class="repeatremand_date">Date</div>
            <div class="repeatremand_order">Order #</div>
            <div class="repeatremand_customer">Customer</div>
            <div class="repeatremand_qty">QTY</div>
            <div class="repeatremand_item">Item</div>
        </div>
        <div class="newunassign_tasktable emptycontent" id="repeatremandtable">
            <div class="datarow whitedatarow">
                <div class="newunassign_emptydata">No new records</div>
            </div>
        </div>
    </div>
    <div class="leads_content_right">
        <div class="myleads_header">
            <div class="myleads_label">MY Leads</div>
            <div class="leads_selectreplarea">
                <label for="leads_replica">Leads of:</label>
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
            <div class="lead_datasearch">
                <img src="/img/icons/magnifier.png"/>
                <input type="text" class="lead_searchinput" name="leadsearchtemplate" value="" placeholder="Enter Lead #, customer, item ..."/>
                <?php if ($user_role=='masteradmin') { ?>
                    <div class="leadgreybtn leadsearchall">Search All</div>
                <?php } ?>
                <div class="leadgreybtn leadsearchusr" data-user="<?=$user_id?>"><?=$user_name?>&apos;s</div>
                <div class="leadgreybtn leadsearchclear">Clear</div>
            </div>
            <div class="leads_add">New Lead</div>
        </div>
        <div class="leads_section_area">
            <div class="leaddata_header leads">
                <div class="leaddata_label">All Leads:</div>
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
            <div class="datarow">
                <div class="paginationview" id="mainleadpagination"></div>
            </div>
        </div>
        <div class="leads_section_area lastsection">
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
                <div class="leaddata_label">Orders with Missing Info:</div>
            </div>
            <div class="leaddata_subheader">
                <div class="ordernumber_head">Order #</div>
                <div class="ordercustomer_head">Customer</div>
                <div class="ordershipaddr_head">ShipAddr</div>
                <div class="orderpayment_head">Payment</div>
                <div class="orderart_head">Art</div>
                <div class="orderapproval_head">Approval</div>
            </div>
            <div class="ordermissinfoarea" id="ordermissinfodata"></div>
        </div>
    </div>
</div>