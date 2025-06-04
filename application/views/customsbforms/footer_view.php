<input type="hidden" id="customform_leadcheck" value="0"/>
<div class="sbcustomformfooter_left">
    <div class="datarow">
        <div class="customform_leadcheck"><i class="fa fa-square-o" aria-hidden="true"></i></div>
        <div class="customform_leadcheck_label">Assign to Existing Lead:</div>
    </div>
    <div class="datarow">
        <div class="customform_lead_select">
            <select id="lead_id" name="lead_id" class="leadopenlist" disabled="disabled">
                <option value="">Enter & Select Lead...</option>
                <?php foreach ($leads as $lead) {?>
                    <option value="<?=$lead['id']?>"><?=$lead['value']?></option>
                <?php }?>
            </select>
        </div>
        <div class="customform_lead_assign">Attach to Lead</div>
    </div>
</div>
<div class="sbcustomformfooter_right">
    <div class="sbcustomform_newlead active">Create Lead</div>
</div>
