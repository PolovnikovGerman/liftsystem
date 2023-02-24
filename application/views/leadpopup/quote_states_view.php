<select class="quoteaddressinpt quotestate" <?=$edit_mode==0 ? 'disabled="true"' : ''?> data-item="<?=$item?>">
    <option value=""></option>
    <?php foreach ($states as $state) { ?>
        <option value="<?=$state['state_code']?>" <?=$state['state_code']==$data[$item] ? 'selected="selected"' : ''?>><?=$state['state_code']?></option>
    <?php } ?>
</select>
