<div class="artpopup_orderrow">
    <div class="artpopup_orderlabel">Order:</div>
    <?=$ordernum_data?>
</div>
<div class="artpopup_orderrow">
    <div class="artpopup_orderlabel">Request:</div>
    <div class="artpopup_orderproof">
        <?=($proof_num=='' ? '&nbsp;' : 'pr'.$proof_num)?>
    </div>
</div>
<div class="artpopup_orderemp">&nbsp;</div>
<div class="artpopup_orderrow">
    <div class="artpopup_orderlabel rushlabel">
        <div class="artpopup_rushinpt">
            <input type="checkbox" name="rushval" id="rushval" value="1" <?=($artwork_rush==1 ? 'checked="checked"' : '')?>/>
        </div>
        Rush
    </div>
</div>
<div class="artpopup_orderrow">
    <div class="artpopup_orderlabel rushlabel">
        <div class="artpopup_rushinpt">
            <input type="checkbox" name="blankval" id="blankval" value="1" <?=($artwork_blank==1 ? 'checked="checked"' : '')?>/>
        </div>
        Blank
    </div>
</div>