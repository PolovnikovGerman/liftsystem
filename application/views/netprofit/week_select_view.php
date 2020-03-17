<div class="netprofit_week_selectarea">
    <div class="weekselect-data">
        <div class="weekselect-data-input">
            <input type="checkbox" class="allweekschoice" name="test" value="allweeks" <?=$weekallcheck?>/>
        </div>
        <span class="weekselect-label">All Weeks</span>
    </div>
    <div class="weekselect-datalist">
        <span class="weekselect-labelchoice">From</span>
        <select id="weekselectfrom" class="weekselect">
            <option value="" <?=($weekfrom=='' ? 'selected="selected"' : '')?>>Select Week</option>
            <?php foreach ($weeklist as $row) {?>
            <option value="<?=$row['id']?>" <?=($row['id']==$weekfrom ? 'selected="selected"' : '')?>><?=$row['label']?></option>
            <?php } ?>
        </select>
        <span class="weekselect-labelchoice">Until</span>
        <select id="weekselectuntil" class="weekselect">
            <option value="" <?=($weekuntil=='' ? 'selected="selected"' : '')?>>Select Week</option>
            <?php foreach ($weeklist as $row) {?>
            <option value="<?=$row['id']?>" <?=($row['id']==$weekuntil ? 'selected="selected"' : '')?>><?=$row['label']?></option>
            <?php } ?>
        </select>
    </div>
</div>