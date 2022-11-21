<input type="hidden" id="date_type" value="<?=$date_type?>"/>
<input type="hidden" id="expensive" value="<?=$calc_id?>"/>
<div class="expensivesviewtablerow greydatarow">
    <div class="expensive-deeds">
        <div class="expensive-savedata">
            <i class="fa fa-check-circle" data-calc="<?=$calc_id?>"></i>
        </div>
        <div class="expensive-cancel">
            <i class="fa fa-times-circle" data-calc="<?=$calc_id?>"></i>
        </div>
    </div>
    <div class="expensive-annually">
        <?php if ($date_type=='year') { ?>
            <i class="fa fa-circle checked" data-amount="year"></i>
        <?php } else { ?>
            <i class="fa fa-circle-o" data-amount="year"></i>
        <?php } ?>
        <input id="yearsum_inpt" <?=$date_type=='year' ? '' : 'readonly'?> type="text" value="<?=$yearsum?>"/>
    </div>
    <div class="expensive-monthly">
        <?php if ($date_type=='month') { ?>
            <i class="fa fa-circle checked" data-amount="month"></i>
        <?php } else { ?>
            <i class="fa fa-circle-o" data-amount="month"></i>
        <?php } ?>
        <input id="monthsum_inpt" <?=$date_type=='month' ? '' : 'readonly'?> type="text" value="<?=$monthsum?>"/>
    </div>
    <div class="expensive-weekly">
        <?php if ($date_type=='week') { ?>
            <i class="fa fa-circle checked" data-amount="week"></i>
        <?php } else { ?>
            <i class="fa fa-circle-o" data-amount="week"></i>
        <?php } ?>
        <input id="weeksum_inpt" <?=$date_type=='week' ? '' : 'readonly'?> type="text" value="<?=$weeksum?>"/>
    </div>
    <div class="expensive-date">
        <?=$date_edit?>
    </div>
    <div class="expensive-method">
        <select id="method_inpt">
            <option value=""></option>
            <option value="Invoiced" <?=$method=='Invoiced' ? 'selected="selected"' : ''?>>Invoiced</option>
            <option value="Bank">Bank</option>
            <option value="CapOne">CapOne</option>
            <option value="Chase Visa">Chase Visa</option>
            <option value="Amex Star">Amex Star</option>
        </select>
    </div>
    <div class="expensive-description">
        <input id="description_inpt" type="text" value="<?=$description?>"/>
    </div>
    <div class="expensive-quoter expensive-total-dark"><?=$weektotal?></div>
    <div class="expensive-yearly expensive-total-dark"><?=$yeartotal?></div>
    <div class="expensive-percent expensive-total-dark"><?=$expense_perc?></div>
</div>
