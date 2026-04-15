<?php if (count($states)==0) :?>
    <input name="state" type="text" placeholder="State" class="dp-state leadaddressedit emptyleadcontact" value="<?=$statecode?>" data-fld="state"/>
<?php else: ?>
    <select class="dp-state leadaddressedit <?=empty($statecode) ? 'emptyleadcontact' : ''?>" name="state" data-fld="state">
        <option value=""></option>
        <?php foreach ($states as $state): ?>
            <option value="<?=$state['state_code']?>" <?=$state['state_code']==$statecode ? 'selected="selected"' : ''?>><?=$state['state_code']?></option>
        <?php endforeach; ?>
    </select>
<?php endif; ?>