<?php $nrow=0;?>
<?php foreach ($data as $row) {?>
<div class="calcdatarow <?=($nrow%2==0 ? 'greydatarow' : 'whitedatarow')?>" id="calcrow<?=$row['calc_id']?>">
    <div class="calc-actions <?=$brand=='ALL' ? '' : 'calc-delete'?>" data-calcid="<?=$row['calc_id']?>">
        <?php if ($brand=='ALL') { ?>
            &nbsp;
        <?php } else { ?>
            <i class="fa fa-trash" aria-hidden="true" title="Delete row"></i>
        <?php } ?>
    </div>
    <div class="calc-descrdata <?=$brand=='ALL' ? '' : 'calc-edit'?>" data-calcid="<?=$row['calc_id']?>"><?=$row['description']?></div>
    <div class="calc-montdata"><?=$row['out_month']?></div>
    <div class="calc-weekdata"><?=$row['out_week']?></div>
    <div class="calc-quartadat <?=($nrow%2==0 ? 'grey' : '')?>"><?=$row['out_quarta']?></div>
    <div class="calc-yeardat <?=($nrow%2==0 ? 'grey' : '')?>"><?=$row['out_year']?></div>
    <div class="calc-expenseperc <?=($nrow%2==0 ? 'grey' : '')?>"><?=$row['expense_perc']?>%</div>
    <?php $nrow++;?>
</div>
<?php } ?>
<?php if ($brand!=='ALL') { ?>
    <div class="calcdatarow  newcalcrow <?=($nrow%2==0 ? 'grey' : 'white')?>">
        Click here to add new record
    </div>
<?php } ?>
