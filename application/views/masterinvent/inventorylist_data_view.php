<?php $coloridx=0;?>
<?php  foreach ($lists as $list) {?>
    <?php if ($list['item_flag']==1) { ?>
        <div class="inventorydatarow masteritem">
            <div class="masterinventstatus"><?=$list['status']?></div>
    <?php } else { ?>
        <div class="inventorydatarow itemcolor <?=$coloridx%2==0 ? 'whitedatarow' : 'greydatarow'?>">
        <?php $coloridx++;?>
            <div class="masterinventstatus <?=$list['noreorder'] ? 'donotreorder' : ''?>"><?=$list['status']?></div>
    <?php } ?>
        <?php if ($list['item_flag']==1) { ?>
            <div class="masterinventseq itemedit" data-item="<?=$list['id']?>">
                <i class="fa fa-pencil"></i>
            </div>
            <div class="masterinventnumber"><?=$list['item_code']?></div>
            <div class="masterinventdescrip">
                <?=$list['description']?>
                <span class="addmasterinventory"><i class="fa fa-plus"></i></span>
            </div>
        <?php } else { ?>
            <div class="masterinventseq"><?=$list['item_seq']?></div>
            <div class="masterinventnumber colordata" data-color="<?=$list['id']?>"><i class="fa fa-pencil"></i></div>
            <div class="masterinventdescrip"><?=$list['description']?></div>
        <?php } ?>
        <div class="masterinventpercent <?=$list['stockclass']?>" style="border-right: <?=$showmax==1 ? 'none' : '1px solid #000000'?>">
            <?=$list['percent'].'%'?>
        </div>
        <div class="masterinventmaximum" style="display: <?=$showmax==1 ? 'block' : 'none'?>"><?=empty($list['max']) ? '&nbsp;' : QTYOutput($list['max'])?></div>
        <div class="masterinventinstock <?=$list['stockclass']?>"><?=$list['instock']?></div>
        <div class="masterinventreserv"><?=empty($list['reserved']) ? '&nbsp;' : QTYOutput($list['reserved'])?></div>
        <div class="masterinventavailab <?=$list['stockclass']?>"><?=$list['available']?></div>
        <div class="masterinventunit"><?=$list['unit']?></div>
        <div class="masterinventonorder" style="display: <?=$showmax==1 ? 'none' : 'block'?>">
            <?=empty($list['onorder']) ? '&nbsp;' : QTYOutput($list['onorder'])?>
        </div>
        <div class="masterinventonmax" style="display: <?=$showmax==1 ? 'block' : 'none'?>">&nbsp;</div>
        <div class="masterinventavgprice <?=$list['totalclass']?>" data-item="<?=$list['id']?>"><?=MoneyOutput($list['price'],3)?></div>
        <div class="masterinventtotalval <?=$list['totalclass']?>"><?=MoneyOutput($list['total'])?></div>
        <?php if ($list['item_flag']==1) { ?>
            <div class="masterinventdetails itemdata" data-item="<?=$list['id']?>"><i class="fa fa-search"></i></div>
        <?php } else { ?>
            <div class="masterinventdetails colordata" data-color="<?=$list['id']?>"><i class="fa fa-search"></i></div>
        <?php } ?>
    </div>
<?php } ?>
