<div class="onboacontainerarea" data-container="<?= $onboat_container ?>">
    <div class="onboacontainerdata <?= ($onboat_status == 1 ? 'arrived' : '') ?>" data-container="<?= $onboat_container ?>">
        <?php $numpp = 0; ?>
        <?php foreach ($data as $row) { ?>
            <div class="contanerdataval <?=$numpp % 2 == 0 ? 'whitedatarow' : 'greydatarow' ?>">
                <?= $row['onroutestock'] ?>
            </div>
            <?php $numpp++; ?>
        <?php } ?>
    </div>
</div>
