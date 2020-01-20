<?php $i = 0;?>
<?php foreach ($imprint as $row) {?>
    <div class="imprintdatarow" id="location<?=$i?>">
        <div class="location_name_tab">
            <input type="hidden" id="impr_id<?=$i?>" name="impr_id<?=$i?>" value="<?=$row['item_inprint_id']?>"/>
            <input type="hidden" id="impr_name<?=$i?>" name="impr_name<?=$i?>" value="<?=$row['item_inprint_location']?>"/>
            <input type="hidden" id="impr_size<?=$i?>" name="impr_size<?=$i?>" value="<?=$row['item_inprint_size']?>"/>
            <input type="hidden" id="impr_view<?=$i?>" name="impr_view<?=$i?>" value="<?=$row['item_inprint_view']?>"/>
            <?=($i + 1)?>.&nbsp;
            <a href="javascript:void(0);" class="locationedit" id="locedt<?=$i?>"><?=($row['item_inprint_location']=='' ? 'Click to add location' : $row['item_inprint_location']) ?></a>
        </div>
        <div class="location_size_tab" id="locsize<?=$i?>"><?= $row['item_inprint_size'] ?></div>
        <div class="location_view_tab">
            <?php if ($row['item_inprint_view']!='') { ?>
                <div class="location_upload edit" id="upl_<?= $i ?>">
                    <a class="gallery" title="<?=$row['item_inprint_location'] ?>" href="<?=$row['item_inprint_view'] ?>">View</a>
                </div>
            <?php } else { ?>
                <div class="location_upload" id="upl_<?= $i ?>">&nbsp;</div>
            <?php } ?>
            <?php if ($row['item_inprint_id']!='') { ?>
                <div class="location_del" id="del_<?= $i ?>">[del]</div>
            <?php } else { ?>
                <div class="location_del" id="del_<?= $i ?>"></div>
            <?php } ?>
        </div>
    </div>
    <?php $i++; ?>
<?php } ?>
