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
                <div class="location_upload" data-title="<?= $row['item_inprint_location'] ?>" data-srclink="<?= $row['item_inprint_view'] ?>" id="upl_<?= $i ?>">
                    click here
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
