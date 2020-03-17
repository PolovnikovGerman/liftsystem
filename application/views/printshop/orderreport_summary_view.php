<div class="numpp">
    <i class="fa fa-plus-circle addnewprintshopincome" aria-hidden="true"></i>
</div>
<div class="summarylabel">Totals:</div>
<div class="ordernum">&nbsp;</div>
<div class="customer ">&nbsp;</div>
<div class="itemname ">&nbsp;</div>
<div class="itemcolor ">&nbsp;</div>    
<div class="shipped "><?= QTYOutput($shipped) ?></div>
<div class="kepted "><?= QTYOutput($kepted) ?></div>
<div class="misprints "><?= QTYOutput($misprint) ?></div>
<div class="misprintproc "><?= $misprintperc ?></div>
<div class="totalqty "><?= QTYOutput($totalqty) ?></div>
<div class="costea ">&nbsp;</div>
<div class="addlcost ">&nbsp;</div>
<div class="totalea ">&nbsp;</div>
<div class="totaladdlcost"><?= MoneyOutput($total_extra,0)?></div>
<div class="itemscost"><?=MoneyOutput($item_cost,0)?></div>
<div class="oranplate"><?= QTYOutput($oranplate,1) ?></div>
<div class="blueplate"><?= QTYOutput($blueplate,1) ?></div>
<div class="beigeplate"><?= QTYOutput($beigeplate,1) ?></div>
<div class="totalplate "><?= QTYOutput($totalplate) ?></div>
<div class="platecost "><?= MoneyOutput($platecost,0) ?></div>
<div class="totalcost "><?= MoneyOutput($total_cost,0) ?></div>
<div class="misprintcost "><?= MoneyOutput($misprint_cost,0) ?></div>    
