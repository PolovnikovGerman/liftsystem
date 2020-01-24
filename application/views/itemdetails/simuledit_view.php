<?php $i = 1; ?>
<div class="similar_dat">
    <?php foreach ($similar as $row) { ?>
        <div class="similar_item_dat">
            <select class="simularselect" data-entity="simular" data-fld="<?= $row['item_similar_id'] ?>">
                <option value="">Select</option>
                <?php foreach ($item_list as $itemrow) { ?>
                    <option value="<?= $itemrow['item_id'] ?>" <?= ($itemrow['item_id'] == $row['item_similar_similar'] ? 'selected' : '') ?> ><?= $itemrow['item_name'] ?></option>
                <?php } ?>
            </select>
        </div>
        <?php $i++; ?>
    <?php } ?>
</div>