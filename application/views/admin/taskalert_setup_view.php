<div class="arttaskalertcontent">
    <div class="style-text1">
        <div class="taskalert_head">
            <div class="taskalert_name">Task Stage</div>
            <div class="taskalert_check">&nbsp;</div>
            <div class="taslalert_times">Common Time</div>
            <div class="taslalert_times">Rush Time</div>
        </div>
        <div class="taskalert_row">
            <div class="taskalert_name">No ART</div>
            <div class="taskalert_check">
                <input type="checkbox" name="noart_alert" value="1" id="noart_alert" <?=($noart_alert==1 ? 'checked="checked"' : '')?>>
            </div>
            <div class="taslalert_times" id="noartcommontimes">
                <div class="taskalert_timeday">
                    <input type="text" value="<?=$noart_common_days?>" name="noart_common_days" id="noart_common_days" class="alertspin"/>
                </div>
                <div class="taskalert_timedaylabel">days</div>
                <div class="taskalert_timeday">
                    <input type="text" value="<?=$noart_common_hours?>" name="noart_common_hours" id="noart_common_hours" class="alertspin"/>
                </div>
                <div class="taskalert_timedaylabel">hours</div>
            </div>
            <div class="taslalert_times" id="noartrushtimes">
                <div class="taskalert_timeday">
                    <input type="text" value="<?=$noart_rush_days?>" name="noart_rush_days" id="noart_rush_days" class="alertspin"/>
                </div>
                <div class="taskalert_timedaylabel">days</div>
                <div class="taskalert_timeday">
                    <input type="text" value="<?=$noart_rush_hours?>" name="noart_rush_hours" id="noart_rush_hours" class="alertspin"/>
                </div>
                <div class="taskalert_timedaylabel">hours</div>
            </div>
        </div>
        <div class="taskalert_row">
            <div class="taskalert_name">Redrawing</div>
            <div class="taskalert_check">
                <input type="checkbox" name="redraw_alert" value="1" id="redraw_alert" <?=($redraw_alert==1 ? 'checked="checked"' : '')?>>
            </div>
            <div class="taslalert_times" id="redrawcommontimes">
                <div class="taskalert_timeday">
                    <input type="text" value="<?=$redraw_common_days?>" name="redraw_common_days" id="redraw_common_days" class="alertspin"/>
                </div>
                <div class="taskalert_timedaylabel">days</div>
                <div class="taskalert_timeday">
                    <input type="text" value="<?=$redraw_common_hours?>" name="redraw_common_hours" id="redraw_common_hours" class="alertspin"/>
                </div>
                <div class="taskalert_timedaylabel">hours</div>
            </div>
            <div class="taslalert_times" id="redrawrushtimes">
                <div class="taskalert_timeday">
                    <input type="text" value="<?=$redraw_rush_days?>" name="redraw_rush_days" id="redraw_rush_days" class="alertspin"/>
                </div>
                <div class="taskalert_timedaylabel">days</div>
                <div class="taskalert_timeday">
                    <input type="text" value="<?=$redraw_rush_hours?>" name="redraw_rush_hours" id="redraw_rush_hours" class="alertspin"/>
                </div>
                <div class="taskalert_timedaylabel">hours</div>
            </div>
        </div>
        <!-- -->
        <div class="taskalert_row">
            <div class="taskalert_name">To Proof</div>
            <div class="taskalert_check">
                <input type="checkbox" name="toproof_alert" value="1" id="toproof_alert" <?=($toproof_alert==1 ? 'checked="checked"' : '')?>>
            </div>
            <div class="taslalert_times" id="toproofcommontimes">
                <div class="taskalert_timeday">
                    <input type="text" value="<?=$toproof_common_days?>" name="toproof_common_days" id="toproof_common_days" class="alertspin"/>
                </div>
                <div class="taskalert_timedaylabel">days</div>
                <div class="taskalert_timeday">
                    <input type="text" value="<?=$toproof_common_hours?>" name="toproof_common_hours" id="toproof_common_hours" class="alertspin"/>
                </div>
                <div class="taskalert_timedaylabel">hours</div>
            </div>
            <div class="taslalert_times" id="toproofrushtimes">
                <div class="taskalert_timeday">
                    <input type="text" value="<?=$toproof_rush_days?>" name="toproof_rush_days" id="toproof_rush_days" class="alertspin"/>
                </div>
                <div class="taskalert_timedaylabel">days</div>
                <div class="taskalert_timeday">
                    <input type="text" value="<?=$toproof_rush_hours?>" name="toproof_rush_hours" id="toproof_rush_hours" class="alertspin"/>
                </div>
                <div class="taskalert_timedaylabel">hours</div>
            </div>
        </div>
        <div class="taskalert_row">
            <div class="taskalert_name">Need Approval</div>
            <div class="taskalert_check">
                <input type="checkbox" name="needapproval_alert" value="1" id="needapproval_alert" <?=($needapproval_alert==1 ? 'checked="checked"' : '')?>>
            </div>
            <div class="taslalert_times" id="needapprovalcommontimes">
                <div class="taskalert_timeday">
                    <input type="text" value="<?=$needapproval_common_days?>" name="needapproval_common_days" id="needapproval_common_days" class="alertspin"/>
                </div>
                <div class="taskalert_timedaylabel">days</div>
                <div class="taskalert_timeday">
                    <input type="text" value="<?=$needapproval_common_hours?>" name="needapproval_common_hours" id="needapproval_common_hours" class="alertspin"/>
                </div>
                <div class="taskalert_timedaylabel">hours</div>
            </div>
            <div class="taslalert_times" id="needapprovalrushtimes">
                <div class="taskalert_timeday">
                    <input type="text" value="<?=$needapproval_rush_days?>" name="needapproval_rush_days" id="needapproval_rush_days" class="alertspin"/>
                </div>
                <div class="taskalert_timedaylabel">days</div>
                <div class="taskalert_timeday">
                    <input type="text" value="<?=$needapproval_rush_hours?>" name="needapproval_rush_hours" id="needapproval_rush_hours" class="alertspin"/>
                </div>
                <div class="taskalert_timedaylabel">hours</div>
            </div>
        </div>
    </div>
</div>
