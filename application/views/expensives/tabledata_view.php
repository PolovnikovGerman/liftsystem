<div class="expensivesviewtable">
    <div class="datarow" id="newcalcrow" style="display: none;"></div>
    <?php $numpp = 1; ?>
    <?php foreach ($datas as $data) { ?>
        <div class="datarow" data-calc="<?=$data['calc_id']?>">
            <div class="expensivesviewtablerow <?=$numpp%2==0 ? 'whitedatarow' : 'greydatarow'?>">
                <div class="expensive-deeds">
                    <i class="fa fa-trash-o removeexpensive" data-calc="<?=$data['calc_id']?>"></i>
                </div>
                <div class="expensive-annually calc-edit"><?=$data['out_year']?></div>
                <div class="expensive-monthly calc-edit"><?=$data['out_month']?></div>
                <div class="expensive-weekly calc-edit"><?=$data['out_week']?></div>
                <div class="expensive-date calc-edit"><?=$data['out_date']?></div>
                <div class="expensive-method calc-edit"><?=$data['method']?></div>
                <div class="expensive-description calc-edit"><?=$data['description']?></div>
                <div class="expensive-quoter expensive-total-light"><?=$data['out_weektotal']?></div>
                <div class="expensive-yearly expensive-total-light"><?=$data['out_yeartotal']?></div>
                <div class="expensive-percent expensive-total-light"><?=empty($data['expense_perc']) ? '&nbsp;' : $data['expense_perc'].'%'?></div>
            </div>
        </div>
        <?php $numpp++;?>
    <?php } ?>
</div>
<!--<div class="datarow">-->
<!--    <input type="hidden" id="date_type" value="year"/>-->
<!--    <div class="expensivesviewtablerow greydatarow">-->
<!--        <div class="expensive-deeds">-->
<!--            <div class="expensive-savedata">-->
<!--                <i class="fa fa-check-circle"></i>-->
<!--            </div>-->
<!--            <div class="expensive-cancel">-->
<!--                <i class="fa fa-times-circle"></i>-->
<!--            </div>-->
<!--        </div>-->
<!--        <div class="expensive-annually"><i class="fa fa-circle checked"></i><input type="text" value="$15000.00"/></div>-->
<!--        <div class="expensive-monthly"><i class="fa fa-circle-o"></i><input type="text" readonly/></div>-->
<!--        <div class="expensive-weekly"><i class="fa fa-circle-o"></i><input type="text" readonly/></div>-->
<!--        <div class="expensive-date">Aug 15</div>-->
<!--        <div class="expensive-method">-->
<!--            <select>-->
<!--                <option>Amex Star</option>-->
<!--            </select>-->
<!--        </div>-->
<!--        <div class="expensive-description">-->
<!--            <input type="text" value="Clifton Rent + Extras"/>-->
<!--        </div>-->
<!--        <div class="expensive-quoter expensive-total-dark">$2,050.00</div>-->
<!--        <div class="expensive-yearly expensive-total-dark">$98,400.00</div>-->
<!--        <div class="expensive-percent expensive-total-dark">66.9%</div>-->
<!--    </div>-->
<!--</div>-->
