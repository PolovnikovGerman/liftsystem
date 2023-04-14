<?php $nrow=0; ?>
<?php foreach ($data as $row) { ?>
    <div class="leadorder_datarow <?=($nrow%2==0 ? 'greydatarow' : 'whitedatarow')?> <?=$row['rowclass']?> <?=$brand=='SR' ? 'relievers' : ''?>" data-order="<?=$row['order_id']?>">
        <div class="date"><?=$row['order_date']?></div>
        <div class="ordernum <?=$row['ordernum_class']?>"><?=$row['order_num']?></div>
        <div class="confirmnum <?=$row['order_confirmclass']?>"><?=$row['out_confirm']?></div>
        <?php if ($brand=='SR') { ?>
            <div class="customerponumber" title="<?=$row['customer_ponum']?>"><?=(empty($row['customer_ponum']) ? '&nbsp;' : $row['customer_ponum'])?></div>
        <?php } ?>
        <div class="customer"><?=(empty($row['customer_name']) ? '&nbsp;' : $row['customer_name'])?></div>
        <div class="qty" ><?=($row['order_qty']==0 ? '&nbsp;' : QTYOutput($row['order_qty']))?></div>
        <?php if (empty($row['itemcolor'])) { ?>
            <div class="itemcolor">&nbsp;</div>
        <?php } else { ?>
            <div class="itemcolor <?=$row['itemcolorclass']?>" <?=$row['itemcolorclass']=='wide' ? 'data-content="'.$row['itemcolor'].'"' : ''?> ><?=$row['itemcolor']?></div>
        <?php } ?>
        <div class="item <?=($row['custom_order']==1 ? 'customorder' : '')?>"><?=$row['out_item']?></div>
        <div class="revenue"><?=$row['revenue']?></div>
        <div class="usrrepl <?=$row['usrreplclass']?>">
            <?php if ($role=='user') { ?>
                <?=$row['user_replic']?>
            <?php } else { ?>
                <select data-order="<?=$row['order_id']?>" class="selectreplic">
                    <?php if ($row['order_usr_repic']=='') { ?>
                        <option value="">Unassigned</option>
                    <?php } ?>
                    <option value="-1">Website</option>
                    <?php foreach ($users as $urow) {?>
                        <option value="<?=($urow['user_id'])?>" <?=($urow['user_id']==$row['order_usr_repic'] ? 'selected="selected"' : '')?>>
                            <?=($urow['user_leadname']=='' ? $urow['user_name'] : $urow['user_leadname'])?>
                        </option>
                    <?php } ?>
                </select>
            <?php } ?>
        </div>
        <div class="ordclass"><?=$row['order_class']?></div>
        <div class="artstage"><?=$row['artstage']?></div>
        <div class="points <?=$row['profit_class']?>"><?=$row['points']?></div>
        <div class="pointsdevider">&nbsp;</div>
        <div class="ordstatus <?=$row['order_status_class']?>"><?=$row['order_status']?></div>
    </div>
    <?php $nrow++;?>
<?php } ?>
