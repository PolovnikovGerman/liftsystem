<?php $i = 1; ?>
<div class="similar_dat">
    <?php foreach ($similar as $row) { ?>
        <input type="hidden" id="simid<?= $i ?>" name="simid<?= $i ?>" value="<?= $row['item_similar_id'] ?>"/>
        <input type="hidden" id="simsim<?= $i ?>" name="simsim<?= $i ?>" value="<?= $row['item_similar_similar'] ?>"/>
        <div class="similar_item_dat" title="<?= $row['item_name'] ?>"><?= $row['item_number'] ?></div>
        <?php $i++; ?>
    <?php } ?>
</div>