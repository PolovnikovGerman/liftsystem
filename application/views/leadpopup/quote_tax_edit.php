<div class="datarow">
    <div class="quotetaxlabeldata">NJ 7% Tax</div>
    <div class="quotetaxexcept">
        <div class="quotetaxexceptcheck <?=$edit_mode==0 ? '' : 'choice'?>">
            <?php if ($data['tax_exempt']==1) { ?>
                <i class="fa fa-check-square-o" aria-hidden="true"></i>
            <?php } else { ?>
                <i class="fa fa-square-o" aria-hidden="true"></i>
            <?php } ?>
        </div>
        <span>Exempt</span>
    </div>
    <div class="quotetaxexceptreason">
        <select class="taxexceptreasoninpt quotecommondatainpt" <?=$edit_mode==0 ? 'disabled="disabled"' : ''?> data-item="tax_reason">
            <option value="" <?=$data['tax_reason']=='' ? 'selected="selected"' : ''?>>....</option>
            <option value="Non-profit" <?=$data['tax_reason']=='Non-profit' ? 'selected="selected"' : ''?>>Non-profit</option>
            <option value="School" <?=$data['tax_reason']=='School' ? 'selected="selected"' : ''?>>School</option>
            <option value="Government" <?=$data['tax_reason']=='Government' ? 'selected="selected"' : ''?>>Government</option>
            <option value="Reseller" <?=$data['tax_reason']=='Reseller' ? 'selected="selected"' : ''?>>Reseller</option>
        </select>
    </div>
</div>