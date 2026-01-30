<?php if (count($states)==0) :?>
    <input type="text" placeholder="State" class="dp-state"/>
<?php else: ?>
    <select class="dp-state">
        <option value=""></option>
        <?php foreach ($states as $state): ?>
            <option value="<?=$state['state_code']?>" <?=$state['state_code']==$statecode ? 'selected="selected"' : ''?>><?=$state['state_code']?></option>
        <?php endforeach; ?>
    </select>
<?php endif; ?>