<select class="userstartpageselect" data-brand="<?=$brand?>">
    <option value="" <?=($defpage=='' ? 'selected="selected"' : '')?>>Welcome Page</option>
    <?php foreach ($data as $row) { ?>
        <option value="<?=$row['key']?>" <?=($defpage==$row['key'] ? 'selected="selected"' : '')?>><?=$row['label']?></option>
    <?php } ?>
</select>