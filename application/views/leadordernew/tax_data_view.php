<?php if ($address['taxview']==0) : ?>
    <div class="infoshiptax_infotext">[ Out of State - <span class="italictxt">No sales tax</span> ] -</div>
<?php else : ?>
    <div class="infoshiptax_infotext taxdataview">[ <span class="bluetxt">NJ <?=$this->config->item('outsalestax')?>% Tax </span>
        <input type="checkbox" class="excepttax" <?=$edit==0 ? 'disabled="disabled"' : ''?> <?=($address['tax_exempt']==1 ? 'checked="checked"' : '')?> />
        Exempt
        <select class="taxexcept_select input_border_black" <?=$edit==0 ? 'disabled="disabled"' : ''?>>
            <option value="" <?=$address['tax_reason']=='' ? 'selected="selected"' : ''?>>....</option>
            <option value="Non-profit" <?=$address['tax_reason']=='Non-profit' ? 'selected="selected"' : ''?>>Non-profit</option>
            <option value="School" <?=$address['tax_reason']=='School' ? 'selected="selected"' : ''?>>School</option>
            <option value="Government" <?=$address['tax_reason']=='Government' ? 'selected="selected"' : ''?>>Government</option>
            <option value="Reseller" <?=$address['tax_reason']=='Reseller' ? 'selected="selected"' : ''?>>Reseller</option>
        </select>
        <span class="icon_file">
                            <i class="fa fa-file-text" aria-hidden="true"></i>
                        </span>
        ] -
    </div>
<?php endif; ?>

