<input type="hidden" id="sessionid" value="<?=$session?>"/>
<div class="numpp">
    <i class="fa fa-check-circle saveorderdata" aria-hidden="true"></i>
</div>
<div class="delrecord"><i class="fa fa-times-circle canceleditorderdata" aria-hidden="true" ></i></div>
<div class="orderdate editform">
    <input type="text" class="psorderdate psorderinput" data-fldname="printshop_date" value="<?=($printshop_date==0 ? '' : date('m/d/Y', $printshop_date))?>"/>
</div>
<div class="ordernum editform">
<!--   --><?php //if ($printshop_income_id<=0) { ?>
<!--        <input type="text" class="psordernum psorderinput" data-fldname="order_num" value="--><?php //=$order_num?><!--"/>-->
<!--   --><?php //} else { ?>
        <?=$order_num?>
<!--    --><?php //} ?>
</div>
<div class="customer editform"><?=(empty($customer) ? '&nbsp;' : $customer)?></div>
<div class="itemname editform" title="<?=$item_name?> - <?=$item_num?>"><?=$item_name?> - <?=$item_num?></div>
<div class="itemcolor editform"><?=$color?></div>
<div class="shipped editform">
    <input type="text" class="psshipped psorderinput" data-fldname="shipped" value="<?=$shipped?>" title="<?=$title?>"/>
</div>
<div class="kepted editform">
    <input type="text" class="pskepted psorderinput" data-fldname="kepted" value="<?=$kepted?>" title="<?=$title?>"/>
</div>
<div class="misprints editform">
    <input type="text" class="psmisprint psorderinput" data-fldname="misprint" value="<?=$misprint?>" title="<?=$title?>"/>
</div>
<div class="misprintproc editform"><?=$misprint_proc?></div>
<div class="totalqty editform"><?=QTYOutput($total_qty)?></div>
<div class="costea editform"><?=number_format($price,3)?></div>
<div class="addlcost editform"><?=number_format($extracost,3)?></div>
<div class="totalea editform"><?=number_format($totalea,3)?></div>
<div class="totaladdlcost editform"><?=MoneyOutput($extraitem)?></div>
<div class="itemscost editform"><?=MoneyOutput($costitem)?></div>
<div class="oranplate editform">
    <input type="text" class="psplateqty psorderinput" data-fldname="orangeplate" value="<?=$orangeplate?>"/>
</div>
<div class="blueplate editform">
    <input type="text" class="psplateqty psorderinput" data-fldname="blueplate" value="<?=$blueplate?>"/>                
</div>
<div class="beigeplate editform">
    <input type="text" class="psplateqty psorderinput" data-fldname="beigeplate" value="<?=$beigeplate?>"/>
</div>
<div class="totalplate editform"><?=QTYOutput($totalplates)?></div>
<div class="platecost editform"><?=MoneyOutput($platescost)?></div>
<div class="totalcost editform"><?=MoneyOutput($itemstotalcost)?></div>
<div class="misprintcost editform"><?=MoneyOutput($misprintcost)?></div>
