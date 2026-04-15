<input type="hidden" id="webquestions_leadcheck" value="0"/>
<input type="hidden" id="interestsb_brand" value="<?=$brand?>"/>
<div class="intpopup-footer">
    <div class="intpopup-selectlead">
        <div class="intpopupfooter-check">
            <i class="fa fa-square-o" aria-hidden="true"></i>
        </div>
        <div class="intpopupfooter-label">Assign to Existing Lead:</div>
        <div class="intpopupfooter-select">
            <select id="lead_id" name="lead_id" class="leadopenlist" disabled="disabled">
                <option value="">Enter & Select Lead...</option>
                <?php foreach ($leads as $lead) {?>
                    <option value="<?=$lead['id']?>"><?=$lead['value']?></option>
                <?php }?>
            </select>
        </div>
    </div>
    <div class="intpopup-btn">
        <div class="ip-btncreatelead active">Create Lead</div>
        <div class="interest_lead_assign">Attach to Lead</div>
    </div>
</div>
