<?php if (count($data)==0) { ?>
<div class="rowdata emptyvalue">No coupon data</div>
<?php } else { ?>
    <?php $numr = 0;?>
    <?php foreach ($data as $row) { ?>
        <div class="rowdata <?=$numr%2==0 ? 'greydatarow' : 'whitedatarow'?>">
            <div class="activedoc" data-couponid="<?=$row['coupon_id']?>">
                <?=($row['coupon_ispublic']==0 ? '<i class="fa fa-square-o" aria-hidden="true"></i>' : '<i class="fa fa-check-square-o" aria-hidden="true"></i>')?>
            </div>
            <div class="percentoff"><?=($row['coupon_discount_perc']==0 ? '&nbsp;' : $row['coupon_discount_perc'].'%' )?></div>
            <div class="text">&nbsp;</div>
            <div class="moneyoff"><?=($row['coupon_discount_sum']!='0' ? MoneyOutput($row['coupon_discount_sum']) : '&nbsp;')?></div>
            <div class="minrevenue"><?=($row['coupon_minlimit']=='0' ? '- - - - - - - - -' : MoneyOutput($row['coupon_minlimit']))?></div>
            <div class="maxrevenue"><?=($row['coupon_maxlimit']=='0' ? '- - - - - - - - -' : MoneyOutput($row['coupon_maxlimit']))?></div>
            <div class="coupon_description truncateoverflowtext"><?=$row['coupon_description']?></div>
            <div class="coupon_code"><?=$row['coupon_code']?></div>
            <div class="coupon_manage">
                <div class="coupon_edit" data-coupon="<?=$row['coupon_id']?>"><i class="fa fa-pencil" aria-hidden="true"></i></div>
                <div class="coupon_delete" data-coupon="<?=$row['coupon_id']?>">[delete]</div>
            </div>
        </div>
        <?php $numr++;?>
    <?php } ?>
<?php } ?>