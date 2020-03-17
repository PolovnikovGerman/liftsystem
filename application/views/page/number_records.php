<select class="selectrecords" id="<?=$fieldname?>">
    <?php foreach ($numrecs as $row) { ?>
    <option value="<?=$row?>" <?=($row==$default_value ? 'selected="selected"' : '')?>><?=$row?> records/per page</option>
    <?php } ?>
</select>