<select class="pay_method_select1 input_border_gray creditappselectterm" <?=($editalov==0 ? 'disabled="disabled"' : '')?>>
    <option value="School" <?=$balance_term=='School' ? 'selected="selected"' : '' ?>>School</option>
    <option value="Govt" <?=$balance_term=='Govt' ? 'selected="selected"' : '' ?>>Govt</option>
    <option value="College" <?=$balance_term=='College' ? 'selected="selected"' : '' ?>>College</option>
    <option value="Hospital" <?=$balance_term=='Hospital' ? 'selected="selected"' : '' ?>>Hospital</option>
    <option value="Business" <?=$balance_term=='Business' ? 'selected="selected"' : '' ?>>Business</option>
    <option value="Non-Profit" <?=$balance_term=='Non-Profit' ? 'selected="selected"' : '' ?>>Non-Profit</option>
</select>
<div class="icon_file creditappview <?=($editalov==1 ? 'uploadappfile' : '')?>">&nbsp;</div>
<div class="button_no">
    <div class="button_no_text">NO</div>
</div>
<div class="pm_content3_tx1">Due:<br/>
    <input type="text" class="pay_method_input5 input_border_gray creditappduedate" <?=($editalov==0 ? 'disabled="disabled"' : '')?>
           value="<?=$credit_appdue==0 ? '' : date('m/d', $credit_appdue)?>"/>
</div>
