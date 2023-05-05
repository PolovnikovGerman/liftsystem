<select class="psprintcolor psorderselect" data-fldname="inventory_color_id">
    <option value="" <?= ($printshop_color_id == '' ? 'selected="selected"' : '') ?>>...</option>
    <?php foreach ($colors as $crow) { ?>
        <option value="<?= $crow['inventory_color_id'] ?>" <?= ($crow['inventory_color_id'] == $printshop_color_id ? 'selected="selected"' : '') ?>><?= $crow['color'] ?></option>
    <?php } ?>
</select>
