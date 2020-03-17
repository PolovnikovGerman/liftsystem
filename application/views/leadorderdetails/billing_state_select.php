<select class="billing_select1 input_border_gray">
    <option value="">&nbsp;</option>
    <?php foreach ($states as $row) { ?>
        <option value="<?= $row['state_id'] ?>" <?= $row['state_id'] == $curstate ? 'selected="selected"' : '' ?>><?= $row['state_code'] ?></option>
    <?php } ?>
</select>                
