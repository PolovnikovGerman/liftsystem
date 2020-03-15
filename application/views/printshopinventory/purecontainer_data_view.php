<div class="onboacontainerdata <?= ($onboat_status == 1 ? 'arrived' : '') ?>" data-container="<?= $onboat_container ?>">
    <?php $numpp = 0; ?>
    <?php foreach ($data as $row) { ?>
        <div class="contanerdataval <?= ($row['type'] == 'item' ? 'itemdata' : ($numpp % 2 == 0 ? 'whitedatarow' : 'greydatarow')) ?>">
            <?= $row['onroutestock'] ?>
        </div>
        <?php if ($row['type'] == 'color') $numpp++; ?>
    <?php } ?>
</div>
