<select name="rushdays" class="shiprashselect input_border_gray" <?=($edit==1 ? '' : 'disabled="disabled"')?>>
    <?php foreach ($rush as $row) { ?>        
        <option value="<?= $row['id'] ?>" <?= ($row['date'] == $shipdate ? 'selected="selected"' : '') ?> ><?= $row['list'] ?></option>
    <?php } ?>
</select>
