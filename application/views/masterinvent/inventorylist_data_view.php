<?php $coloridx=0;?>
<div class="masterinventtablebody">
<?php  foreach ($lists as $list) {?>
    <?php if ($list['item_flag']==1) { ?>
        <div class="inventorydatarow masteritem <?=$expand==1 ? 'expand' : ''?>">
    <?php } else { ?>
        <div class="inventorydatarow itemcolor <?=$coloridx%2==0 ? 'whitedatarow' : 'greydatarow'?>">
        <?php $coloridx++;?>
    <?php } ?>
        <?php if ($list['item_flag']==1) { ?>
            <div class="masterinventseq itemedit" data-item="<?=$list['id']?>">
                <i class="fa fa-pencil"></i>
            </div>
            <div class="masterinventnumber"><?=$list['item_code']?></div>
            <div class="masterinventdescrip">
                <?=$list['description']?>
                <span class="addmasterinventory" data-item="<?=$list['id']?>"><i class="fa fa-plus"></i></span>
            </div>
        <?php } else { ?>
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
        <div class="masterinventonorder" style="display: <?=$showmax==1 ? 'none' : 'block'?>">
            <?=empty($list['onorder']) ? '&nbsp;' : QTYOutput($list['onorder'])?>
        </div>
        <div class="masterinventonmax" style="display: <?=$showmax==1 ? 'block' : 'none'?>">&nbsp;</div>
        <div class="masterinventavgprice <?=$list['totalclass']?>" data-item="<?=$list['id']?>"><?=MoneyOutput($list['price'],3)?></div>
        <div class="masterinventhistory" data-item="<?=$list['id']?>"><?= $list['item_flag']==1 ?  '&nbsp;' :  '<i class="fa fa-question-circle" aria-hidden="true"></i>' ?></div>
        <div class="masterinventtotalval <?=$list['totalclass']?>"><?=MoneyOutput($list['total'])?></div>
<!--        --><?php //if ($list['item_flag']==1) { ?>
<!--            &nbsp;-->
<!--        --><?php //} else { ?>
<!--            --><?php //if (empty($list['color_image'])) { ?>
<!--                <div class="masterinventdetails colordata emptyimage"><i class="fa fa-search"></i></div>-->
<!--            --><?php //} else { ?>
<!--                <div class="masterinventdetails colordata" data-event="hover" data-css="mastercolor_show_detailsmessage" data-position="auto"-->
<!--                     data-bgcolor="#FFFFFF" data-bordercolor="#000" data-textcolor="#000" data-balloon="{ajax} /masterinventory/get_colorimage?c=--><?php //=$list['id']?><!-- ">-->
<!--                    <i class="fa fa-search"></i>-->
<!--                </div>-->
<!--            --><?php //} ?>
<!--        --><?php //} ?>
    </div>
<?php } ?>
</div>