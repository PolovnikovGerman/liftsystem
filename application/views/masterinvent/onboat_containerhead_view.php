<div class="onboacontainer <?= ($onboat_status == 1 ? 'arrived' : '') ?>" data-container="<?= $onboat_container ?>">
    <div class="onboatmanage" data-container="<?= $onboat_container ?>">
        <?= ($onboat_status == 0 ? '<i class="fa fa-pencil edit_onboat" aria-hidden="true"></i>' : '') ?>  Cont <?=$onboat_container ?>
    </div>
    <div class="containerdate">
        <input class="boatcontainerdate" data-container="<?= $onboat_container ?>" value="<?= date('m/d/y', $onboat_date) ?>" readonly="readonly"/>
    </div>
    <div class="containrermanage">
        <div class="<?= ($onboat_status == 1 ? 'arrived' : 'waitarrive') ?>" data-container="<?= $onboat_container ?>"><?=$onboat_status==1 ? 'Arrived' : 'Waiting'?></div>
    </div>
    <div class="containerdate">
        <input class="boatcontainerfreight" data-container="<?= $onboat_container ?>" value="" placeholder="freight $" readonly="readonly"/>
    </div>

    <div class="containertotal" data-container="<?= $onboat_container ?>">
        <?= (empty($onboat_total) ? '&nbsp;' : QTYOutput($onboat_total)) ?>
    </div>
</div>

