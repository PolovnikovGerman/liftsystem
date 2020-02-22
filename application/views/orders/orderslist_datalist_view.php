<?php $nrow=0; ?>
<?php foreach ($data as $row) { ?>
    <div class="leadordlist_datarow <?=($nrow%2==0 ? 'greydatarow' : 'whitedatarow')?> <?=$row['rowclass']?>" data-order="<?=$row['order_id']?>">
        <div class="seqnum"><?=$row['numpp']?></div>
        <div class="date"><?=$row['order_date']?></div>
        <div class="ordernum"><?=$row['order_num']?></div>
        <div class="customer"><?=(empty($row['customer_name']) ? '&nbsp;' : $row['customer_name'])?></div>
        <div class="qty" >
            <input class="leadordlistorderqty" type="text" data-orderqty="<?=$row['order_id']?>" value="<?=($row['order_qty']==0 ? '' : $row['order_qty'])?>"/>
        </div>
        <div class="itemnumber"><?=$row['order_itemnumber']?></div>
        <div class="item <?=($row['custom_order']==1 ? 'customorder' : '')?>"><?=$row['out_item']?></div>
        <div class="revenue"><?=$row['revenue']?></div>
        <div class="edit">
            <div class="leadorderform_save" style="visibility: hidden" data-order="<?=$row['order_id']?>"><img src="/img/icons/accept.png"></div>
        </div>
    </div>
    <?php $nrow++;?>
<?php } ?>
