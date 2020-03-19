<div class="option_title">Options:</div>
<div class="option_name">
    <select name="item_options" id="item_options" class="itemactiveselect" data-fld="options">
        <option value="colors" <?= ($options == 'colors' ? 'selected="selected"' : '') ?>>Colors</option>
        <option value="flavors" <?= ($options == 'flavors' ? 'selected="selected"' : '') ?> >Flavors</option>
        <option value="sizes" <?= ($options == 'sizes' ? 'selected="selected"' : '') ?> >Sizes</option>
        <option value="shapes" <?= ($options == 'shapes' ? 'selected="selected"' : '') ?> >Shapes</option>
    </select>
</div>
<div class="options_data">
    <div class="colorsdat-info">
        <?php $i=0;?>
        <?php foreach ($colors as $row) { ?>
            <div class="colorrow">
                <input type="text" value="<?= $row['item_color'] ?>" class="coloroptionvalue itemdetaildatainput" data-entity="colors" data-fldname="item_color" data-fldid="<?= $row['item_color_id'] ?>"/>
            </div>
            <?php $i++;?>
        <?php } ?>
    </div>
</div>