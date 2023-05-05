<div class="containertotal" <?= ($onboat_status == 1 ? 'arrived' : '') ?> data-container="<?= $onboat_container ?>">
    <?= (empty($onboat_total) ? '&nbsp;' : QTYOutput($onboat_total)) ?>
</div>
