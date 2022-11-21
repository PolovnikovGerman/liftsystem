<?php if ($date_type=='year') { ?>
    <input type="text" readonly id="dateday_inpt" value="<?=$date_day?>"/>
<?php } elseif ($date_type=='month') { ?>
    <select id="dateday_inpt">
        <option value=""></option>
        <?php for ($i=1; $i < 31; $i++) { ?>
            <option value="<?=$i?>" <?=$i==$date_day ? 'selected="selected"' : ''?>><?=str_pad($i,2,'0', STR_PAD_LEFT)?></option>
        <?php } ?>
    </select>
<?php } else { ?>
    <select id="dateday_inpt">
        <option value=""></option>
        <option value="1" <?=$date_day==1 ? 'selected="selected"' : ''?>>Mon</option>
        <option value="2" <?=$date_day==2 ? 'selected="selected"' : ''?>>Tue</option>
        <option value="3" <?=$date_day==3 ? 'selected="selected"' : ''?>>Wed</option>
        <option value="4" <?=$date_day==4 ? 'selected="selected"' : ''?>>Thur</option>
        <option value="5" <?=$date_day==5 ? 'selected="selected"' : ''?>>Fri</option>
        <option value="6" <?=$date_day==6 ? 'selected="selected"' : ''?>>Sat</option>
        <option value="7" <?=$date_day==7 ? 'selected="selected"' : ''?>>Sun</option>
    </select>
<?php } ?>