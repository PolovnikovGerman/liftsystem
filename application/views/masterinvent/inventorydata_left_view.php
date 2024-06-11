<?php $coloridx=0;?>
<?php  foreach ($lists as $list) {?>
    <?php if ($list['item_flag']==1) { ?>
        <div class="inventorydatarow masteritem <?=$expand==1 ? 'expand' : ''?>" data-item="<?=$list['id']?>">
    <?php } else { ?>
        <div class="inventorydatarow itemcolor <?=$coloridx%2==0 ? 'whitedatarow' : 'greydatarow'?>" data-invcolor="<?=$list['id']?>">
        <?php $coloridx++;?>
    <?php } ?>
    <?php if ($list['item_flag']==1) { ?>
        <div class="masterinventseq itemedit" data-item="<?=$list['id']?>">
            <i class="fa fa-pencil"></i>
        </div>
        <div class="masterinventnumber itemnumberplace"><?=$list['item_code']?></div>
        <div class="masterinventdescrip">
            <?=$list['description']?>
            <span class="addmasterinventory" data-item="<?=$list['id']?>"><i class="fa fa-plus"></i></span>
        </div>
    <?php } else { ?>
        <div class="masterinventseq"><?=$list['item_seq_total']?></div>
        <div class="masterinventseq"><?=$list['item_seq']?></div>
        <div class="masterinventnumber colordata" data-color="<?=$list['id']?>"><i class="fa fa-pencil"></i></div>
        <div class="masterinventdescrip">
            <div class="masterinventstatus <?=$list['noreorder'] ? 'donotreorder' : ''?>"><?=$list['status']?></div>
            <?=$list['description']?>
        </div>
    <?php } ?>
    <div class="masterinventpercent <?=$list['stockclass']?>" style="border-right: <?=$showmax==1 ? 'none' : '1px solid #000000'?>">
        <?=$list['percent'].'%'?>
    </div>
    <div class="masterinventmaximum" style="display: <?=$showmax==1 ? 'block' : 'none'?>"><?=empty($list['max']) ? '&nbsp;' : QTYOutput($list['max'])?></div>
    <div class="masterinventinstock <?=$list['stockclass']?>"><?=$list['instock']?></div>
    <div class="masterinventreserv"><?=empty($list['reserved']) ? '&nbsp;' : QTYOutput($list['reserved'])?></div>
    <div class="masterinventavailab <?=$list['stockclass']?>"><?=$list['available']?></div>
    <div class="masterinventhistorystock" data-item="<?=$list['id']?>"><?= $list['item_flag']==1 ?  '&nbsp;' :  '<i class="fa fa-question-circle" aria-hidden="true"></i>' ?></div>
    <div class="masterinventunit"><?=$list['unit']?></div>
    </div>
<?php } ?>