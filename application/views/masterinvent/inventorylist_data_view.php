<?php $coloridx=0;?>
<?php  foreach ($lists as $list) {?>
    <?php if ($list['item_flag']==1) { ?>
        <div class="inventorydatarow masteritem">
    <?php } else { ?>
        <div class="inventorydatarow itemcolor <?=$coloridx%2==0 ? 'whitedatarow' : 'greydatarow'?>">
        <?php $coloridx++;?>
    <?php } ?>
        <div class="masterinventstatus"><?=$list['status']?></div>
        <?php if ($list['item_flag']==1) { ?>
            <div class="masterinventseq" data-item="<?=$list['id']?>">
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
        <div class="masterinventpercent"><?=empty($list['percent']) ? '&nbsp;' : $list['percent'].'%'?></div>
        <div class="masterinventinstock"><?=empty($list['instock']) ? '&nbsp;' : QTYOutput($list['instock'])?></div>
        <div class="masterinventreserv"><?=empty($list['reserved']) ? '&nbsp;' : QTYOutput($list['reserved'])?>&nbsp;</div>
        <div class="masterinventavailab"><?=empty($list['available']) ? '&nbsp;' : QTYOutput($list['available'])?></div>
        <div class="masterinventunit"><?=$list['unit']?></div>
        <div class="masterinventonorder"><?=empty($list['onorder']) ? '&nbsp;' : QTYOutput($list['onorder'])?></div>
        <div class="masterinventavgprice"><?=empty($list['price']) ? '&nbsp;' : MoneyOutput($list['price'],3)?></div>
        <div class="masterinventtotalval"><?=empty($list['total']) ? '&nbsp;' : MoneyOutput($list['total'])?></div>
        <?php if ($list['item_flag']==1) { ?>
            <div class="masterinventdetails itemdata" data-item="<?=$list['id']?>"><i class="fa fa-search"></i></div>
        <?php } else { ?>
            <div class="masterinventdetails colordata" data-color="<?=$list['id']?>"><i class="fa fa-search"></i></div>
        <?php } ?>
    </div>
<?php } ?>
