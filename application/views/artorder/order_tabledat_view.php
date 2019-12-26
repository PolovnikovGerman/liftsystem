<?php $nrow=1;?>
    <div id="orderprofit0"></div>
<?php foreach ($orders as $row) {?>
    <div class="ordart_rowdata <?=(($nrow%2)==0 ? 'white' : 'grey')?> <?=$row['rush_class']?>" data-orderid="<?=$row['order_id']?>">
        <div class="ordeart_actioins">
            <a class="edit_artorder" id="edt<?=$row['order_id']?>" href="javascript:void(0);"><img src="/img/icons/edit.png"/></a>
        </div>
        <div class="ordart_blank_dat">
            <input type="checkbox" data-orderid="<?=$row['order_id']?>" class="ordartblanccheck" <?=($row['order_blank']==0 ? '' : 'checked="checked"')?>/>
        </div>
        <div class="ordart_date-dat"><?=$row['order_date']?></div>
        <div class="ordart_ordernum-dat" data-orderid="<?=$row['order_id']?>"><?=$row['order_num']?></div>
        <div class="ordart_customer-maildat"><?=$row['email']?></div>
        <div class="ordart_customer-data"><?=$row['customer_name']?></div>
        <div class="ordart_item-data" ><?=$row['order_items']?></div>
        <div class="ordart_art-dat artstage <?=$row['art_class']?> <?=$row['art_title']?>" data-orderid="<?=$row['order_id']?>" data-messageview="<?=$row['lastmsg']?>">
            <?=$row['art_cell']?>
        </div>
        <div class="ordart_redrawn-dat artstage <?=$row['redrawn_class']?> <?=$row['redrawn_title']?>" data-orderid="<?=$row['order_id']?>" data-messageview="<?=$row['lastmsg']?>">
            <?=$row['redrawn_cell']?>
        </div>
        <div class="ordart_vector-dat artstage <?=$row['vectorized_class']?> <?=$row['vectorized_title']?>" data-orderid="<?=$row['order_id']?>" data-messageview="<?=$row['lastmsg']?>">
            <?=$row['vectorized_cell']?>
        </div>
        <div class="ordart_vector-dat artstage <?=$row['proofed_class']?> <?=$row['proofed_title']?>" data-orderid="<?=$row['order_id']?>" data-messageview="<?=$row['lastmsg']?>">
            <?=$row['proofed_cell']?>
        </div>
        <div class="ordart_approve-dat artstage <?=$row['approved_class']?> <?=$row['approved_title']?>" data-orderid="<?=$row['order_id']?>" data-messageview="<?=$row['lastmsg']?>">
            <?=$row['approved_cell']?>
        </div>
        <div class="ordart_code-data" title="<?=$row['order_code']?>"><?=$row['out_code']?></div>
        <div class="ordart_note-dat <?=($row['art_note']=='' ? '' : 'artnoteshow')?>" <?=($row['art_note']=='' ? '' : 'title="'.$row['art_note'].'"')?>>
            <?php if ($row['art_note']=='') { ?>
                <img src="/img/art/empty_square.png" alt="Notes" id="artnote<?=$row['order_id']?>"/>
            <?php } else { ?>
                <img src="/img/art/lightblue_square.png" alt="Notes" id="artnote<?=$row['order_id']?>"/>
            <?php } ?>
        </div>
    </div>
    <?php $nrow++;?>
<?php } ?>