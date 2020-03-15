<div class="totallabel lastcol">Total of Checked</div>
<div class="repqty" <?=(empty($title_curqty) ? '' : 'title="'.$title_curqty.'"')?>><?= $out_curqty ?></div>
<div class="ordqty lastcol" <?=(empty($title_curordsale) ? '' : 'title="'.$title_curordsale.'"')?>><?=$out_curordsale ?></div>
<div class="repqty" <?=(empty($title_prevqty) ? '' : 'title="'.$title_prevqty.'"')?> ><?=$out_prevqty ?></div>
<div class="ordqty lastcol" <?=(empty($title_prevordsale) ? '' : 'title="'.$title_prevordsale.'"')?> ><?= $out_prevordsale ?></div>
<div class="qtychange <?= $diffclass ?>"><?=$out_difqty ?></div>
<div class="ordqty lastcol <?=$diffordclass?>"><?= $difford ?></div>
<div class="reprevenue" <?=(empty($title_currevenue) ? '' : 'title="'.$title_currevenue.'"')?>><?=$out_currevenue ?></div>
<div class="reprevenue lastcol" <?=(empty($title_prevrevenue) ? '' : 'title="'.$title_prevrevenue.'"')?>><?= $out_prevrevenue ?></div>
<div class="vendorname">&nbsp;</div>
<div class="cost">&nbsp;</div>
<div class="curcog" <?=(empty($title_cog) ? '' : 'title="'.$title_cog.'"')?>><?=$out_cog?></div>
<div class="curprofit <?= $profitvalclass ?>" <?=(empty($title_profit) ? '' : 'title="'.$title_profit.'"')?>><?=$out_profit ?></div>
<div class="curprofitperc lastcol <?= $profit_class ?>"><?= (empty($profit_perc) ? '&nbsp;' : $profit_perc . '%') ?></div>
<div class="imptcost">&nbsp;</div>
<div class="imptcog" <?=(empty($title_imptcog) ? '' : 'title="'.$title_imptcog.'"')?>><?=$out_imptcog ?></div>
<div class="imptprofit <?= $out_imprpofitclass ?>" <?=(empty($title_imprpofit) ? '' : 'title="'.$title_imprpofit.'"')?>><?=$out_imprpofit ?></div>
<div class="imptprofitproc lastcol <?= $imptprofit_class ?>"><?= (empty($imptprofit_perc) ? '&nbsp;' : $imptprofit_perc . '%') ?></div>
<div class="savings <?= $savings_class ?>" <?=(empty($title_savings) ? '' : 'title="'.$title_savings.'"')?>><?= $out_savings ?></div>
