<div class="lead_popupform_row">
    <div class="lead_popup_number">Lead # <?=$brand=='SR' ? 'D' : 'L'?><?=str_pad($data['lead_number'],5,'0',STR_PAD_LEFT)?></div>
    <div class="lead_popup_status">
        <div class="lead_popup_statustitle">Status:</div>
        <div class="lead_popup_statusvalue">
            <select <?=$enable?> class="leadpopstatus" name="lead_type" id="lead_type">
                <option value="6" <?=($data['lead_type']==6 ? 'selected="selected"' : '')?>>Ordering Soon</option>
                <option value="1" <?=($data['lead_type'] == 1 ? 'selected="selected"' : '') ?>>Priority</option>
                <option value="2" <?=($data['lead_type'] == 2 ? 'selected="selected"' : '') ?>>Open</option>
                <option value="4" <?=($data['lead_type'] == 4 ? 'selected="selected"' : '') ?>>Closed</option>
                <?=$dead_option?>
            </select>
        </div>
    </div>
    <div class="lead_popup_replicas">
        <?=$replica?>
    </div>
</div>

