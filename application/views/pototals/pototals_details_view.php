<?php $numpp = 0;?>
<?php foreach ($datas as $data) { ?>
    <div class="poplace-tablerow <?=$numpp%2==0 ? 'greydatarow' : 'whitedatarow' ?>">
        <div class="poplace-rush"><?=$data['order_rush']==1 ? '<i class="fa fa-star"></i>' : ''?></div>
        <div class="poplace-late"><?=$data['order_late']==1 ? 'LATE' : ''?></div>
        <div class="poplace-order" data-order="<?=$data['order_id']?>"><?=$data['order_num']?></div>
        <div class="poplace-item <?=$data['customitem']?>" data-event="<?=$event?>" data-css="poreport_detailmessage"
             data-bgcolor="#FFFFFF" data-bordercolor="#000" data-textcolor="#000" data-position="up" data-balloon="<?=$data['item_name']?>">
            <?=$data['item_name']?>
        </div>
        <div class="poplace-vendor <?=$data['customitem']?>" data-event="<?=$event?>" data-css="poreport_detailmessage"
             data-bgcolor="#FFFFFF" data-bordercolor="#000" data-textcolor="#000" data-position="up" data-balloon="<?=$data['vendorname']?>">
            <?=$data['vendorname']?>
        </div>
        <div class="poplace-esttotal"><?=MoneyOutput($data['estpo'],0)?></div>
        <div class="poplace-poaction">
            <div class="poplace-poactionbtn" data-order="<?=$data['order_id']?>">PO</div>
        </div>
    </div>
    <?php $numpp++;?>
<?php } ?>