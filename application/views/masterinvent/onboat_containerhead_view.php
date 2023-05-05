<div class="onboacontainer <?= ($onboat_status == 1 ? 'arrived' : '') ?>" data-container="<?= $onboat_container ?>">
    <div class="onboatmanage" data-container="<?= $onboat_container ?>" data-onboattype="<?=$onboat_type?>">
        <?php if ($onboat_container==0) { ?>
            New <?=$onboat_type=='C' ? 'Cont' : 'Expr'?>
        <?php } else { ?>
            <?= ($onboat_status == 0 ? '<i class="fa fa-pencil edit_onboat" aria-hidden="true"></i>' : '') ?>
            <?=$onboat_type=='C' ? 'Cont' : 'Expr'?> <?=$onboat_container ?>
        <?php } ?>
    </div>
    <div class="containerdate">
        <input class="boatcontainerdate" data-container="<?= $onboat_container ?>" value="<?= date('m/d/y', $onboat_date) ?>" readonly="readonly"/>
    </div>
    <div class="containrermanage">
        <div class="<?= ($onboat_status == 1 ? 'arrived' : 'waitarrive') ?>" data-onboattype="<?=$onboat_type?>" data-container="<?= $onboat_container ?>"><?=$onboat_status==1 ? 'Arrived' : 'Waiting'?></div>
    </div>
    <div class="containerdate">
        <input class="boatcontainerfreight" data-container="<?= $onboat_container ?>" value="<?=empty($freight_price) ? '' : number_format($freight_price,2)?>" placeholder="freight $" readonly="readonly" <?=empty($title) ? '' : 'title="'.$title.'"' ?>/>
    </div>
</div>

