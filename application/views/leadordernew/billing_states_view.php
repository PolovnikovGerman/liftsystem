<select class="select_addressarea" <?=$edit==0 ? 'disabled' : ''?> name="billing_state" id="billing_state">>
    <option value="">State</option>
    <?php foreach ($states as $state): ?>
        <option value="<?=$state['state_id']?>" <?=$state['state_id']==$billing['state_id'] ? 'selected' : ''?>><?=$state['state_code']?></option>
    <?php endforeach; ?>
</select>
