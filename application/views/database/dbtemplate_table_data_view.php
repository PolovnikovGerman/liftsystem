<?php if (count($item_dat) == 0) { ?>
    <div class="whitedatarow">
        <div class="emptytemplatesdata">No records</div>
    </div>
<?php } else { ?>
    <?php $n_row = $offset + 1; ?>
    <?php foreach ($item_dat as $row) { ?>
        <div class="<?= ($n_row % 2 == 0 ? 'greydatarow' : 'whitedatarow') ?> templatedatarow">
            <div class="templatedatacell numinlist"><?=$n_row?></div>
            <div class="templatedatacell editcoll" data-item="<?=$row['item_id']?>">
                <i class="fa fa-pencil-square-o edit_item" aria-hidden="true"></i>
            </div>
            <div class="templatedatacell itemnum"><?= $row['item_number'] ?></div>
            <div class="templatedatacell overflowtext itemtitle" data-content="<?=$row['item_name']?>">
                <?= $row['item_name'] ?>
            </div>
            <div class="templatedatacell templatesource">
                <img id="il<?= $row['item_id'] ?>" src="/img/database/play-green.png" class="player"/>
                <a href="javascript:void(0);" <?= ($row['item_vector_img'] == '' ? 'onclick="empty_vectorfile();"' : 'onclick="openai(\'' . $row['item_vector_img'] . '\');"') ?>>open
                    in Illustrator</a>
            </div>
            <div class="templatedatacell updatetemplate aiupdatestatus <?= $row['update_template_class'] ?>"">
                <?= $row['update_template'] == 0 ? 'No' : 'Yes' ?>
            </div>
            <div class="templatedatacell imprint_update_label aiupdatestatus <?= $row['update_imprint_class'] ?>" data-item=<?=$row['item_id']?>>
                <?= $row['imprint_update'] == 0 ? 'No' : 'Yes' ?></div>
            <div class="templatedatacell imprint_update_manage aiupdatestatus <?= $row['update_imprint_class'] ?>" data-item=<?=$row['item_id']?>>
                <input type="radio" class="updateimprintradio" name="updateimprint_<?= $row['item_id'] ?>"
                       value="1" <?= $row['imprint_update'] == 0 ? 'disabled="disabled"' : '' ?> <?= $row['imprint_update'] == 1 ? 'checked="checked"' : '' ?>
                       data-item="<?= $row['item_id'] ?>">
                <label for="contactChoice1">Partial</label>
                <input type="radio" class="updateimprintradio" name="updateimprint_<?= $row['item_id'] ?>"
                       value="2" <?= $row['imprint_update'] == 0 ? 'disabled="disabled"' : '' ?> <?= $row['imprint_update'] == 2 ? 'checked="checked"' : '' ?>
                       data-item="<?= $row['item_id'] ?>">
                <label for="contactChoice2">Complete</label>
            </div>
        </div>
        <?php $n_row++; ?>
    <?php } ?>
<?php } ?>

