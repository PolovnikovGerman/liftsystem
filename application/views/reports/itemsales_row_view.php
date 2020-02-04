<div class="edit lastcol">
    <input type="checkbox" class="itemsoldtotalchk" data-item="<?=$item_id?>" <?=in_array($item_id, $itemchk) ? 'checked="checked"' : ''?> />
</div>
<div class="itemnumber" data-imgurl="/reports/itemimage?item=<?=$item_id?>"><?= $item_number ?></div>
<div class="itemname lastcol"><?=$item_name ?></div>
<div class="repqty"><?=$out_curqty ?></div>
<div class="ordqty lastcol"><?=$curordsale ?></div>
<div class="repqty"><?=$out_prevqty ?></div>
<div class="ordqty lastcol"><?=$prevordsale ?></div>
<div class="qtychange <?=$diffclass ?>"><?=$out_difqty ?></div>
<div class="ordqty lastcol <?=$diffordclass ?>"><?=$difford ?></div>
<div class="reprevenue"><?=$out_currevenue ?></div>
<div class="reprevenue lastcol"><?=$out_prevrevenue ?></div>
<div class="vendorname"><?=$vendor_name ?></div>
<div class="cost"><?=$out_cost ?></div>
<div class="curcog"><?=$out_cog ?></div>
<div class="curprofit <?=$profitvalclass ?>"><?= $out_profit ?></div>
<div class="curprofitperc lastcol <?=$profit_class ?>"><?= (empty($profit_perc) ? '&nbsp;' : $profit_perc . '%') ?></div>
<div class="imptcost <?=$imptcost==0 ? 'emptyimptcost' : ''?>"><?=$out_imptcost ?></div>
<div class="imptcog"><?=$out_imptcog ?></div>        
<div class="imptprofit <?=$out_imprpofitclass ?>"><?=$out_imprpofit ?></div>
<div class="imptprofitproc lastcol <?=$imptprofit_class ?>"><?=(empty($imptprofit_perc) ? '&nbsp;' : $imptprofit_perc . '%') ?></div>
<div class="savings <?=$savings_class ?>"><?=$out_savings?></div>        
