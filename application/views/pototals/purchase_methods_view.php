<div class="row mt-2">
    <div class="col-12">
        <div class="methodslabel"><?=$active==1 ? 'ACTIVE:' : 'INACTIVE:'?></div>
    </div>
</div>
<div class="row mt-1">
    <?php foreach ($methods as $method) { ?>
        <div class="col-9">
            <div class="purchmethodname"> - <?=$method['method_name']?></div>
        </div>
        <div class="col-3">
            <div class="purchmethodaction <?=$active==1 ? 'deactivate' : 'activate'?>" data-method="<?=$method['method_id']?>" data-methodlabel="<?=$method['method_name']?>">
                <?=$active==1 ? 'deactivate' : 'activate'?>
            </div>
        </div>
    <?php } ?>
</div>
