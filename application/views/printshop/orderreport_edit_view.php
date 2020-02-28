<input type="hidden" id="sessionid" value="<?=$session?>"/>
<div class="numpp">
    <i class="fa fa-check-circle saveorderdata" aria-hidden="true"></i>
</div>
<div class="delrecord"><i class="fa fa-times-circle canceleditorderdata" aria-hidden="true" ></i></div>
<div class="orderdate editform">
    <input type="text" class="psorderdate psorderinput" data-fldname="printshop_date" value="<?=($printshop_date==0 ? '' : date('m/d/Y', $printshop_date))?>"/>
</div>
<div class="ordernum editform">
   <?php if ($printshop_income_id<=0) { ?>
        <input type="text" class="psordernum psorderinput" data-fldname="order_num" value="<?=$order_num?>"/>
   <?php } else { ?>
        <?=$order_num?>
    <?php } ?>
</div>
<div class="customer editform"><?=(empty($customer) ? '&nbsp;' : $customer)?></div>
<div class="itemname editform">
    <select class="psprintitem psorderselect" data-fldname="printshop_item_id">
        <option value="" <?=($printshop_item_id=='' ? 'selected="selected"' : '')?> >...</option>
        <?php foreach ($items as $irow) { ?>
        <option value="<?=$irow['printshop_item_id']?>" <?=($irow['printshop_item_id']==$printshop_item_id ? 'selected="selected"' : '')?>><?=$irow['item_name']?></option>
        <?php } ?>
    </select>
</div>
<div class="itemcolor editform">
    <select class="psprintcolor psorderselect" data-fldname="printshop_color_id">
        <option value="" <?=($printshop_color_id=='' ? 'selected="selected"' : '')?>>...</option>
        <?php foreach ($colors as $crow) { ?>
        <option value="<?=$crow['printshop_color_id']?>" <?=($crow['printshop_color_id']==$printshop_color_id ? 'selected="selected"' : '')?>><?=$crow['color']?></option>
        <?php } ?>
    </select>
</div>
<div class="shipped editform">
    <input type="text" class="psshipped psorderinput" data-fldname="shipped" value="<?=$shipped?>"/>
</div>
<div class="kepted editform">
    <input type="text" class="pskepted psorderinput" data-fldname="kepted" value="<?=$kepted?>"/>
</div>
<div class="misprints editform">
    <input type="text" class="psmisprint psorderinput" data-fldname="misprint" value="<?=$misprint?>"/>
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
<div class="totalplate editform"><?=QTYOutput($totalplates)?></div>
<div class="platecost editform"><?=MoneyOutput($platescost)?></div>
<div class="totalcost editform"><?=MoneyOutput($itemstotalcost)?></div>
<div class="misprintcost editform"><?=MoneyOutput($misprintcost)?></div>
