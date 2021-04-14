<div class="itemdetails_keyinfo">
    <div class="chapterlabel rightpart">Key Information:</div>
    <div class="keyinfoarea-left">
        <div class="content-row">
            <div class="keydatlabel options">Options:</div>
            <div class="keydatvalue options">
                <?php if ($editmode==0) { ?>
                    <div class="viewparam"><?=$item['options']?></div>
                <?php } else { ?>
                    <select name="item_options" id="item_options" class="itemactiveselect" data-fld="options">
                        <option value="colors" <?= ($item['options'] == 'colors' ? 'selected="selected"' : '') ?>>Colors</option>
                        <option value="flavors" <?= ($item['options'] == 'flavors' ? 'selected="selected"' : '') ?> >Flavors</option>
                        <option value="sizes" <?= ($item['options'] == 'sizes' ? 'selected="selected"' : '') ?> >Sizes</option>
                        <option value="shapes" <?= ($item['options'] == 'shapes' ? 'selected="selected"' : '') ?> >Shapes</option>
                    </select>
                <?php } ?>
            </div>
        </div>
        <div class="content-row">
            <div class="keydatvalue colors">
                <?php foreach ($colors as $color) { ?>
                    <?php if ($editmode==0) { ?>
                        <div class="viewparam"><?=empty($color['item_color']) ? '&nbsp;' : $color['item_color']?></div>
                    <?php } else { ?>
                        <input type="itemcolorinpt" data-colorid="<?=$color['item_color_id']?>" value="<?=$color['item_color']?>">
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="keyinfoarea-right">
        <div class="content-row">
            <div class="keydatlabel size">Size:</div>
            <div class="keydatvalue size">
                <?php if ($editmode==0) { ?>
                    <div class="viewparam"><?=$item['item_size']?></div>
                <?php } else { ?>
                    <input type="text" class="itemlistdetailsinpt" data-item="item_size" value="<?=$item['item_size']?>">
                <?php } ?>
            </div>
        </div>
        <div class="content-row">
            <div class="keydatlabel note">Note:</div>
            <div class="keydatvalue note">
                <?php if ($editmode==0) { ?>
                    <div class="viewparam"><?=$item['item_description2']?></div>
                <?php } else { ?>
                    <input type="text" class="itemlistdetailsinpt" data-item="item_description2" value="<?=$item['item_description2']?>"/>
                <?php } ?>
            </div>
        </div>
        <div class="content-row">
            <div class="keydatlabel material">Material:</div>
            <div class="keydatvalue material">
                <?php if ($editmode==0) { ?>
                    <div class="viewparam"><?=$item['item_material']?></div>
                <?php } else { ?>
                    <input type="text" class="itemlistdetailsinpt" data-item="item_material" value="<?=$item['item_material']?>"/>
                <?php } ?>
            </div>
        </div>
        <div class="content-row">
            <div class="keydatlabel note">Note:</div>
            <div class="keydatvalue note">
                <?php if ($editmode==0) { ?>
                    <div class="viewparam"><?=$item['note_material']?></div>
                <?php } else { ?>
                    <input type="text" class="itemlistdetailsinpt" data-item="note_material" value="<?=$item['note_material']?>"/>
                <?php } ?>
            </div>
        </div>
        <div class="content-row">
            <div class="keydatlabel note">Description:</div>
        </div>
        <div class="content-row">
            <div class="keydatvalue description">
                <?php if ($editmode==0) { ?>
                    <div class="viewtextparam"><?=$item['item_description1']?></div>
                <?php } else { ?>
                    <textarea class="itemlistdetailsinpt" data-item="item_description1"><?=$item['item_description1']?></textarea>
                <?php } ?>
            </div>
        </div>
    </div>
</div>