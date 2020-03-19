<?php if (count($item_dat) == 0) { ?>
    <div class="whitedatarow">
        <div class="emptycategorydata">No records</div>
    </div>
<?php } else { ?>
    <?php $n_row = $offset + 1; ?>
    <?php foreach ($item_dat as $row) { ?>
        <div class="<?=($n_row % 2 == 0 ? 'greydatarow' : 'whitedatarow') ?> categorydatarow">
            <div class="categdatacell numinlist"><?= $n_row ?></div>
            <div class="categdatacell editcoll" data-item="<?=$row['item_id']?>"><i class="fa fa-pencil-square-o edit_item" aria-hidden="true"></i></div>
            <div class="categdatacell itemnum"><?= $row['item_number'] ?></div>
            <div class="categdatacell itemtitle <?= $row['itemnameclass']?> overflowtext" data-content="<?= $row['item_name'] ?>"><?= $row['item_name'] ?></div>
            <div class="categdatacell cntcategories">&nbsp;</div>
            <?php $i = 1; ?>
            <?php foreach ($row['categories'] as $cat) { ?>
                <div class="categdatacell categoryname" data-categorynum="<?=$i?>" data-itemid="<?=$row['item_id']?>">
                    <select <?= ($pagelock == 1 ? 'disabled="disabled"' : '') ?> class="<?= ($cat['category'] != '' ? 'category_exist' : 'category_empty')?>" data-itemcategid="<?=empty($cat['recid']) ? -1 : $cat['recid']?>">
                        <option value="0">--select--</option>
                        <?php foreach ($categ_list as $list) { ?>
                            <option value="<?= $list['category_id'] ?>" <?= ($cat['category'] == $list['category_id'] ? 'selected="selected"' : '') ?>>
                                <?= $list['category_name'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <?php $i++; ?>
            <?php } ?>
            <div class="categdatacell emptyspace">&nbsp;</div>
        </div>
        <?php $n_row++; ?>
    <?php } ?>
<?php } ?>
