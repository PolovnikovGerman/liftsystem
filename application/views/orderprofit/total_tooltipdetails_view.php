<div class="profit_tooltip_content">
    <div class="title"><?=$title?></div>
    <div class="profit_tooltip_row">
        <div class="profit_paramname ordertypenew">New:</div>
        <div class="profit_paramvalue"><?=($type=='qty' ? QTYOutput($new_val) : MoneyOutput($new_val,0))?> (<?=$new_perc?>%)</div>
    </div>
    <div class="profit_tooltip_row">
        <div class="profit_paramname ordertyperepeat">Repeat:</div>
        <div class="profit_paramvalue"><?=($type=='qty' ? QTYOutput($repeat_val) : MoneyOutput($repeat_val,0))?> (<?=$repeat_perc?>%)</div>
    </div>
    <div class="profit_tooltip_row">
        <div class="profit_paramname ordertypeblank">Blank:</div>
        <div class="profit_paramvalue"><?=($type=='qty' ? QTYOutput($blank_val) : MoneyOutput($blank_val,0))?> (<?=$blank_perc?>%)</div>
    </div>
</div>
