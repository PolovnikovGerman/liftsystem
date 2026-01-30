<select class="dp-state">
    <option value=""></option>
    <?php foreach ($states as $state) : ?>
        <option value="<?=$state['state_code']?>" <?=$state['state_code']==$statecod ? 'selected="selected"' : ''?>><?=$state['state_code']?></option>
    <?php endforeach; ?>
</select>
