<?php $nrow=0;?>
<?php foreach ($data as $row) { ?>
    <div class="itemsalesdatarow <?= ($nrow % 2 == 0 ? 'greydatarow' : 'whitedatarow' )?>" data-item="<?= $row['item_id'] ?>">
        <div class="edit lastcol">
            <input type="checkbox" class="itemsoldtotalchk" data-item="<?=$row['item_id']?>" <?=  in_array($row['item_id'], $itemchk) ? 'checked="checked"' : ''?>/>
        </div>
        <div class="itemnumber" data-imgurl="/analytics/itemimage?item=<?=$row['item_id']?>"><?=$row['item_number']?></div>
        <div class="itemname lastcol"><?=$row['item_name']?></div>
        <div class="repqty"><?=$row['out_curqty']?></div>
        <div class="ordqty lastcol" data-year="<?=$curyear?>"><?=$row['curordsale']?></div>
        <div class="repqty"><?=$row['out_prevqty']?></div>
        <div class="ordqty lastcol" data-year="<?=$prevyear?>"><?=$row['prevordsale']?></div>
        <div class="qtychange <?=$row['diffclass']?>"><?=$row['out_difqty']?></div>
        <div class="ordqty lastcol <?=$row['diffordclass']?>"><?=$row['difford']?></div>
        <div class="reprevenue"><?=$row['out_currevenue']?></div>
        <div class="reprevenue lastcol"><?=$row['out_prevrevenue']?></div>
        <div class="vendorname"><?=$row['vendor_name']?></div>
        <div class="cost"><?=$row['out_cost']?></div>
        <div class="curcog"><?=$row['out_cog']?></div>
        <div class="curprofit <?=$row['profitvalclass']?>"><?=$row['out_profit']?></div>
        <div class="curprofitperc lastcol <?=$row['profit_class']?>"><?=(empty($row['profit_perc']) ? '&nbsp;' : $row['profit_perc'].'%')?></div>
        <div class="imptcost <?=$row['imptcost']==0 ? 'emptyimptcost' : ''?>">
            <?=$row['out_imptcost']?>
        </div>
        <div class="imptcog"><?=$row['out_imptcog']?></div>
        <div class="imptprofit <?=$row['out_imprpofitclass']?>"><?=$row['out_imprpofit']?></div>
        <div class="imptprofitproc lastcol <?=$row['imptprofit_class']?>"><?=(empty($row['imptprofit_perc']) ? '&nbsp;' : $row['imptprofit_perc'].'%')?></div>
        <div class="savings <?=$row['savings_class']?>"><?=$row['out_savings']?></div>
    </div>
    <?php $nrow++;?>
<?php } ?>