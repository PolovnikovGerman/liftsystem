<input type="hidden" id="date_type" value="<?=$date_type?>"/>
<input type="hidden" id="expensive" value="<?=$calc_id?>"/>

<div class="expensive-deeds">
    <div class="expensive-savedata">
        <i class="fa fa-check-circle" data-calc="<?=$calc_id?>"></i>
    </div>
    <div class="expensive-cancel">
        <i class="fa fa-times-circle" data-calc="<?=$calc_id?>"></i>
    </div>
</div>
<div class="expensive-annually">
    <div class="expensive-edit-radio">
        <?php if ($date_type=='year') { ?>
            <i class="fa fa-circle checked" data-amount="year"></i>
        <?php } else { ?>
            <i class="fa fa-circle-o" data-amount="year"></i>
        <?php } ?>
    </div>
    <div class="expensive-edit-inputval">
        <input id="yearsum_inpt" <?=$date_type=='year' ? '' : 'readonly'?> type="text" value="<?=$yearsum?>"/>
    </div>
</div>
<div class="expensive-monthly">
    <div class="expensive-edit-radio">
        <?php if ($date_type=='month') { ?>
            <i class="fa fa-circle checked" data-amount="month"></i>
        <?php } else { ?>
            <i class="fa fa-circle-o" data-amount="month"></i>
        <?php } ?>
    </div>
    <div class="expensive-edit-inputval">
        <input id="monthsum_inpt" <?=$date_type=='month' ? '' : 'readonly'?> type="text" value="<?=$monthsum?>"/>
    </div>
</div>
<div class="expensive-weekly">
    <div class="expensive-edit-radio">
        <?php if ($date_type=='week') { ?>
            <i class="fa fa-circle checked" data-amount="week"></i>
        <?php } else { ?>
            <i class="fa fa-circle-o" data-amount="week"></i>
        <?php } ?>
    </div>
    <div class="expensive-edit-inputval">
        <input id="weeksum_inpt" <?=$date_type=='week' ? '' : 'readonly'?> type="text" value="<?=$weeksum?>"/>
    </div>
</div>
<div class="expensive-date">
    <?=$date_edit?>
</div>
<div class="expensive-method">
    <select id="method_inpt">
        <option value=""></option>
        <option value="Invoiced" <?=$method=='Invoiced' ? 'selected="selected"' : ''?>>Invoiced</option>
        <option value="Bank" <?=$method=='Bank' ? 'selected="selected"' : ''?>>Bank</option>
        <option value="CapOne" <?=$method=='CapOne' ? 'selected="selected"' : ''?>>CapOne</option>
        <option value="Chase Visa" <?=$method=='Chase Visa' ? 'selected="selected"' : ''?>>Chase Visa</option>
        <option value="Amex Star" <?=$method=='Amex Star' ? 'selected="selected"' : ''?>>Amex Star</option>
    </select>
</div>
<div class="expensive-description">
    <input id="description_inpt" type="text" value="<?=$description?>"/>
</div>
<div class="expensive-quoter expensive-total-dark"><?=$weektotal?></div>
<div class="expensive-yearly expensive-total-dark"><?=$yeartotal?></div>
<div class="expensive-percent expensive-total-dark"><?=$expense_perc?></div>
