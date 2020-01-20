<?php $i = 0;?>
<?php foreach ($imprint as $row) {?>
    <div class="imprintdatarow">
        <div class="location_name_tab overflowtext imprintpopover" data-content="<?= $row['item_inprint_location'] ?>">
            <?=($i + 1)?>.&nbsp;<?=$row['item_inprint_location'] ?>
        </div>
        <div class="location_size_tab  overflowtext imprintpopover" data-content="<?= $row['item_inprint_size'] ?>">
            <?= $row['item_inprint_size'] ?>
        </div>
        <div class="location_view_tab">
            <?php if ($row['item_inprint_view'] != '') { ?>
                <div class="location_upload" id="upl_<?= $i ?>">
                    <a class="gallery" title="<?= $row['item_inprint_location'] ?>" href="<?= $row['item_inprint_view'] ?>">click here</a>
                </div>
            <?php } else { ?>
                <div class="location_upload" id="upl_<?= $i ?>">&nbsp;</div>
            <?php } ?>
        </div>
        <div class="location_popular_tab">
            <input type="checkbox" disabled="disabled" <?=$row['item_imprint_mostpopular']==1 ? 'checked="checked"' : ''?>/>
        </div>
    </div>
    <?php $i++; ?>
<?php } ?>
