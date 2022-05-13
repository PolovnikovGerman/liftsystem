<div class="inventoryhistory_table_row manualoutcomerow">
    <input type="hidden" value="<?=$color?>" id="colorinventory"/>
    <div class="instock_date">
        <input class="inventoryoutcomedateinpt" value="<?=date('m/d/Y')?>"/>
    </div>
    <div class="instock_recnum">
        <input class="inventoutcomerecnum" value="" placeholder="Rec #"/>
    </div>
    <div class="instock_descript">
        <input class="inventoutcomedescripinpt" value="" placeholder="Description"/>
    </div>
    <div class="instock_amount">
        <input class="inventoutcomeqtyinpt" value="" placeholder="QTY"/>
    </div>
    <div class="instock_balance">
        <span class="cancelinventoutcome"><i class="fa fa-times-circle" aria-hidden="true"></i></span>
        <span class="saveinventoutcome"><i class="fa fa-check-square" aria-hidden="true"></i></span>
    </div>
</div>
