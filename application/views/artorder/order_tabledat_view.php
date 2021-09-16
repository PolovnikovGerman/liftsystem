<?php $nrow=1;?>
<?php foreach ($orders as $row) {?>
    <div class="rowdata <?=(($nrow%2)==0 ? 'whitedatarow' : 'greydatarow')?> <?=($row['order_rush']==0 ? '' : 'rushgeneralorder')?>" data-orderid="<?=$row['order_id']?>">
        <div class="date"><?=$row['order_date']?></div>
        <div class="ordernum" data-orderid="<?=$row['order_id']?>"><?=$row['order_num']?></div>
        <div class="orderconf"><?=($row['order_confirmation']=='' ? '&nbsp;' : $row['order_confirmation'])?></div>
        <div class="customer"><?=$row['customer_name']?></div>
        <div class="item" title="<?=$row['order_items']?>"><?=$row['order_items']?></div>
        <div class="art artstage <?=$row['art_class']?> <?=$row['art_title']?>" data-orderid="<?=$row['order_id']?>" <?=$row['art_msg']?>" >
            <?=$row['art_cell']?>
        </div>
        <div class="art artstage <?=$row['redrawn_class']?> <?=$row['redrawn_title']?>" data-orderid="<?=$row['order_id']?>" <?=$row['redrawn_msg']?> >
            <?=$row['redrawn_cell']?>
        </div>
        <div class="art artstage <?=$row['vectorized_class']?> <?=$row['vectorized_title']?>" data-orderid="<?=$row['order_id']?>" <?=$row['vectorized_msg']?> >
            <?=$row['vectorized_cell']?>
        </div>
        <div class="art artstage <?=$row['proofed_class']?> <?=$row['proofed_title']?>" data-orderid="<?=$row['order_id']?>" <?=$row['proofed_msg']?>>
            <?=$row['proofed_cell']?>
        </div>
        <div class="approve artstage <?=$row['approved_class']?> <?=$row['approved_title']?>" data-orderid="<?=$row['order_id']?>" <?=$row['approved_msg']?>>
            <?=$row['approved_cell']?>
        </div>
        <div class="ordercode" data-content="<?=$row['order_code']?>"><?=$row['order_code']?></div>
        <div class="ordernote <?=($row['art_note']=='' ? '' : 'artnoteshow')?>" <?=($row['art_note']=='' ? '' : 'data-content="'.$row['art_note'].'"')?> data-orderid="<?=$row['order_id']?>">
            <?php if ($row['art_note']=='') { ?>
                <img src="/img/art/empty_square.png" alt="Notes" id="artnote<?=$row['order_id']?>"/>
            <?php } else { ?>
                <img src="/img/art/lightblue_square.png" alt="Notes" id="artnote<?=$row['order_id']?>"/>
            <?php } ?>
        </div>
        <div class="revenue"><?=$row['revenue']?></div>
        <div class="salesrepl"><?=ifset($row, 'user_replic','')?></div>
    </div>
    <?php $nrow++;?>
<?php } ?>