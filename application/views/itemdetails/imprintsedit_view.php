<?php foreach ($imprint as $row) {?>
    <div class="imprintdatarow">
        <div class="location_name_tab locationedit" data-idx="<?=$row['item_inprint_id']?>">
            <?=($row['item_inprint_location']=='' ? 'Click to add location' : htmlspecialchars($row['item_inprint_location'])) ?>
        </div>
        <div class="location_size_tab"><?= htmlspecialchars($row['item_inprint_size']) ?></div>
        <div class="location_view_tab">
            <?php if ($row['item_inprint_view']!='') { ?>
                <div class="location_upload edit" data-title="<?=htmlspecialchars($row['item_inprint_location']) ?>" data-srclink="<?= $row['item_inprint_view'] ?>">
                    View
                </div>
            <?php } ?>
            <?php if ($row['item_inprint_location']!='') { ?>
                <div class="location_popular_tab">
                    <input type="checkbox" class="editimprint" data-idx="<?=$row['item_inprint_id']?>" <?=$row['item_imprint_mostpopular']==1 ? 'checked="checked"' : ''?>/>
                </div>
                <div class="location_del" data-idx="<?=$row['item_inprint_id']?>" data-title="<?=$row['item_inprint_location']?>">
                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                </div>
            <?php } ?>
        </div>
    </div>
<?php } ?>
