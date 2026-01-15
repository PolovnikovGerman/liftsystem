<input type="hidden" id="nonart_sort" value="<?=$sort_need_art?>"/>
<input type="hidden" id="nonart_direc" value="<?=$direc_needart?>"/>
<input type="hidden" id="redraw_sort" value="<?=$sort_redraw?>"/>
<input type="hidden" id="redraw_direc" value="<?=$direc_redraw?>"/>
<input type="hidden" id="proof_sort" value="<?=$sort_proof?>"/>
<input type="hidden" id="proof_direc" value="<?=$direc_proof?>"/>
<input type="hidden" id="needapr_sort" value="<?=$sort_needapr?>"/>
<input type="hidden" id="needapr_direc" value="<?=$direc_needapr?>"/>
<input type="hidden" id="aproved_sort" value="<?=$sort_aproved?>"/>
<input type="hidden" id="aproved_direc" value="<?=$direc_aproved?>"/>
<input type="hidden" id="arttasksviewbrand" value="<?=$brand?>"/>
<input type="hidden" id="needplates_sort" value="<?=$sort_plates?>"/>
<input type="hidden" id="needplates_direc" value="<?=$direc_plates?>"/>
<div class="taskview_conteiner">
    <div class="taskview_manage">
        <div class="tasklist_search">
            <div class="tasksearchselect">
                <select class="tasksearch_select" id="tasksearchselect">
                    <option value="O">Orders</option>
                    <option value="R">Proof Requests</option>
                </select>
            </div>
            <div class="monitorsearch_form">
                <div class="monitorsearch_input">
                    <input placeholder="Enter order #, customer, email" class="monitorsearch_input" id="tasksearch"/>
                </div>
                <div class="search_action">
                    <div id="find_tasks" class="find_newbtn">Search It</div>
                    <div id="clear_tasks" class="find_newbtn">Clear</div>
                </div>
            </div>
        </div>
        <div class="ordproofinclude">
            <input type="checkbox" id="ordersproofs" value="1"/>
        </div>
        <div class="ordproofinclude_label">Show Requests</div>
        <div class="ordproofinclude">
            <input type="checkbox" id="viewallapproved" value="1"/>
        </div>
        <div class="ordproofinclude_label oldhide">Hide Over 120 Days Old</div>
        <div class="ordproofviewselect taskview">
            <select class="ordproofview" id="ordproofview">
                <option value="ordproof" selected="selected">Orders & Requests</option>
                <option value="orders">Orders Only</option>
                <option value="proofs">Requests Only</option>
            </select>
        </div>
    </div>
    <div class="taskview_keymaps">
        <div class="datarow">
            <div class="taskview_keymap_icon"><img src="/img/art/task_rushicon.png" alt="rush"/></div>
            <div class="taskview_keymap_label">Rush or Urgent</div>
        </div>
        <div class="datarow">
            <div class="taskview_keymap_icon"><i class="fa fa-diamond" aria-hidden="true"></i></div>
            <div class="taskview_keymap_label">Custom Shape</div>
        </div>
    </div>
    <div class="datarow taskview_datacontent">
        <div class="taskview_devstage">
            <div class="taskview_devstage_title" data-stage="nonart">Need Art</div>
            <div class="taskview_devstage_subtitle" id="noarttitle">
                <div class="taskview_rushtitle">&nbsp;</div>
                <div class="taskview_sortarea timesort sorttaskdesc">&nbsp;</div>
                <div class="taskview_timetitle sortactive">Time</div>
                <div class="taskview_sortarea ordersort">&nbsp;</div>
                <div class="taskview_ordertitle">Order</div>
                <div class="taskview_notetitle">&nbsp;</div>
            </div>
            <div class="taskview_devstage_data" id="dataneedartarea">&nbsp;</div>
        </div>
        <div class="taskview_devstage short">
            <div class="taskview_devstage_title" data-stage="redraw">Redrawing</div>
            <div class="taskview_devstage_subtitle" id="redrawtitle">
                <div class="taskview_rushtitle">&nbsp;</div>
                <div class="taskview_sortarea timesort sorttaskdesc">&nbsp;</div>
                <div class="taskview_timetitle sortactive">Time</div>
                <div class="taskview_sortarea ordersort">&nbsp;</div>
                <div class="taskview_ordertitle">Order</div>
            </div>
            <div class="taskview_devstage_data short" id="dataredrawnarea" >&nbsp;</div>
        </div>
        <div class="taskview_devstage taskstoproof">
            <div class="taskview_devstage_title" data-stage="toproof">To Proof</div>
            <div class="taskview_devstage_subtitle" id="prooftitle">
                <div class="taskview_rushtitle">&nbsp;</div>
                <div class="taskview_sortarea timesort sorttaskdesc">&nbsp;</div>
                <div class="taskview_timetitle sortactive">Time</div>
                <div class="taskview_sortarea ordersort">&nbsp;</div>
                <div class="taskview_ordertitle">Order</div>
                <div class="taskview_itemtitle">Item</div>
            </div>
            <div class="taskview_devstage_data" id="datatoproofarea">&nbsp;</div>
        </div>
        <div class="taskview_devstage">
            <div class="taskview_devstage_title" data-stage="needapprov">Need Approval</div>
            <div class="taskview_devstage_subtitle" id="needaprtitle">
                <div class="taskview_rushtitle">&nbsp;</div>
                <div class="taskview_sortarea timesort sorttaskdesc">&nbsp;</div>
                <div class="taskview_timetitle sortactive">Time</div>
                <div class="taskview_sortarea ordersort">&nbsp;</div>
                <div class="taskview_ordertitle">Order</div>
                <div class="taskview_notetitle">&nbsp;</div>
            </div>
            <div class="taskview_devstage_data" id="dataneedaprarea">&nbsp;</div>
        </div>
        <div class="taskview_devstage justapproved">
            <div class="taskview_devstage_title" data-stage="approved">Just Approved</div>
            <div class="taskview_devstage_subtitle" id="aprovedtitle">
                <div class="taskview_rushtitle">&nbsp;</div>
                <div class="taskview_sortarea timesort sorttaskasc">&nbsp;</div>
                <div class="taskview_timetitle sortactive">Time</div>
                <div class="taskview_sortarea ordersort">&nbsp;</div>
                <div class="taskview_ordertitle">Order</div>
            </div>
            <div class="taskview_devstage_data short" id="dataaprovedarea">&nbsp;</div>
        </div>
        <div class="taskview_devstage short">
            <div class="taskview_devstage_title" data-stage="needplates">Need Plate</div>
            <div class="taskview_devstage_subtitle" id="needplatetitle">
                <div class="taskview_rushtitle">&nbsp;</div>
                <div class="taskview_sortarea timesort sorttaskdesc">&nbsp;</div>
                <div class="taskview_timetitle sortactive">Time</div>
                <div class="taskview_sortarea ordersort">&nbsp;</div>
                <div class="taskview_ordertitle">Order</div>
            </div>
            <div class="taskview_devstage_data short" id="dataneedplates">&nbsp;</div>
        </div>
    </div>
</div>
