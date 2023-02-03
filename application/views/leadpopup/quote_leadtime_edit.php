<select class="quoteleadtimeselect" <?=$edit_mode==0 ? 'disabled="true"' : ''?>>
    <?php foreach($lead_times as $lead_time) { ?>
        <option value="<?=$lead_time['id']?>" <?=$lead_time['current']==1 ? 'selected="selected"' : ''?>><?=$lead_time['name']?></option>
    <?php } ?>
</select>
