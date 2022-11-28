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
                <div class="expensive-quoter <?=$numpp%2==0 ? 'expensive-total-light' : 'expensive-total-dark'?>"><?=$data['out_weektotal']?></div>
                <div class="expensive-yearly <?=$numpp%2==0 ? 'expensive-total-light' : 'expensive-total-dark'?>"><?=$data['out_yeartotal']?></div>
                <div class="expensive-percent <?=$numpp%2==0 ? 'expensive-total-light' : 'expensive-total-dark'?> <?=$expand==1 ? 'expandcell' : ''?>"><?=empty($data['expense_perc']) ? '&nbsp;' : $data['expense_perc'].'%'?></div>
            </div>
        </div>
        <?php $numpp++;?>
    <?php } ?>
    <?php if ($numpp < 23) { ?>
        <?php for ($i=$numpp; $i<23;$i++) { ?>
            <div class="datarow">
                <div class="expensivesviewtablerow <?=$numpp%2==0 ? 'whitedatarow' : 'greydatarow'?>">
                    <div class="expensive-deeds">&nbsp;</div>
                    <div class="expensive-annually">&nbsp;</div>
                    <div class="expensive-monthly">&nbsp;</div>
                    <div class="expensive-weekly">&nbsp;</div>
                    <div class="expensive-date">&nbsp;</div>
                    <div class="expensive-method">&nbsp;</div>
                    <div class="expensive-description">&nbsp;</div>
                    <div class="expensive-quoter <?=$numpp%2==0 ? 'expensive-total-light' : 'expensive-total-dark'?>">&nbsp;</div>
                    <div class="expensive-yearly <?=$numpp%2==0 ? 'expensive-total-light' : 'expensive-total-dark'?>">&nbsp;</div>
                    <div class="expensive-percent <?=$numpp%2==0 ? 'expensive-total-light' : 'expensive-total-dark'?> expandcell">&nbsp;</div>
                </div>
                <?php $numpp++;?>
            </div>
        <?php } ?>

    <?php } ?>
</div>
