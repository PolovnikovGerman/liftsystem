<div class="taxlabel">NJ <?=$this->config->item('outsalestax')?>% Tax</div>
<div class="taxexcept">
    <input type="checkbox" class="excepttax" disabled="disabled" <?=($tax_exempt==1 ? 'checked="checked"' : 0)?> />
    Exempt
</div>
<select class="taxexcept_select input_border_black" disabled="disabled">
    <option value="" <?=$tax_reason=='' ? 'selected="selected"' : ''?>>....</option>
    <option value="Non-profit" <?=$tax_reason=='Non-profit' ? 'selected="selected"' : ''?>>Non-profit</option>
    <option value="School" <?=$tax_reason=='School' ? 'selected="selected"' : ''?>>School</option>
    <option value="Government" <?=$tax_reason=='Government' ? 'selected="selected"' : ''?>>Government</option>
    <option value="Reseller" <?=$tax_reason=='Reseller' ? 'selected="selected"' : ''?>>Reseller</option>     
</select>
<div class="icon_file">&nbsp;</div>
<!-- <div class="exceptbutton">&nbsp;</div> -->