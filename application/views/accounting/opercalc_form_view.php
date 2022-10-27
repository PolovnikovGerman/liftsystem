<input type="hidden" id="calcsort" value="<?=$calcsort?>"/>
<input type="hidden" id="calcdirec" value="<?=$calcdirec?>"/>
<input type="hidden" id="expensesviewbrand" value="<?=$brand?>">
<div class="calculator">
    <div class="calculatortitle">
        <div class="calc_cell-00">&nbsp;</div>
        <div class="calc_cell-01 sortcalc" id="sortdescription">
            <div class="sortcalclnk">
                <?php if ($calcsort=='description') {?>            
                    <img src="/img/icons/<?=($calcdirec=='desc' ? 'sort_down.png' : 'sort_up.png')?>" alt="Sort"/>
                <?php } ?>            
            </div>
            Description
        </div>
        <div class="calc_cell-02 sortcalc" id="sortmonthly">            
            <div class="sortcalclnk">
                <?php if ($calcsort=='monthly') {?>
                    <img src="/img/icons/<?=($calcdirec=='desc' ? 'sort_down.png' : 'sort_up.png')?>" alt="Sort"/>
                <?php } ?>                        
            </div>            
            Monthly
        </div>
        <div class="calc_cell-03 sortcalc" id="sortweekly">
            <div class="sortcalclnk">
                <?php if ($calcsort=='weekly') {?>            
                    <img src="/img/icons/<?=($calcdirec=='desc' ? 'sort_down.png' : 'sort_up.png')?>" alt="Sort"/>
                <?php } ?>            
            </div>
            Weekly
        </div>
        <div class="calc_cell-03 sortcalc" id="sortquarta">            
            <div class="sortcalclnk">
                <?php if ($calcsort=='quarta') {?>
                    <img src="/img/icons/<?=($calcdirec=='desc' ? 'sort_down.png' : 'sort_up.png')?>" alt="Sort"/>
                <?php } ?>            
            </div>            
            4 / 12
        </div>
        <div class="calc_cell-04 sortcalc" id="sortyearly">
            <div class="sortcalclnk">
                <?php if ($calcsort=='yearly') {?>
                    <img src="/img/icons/<?=($calcdirec=='desc' ? 'sort_down.png' : 'sort_up.png')?>" alt="Sort"/>
                <?php } ?>
            </div>
            Yearly
        </div>
        <div class="calc_cell-05">%</div>
        <div class="calc_cell-last">&nbsp;</div>
    </div>    
    <div class="calc-content"></div>    
    <div class="calculatorfoot">
        <div class="calc-grandtotal-start">&nbsp;</div>
        <div class="calc-grandtotal">Grand Total:</div>
        <div class="calc-totalmonthly">&nbsp;</div>
        <div class="calc-totalweekly">&nbsp;</div>
        <div class="calc-totalquart">&nbsp;</div>
        <div class="calc-totalyear">&nbsp;</div>
        <div class="calc-totalpercent">&nbsp;</div>
        <div class="calc-grandtotal-end">&nbsp;</div>
    </div>
</div>
