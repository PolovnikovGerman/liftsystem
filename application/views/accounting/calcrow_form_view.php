<input type="hidden" id="calc_id" name="calc_id" value="<?=$calc_id?>"/>
<div class="calc-descrdata-edit" style="padding-right: 0px;padding-top: 0px;width: 370px;padding-left: 2px;height: 27px;">
    <div class="calc-actions">
        <div class="applycalc">
            <a id="applycalc" href="javascript:void(0);" onclick="savecalcrow();"><img src="/img/icons/accept.png" alt="apply" title="Apply changes"/></a>
        </div>
        <div class="cancelcalc">
            <a id="cancelcalc" href="javascript:void(0);" onclick="init_calc();"><img src="/img/icons/cancel.png" alt="cancel" title="Cancel changes"/></a>
        </div>
    </div>        
    <input type="text" class="calcdescript" id="description" name="description" value="<?=$description?>"/>        
</div>
<div class="calc-montdata" style="padding-top: 0px;height: 27px;">
    <input type="text" class="calcmonthdata" id="monthsum" name="monthsum" value="<?=$monthsum?>" <?=($monthsum==='' ? 'readonly="readonly"' : '')?> />
</div>
<div class="calc-weekdata" style="padding-top: 0px;height: 27px;">
    <input type="text" class="calcweekdata" id="weeksum" name="weeksum" value="<?=$weeksum?>" <?=($weeksum==='' ? 'readonly="readonly"' : '')?> />
</div>
<div class="calc-quartadat"><?=$quartasum?></div>
<div class="calc-yeardat"><?=$yearsum?></div>
<div class="calc-expenseperc"><?=$expense_perc?>%</div>
