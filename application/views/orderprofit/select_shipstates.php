<select class="selectstatelocationdat">
    <option value="0">All States</option>
    <?php foreach ($states as $row) { ?>
        <option value="<?=$row['state_id']?>"><?=$row['state_code']?></option>
    <?php } ?>
</select>