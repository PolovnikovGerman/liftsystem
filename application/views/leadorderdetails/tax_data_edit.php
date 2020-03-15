<div class="taxlabel">NJ 7% Tax</div>
<div class="taxexcept">
    <input type="checkbox" class="excepttax"  <?=($tax_exempt==1 ? 'checked="checked"' : '')?> data-shipadr="<?=$order_shipaddr_id?>"/>
    Exempt
</div>
<select class="taxexcept_select input_border_black" data-shipadr="<?=$order_shipaddr_id?>" <?=$tax_exempt==1 ? '' : 'disabled="disabled"'?>>
    <option value="" <?=$tax_reason=='' ? 'selected="selected"' : ''?>>....</option>
    <option value="Non-profit" <?=$tax_reason=='Non-profit' ? 'selected="selected"' : ''?>>Non-profit</option>
    <option value="School" <?=$tax_reason=='School' ? 'selected="selected"' : ''?>>School</option>
    <option value="Government" <?=$tax_reason=='Government' ? 'selected="selected"' : ''?>>Government</option>
    <option value="Reseller" <?=$tax_reason=='Reseller' ? 'selected="selected"' : ''?>>Reseller</option>     
</select>
<div class="icon_file taxexceptdoc <?=$tax_exempt==1 ? 'active' : 'nonactive'?>" data-shipadr="<?=$order_shipaddr_id?>">&nbsp;</div>
<div class="exceptbutton">&nbsp;</div>