<div class="datarow">
    <div class="methodslabel"><?=$active==1 ? 'ACTIVE:' : 'INACTIVE:'?></div>
</div>
<?php foreach ($methods as $method) { ?>
    <div class="datarow">
        <div class="purchmethodname"> - <?=$method['method_name']?></div>
        <div class="purchmethodaction <?=$active==1 ? 'deactivate' : 'activate'?>" data-method="<?=$method['method_id']?>" data-methodlabel="<?=$method['method_name']?>">
            <?=$active==1 ? 'deactivate' : 'activate'?>
        </div>
    </div>
<?php } ?>

