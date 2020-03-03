<div class="onboacontainer <?= ($onboat_status == 1 ? 'arrived' : '') ?>" data-container="<?= $onboat_container ?>">
    <div class="onboatmanage" data-container="<?= $onboat_container ?>">
        Cont <?=$onboat_container ?>
    </div>
    <div class="containerdate">
        <input class="boatcontainerdate" data-container="<?= $onboat_container ?>" value="<?= date('m/d/y', $onboat_date) ?>" readonly="readonly"/>
    </div>
    <div class="containrermanage">
        <div class="<?= ($onboat_status == 1 ? 'arrived' : 'waitarrive') ?>" data-container="<?= $onboat_container ?>">&nbsp;</div>
    </div>
    <div class="containertotal" data-container="<?= $onboat_container ?>">
        <?= (empty($onboat_total) ? '&nbsp;' : QTYOutput($onboat_total)) ?>
    </div>
</div>
