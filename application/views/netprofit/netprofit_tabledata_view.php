<div class="line_table_netprofit color_total white"><?= $totals ?></div>
<div class="netprofitabledataarea">
    <?php $nrow = 0;
    $tax = 1; ?>
    <?php foreach ($data as $row) { ?>
        <div class="line_table_netprofit <?= ($nrow % 2 == 0 ? 'greydatarow' : 'whitedatarow' ) ?>" id="nerpr<?= $row['datord'] ?>">
            <div class="tax_quarter <?= $row['tax_quarter_class'] ?>"><?= $row['tax_quarter']?></div>
            <div class="cell_week2 <?=$brand=='ALL' ? '' : 'editdata'?> <?=$row['datarowclass']?>" data-profit="<?=$row['profit_id'] ?>"><?= $row['week'] ?></div>
            <div class="cell_sales2 <?=$row['datarowclass']?>" <?= ($row['pcssold'] == 0 ? '' : 'data-content="# Pcs Sold ' . QTYOutput($row['pcssold']) . '"') ?>><?= $row['sales'] ?></div>
            <div class="cell_revenue2 <?=$row['datarowclass']?>"><?= $row['out_revenue'] ?></div>
            <div class="cell_gross_profit2 <?= $row['profit_class'] ?> <?=$row['datarowclass']?>" data-profitid="/accounting/netprofit_orders/?d=<?= $row['profit_id']?>&brand=<?=$brand?>"><?= $row['out_profit'] ?></div>
            <div class="cell_gross_profitpercent2 <?=$row['datarowclass']?>"><?= $row['out_profitperc'] ?></div>
            <div class="cell_operating2 <?= $row['operating_class'] ?> <?=$row['datarowclass']?>"><?= $row['out_operating'] ?></div>
            <div class="cell_advertising2 <?= $row['advertising_class'] ?> <?=$row['datarowclass']?>"><?= $row['out_advertising'] ?></div>
            <div class="cell_payroll2 <?= $row['payroll_class'] ?> <?=$row['datarowclass']?>"><?= $row['out_payroll'] ?></div>
            <div class="cell_projects2 <?= $row['projects_class'] ?> <?=$row['datarowclass']?>"><?= $row['out_projects'] ?></div>
            <div class="cell_w9work <?= $row['w9work_class'] ?> <?=$row['datarowclass']?>" data-netnote="<?=$row['profit_id']?>"><?=$row['out_w9']?></div>
            <div class="cell_purchases3 <?=$row['notesclass']?> <?= $row['purchases_class'] ?> <?=$row['datarowclass']?>" data-netnote="<?=$row['profit_id']?>">
                <?= $row['out_purchases'] ?>
            </div>
            <div class="cell_inbut2 <?=$row['notesclass']?> <?=$row['datarowclass']?>">
                <div class="imbox <?=($row['shownote']==1 ? 'shownote' : '')?>" data-netnote="/accounting/netprofit_weeknoteview/?d=<?=$row['profit_id']?>&brand=<?=$brand?>">
                    <img src="/img/accounting/list.png" alt="box" title="Notes"/>
                </div>
            </div>
            <div class="cell_total_costs2 <?= $row['totalcost_class'] ?> <?=$row['datarowclass']?>"><?= $row['out_totalcost'] ?></div>            
            <div class="cell_net_profit3 <?= $row['netprofit_class'] ?> <?=$row['datarowclass']?>"><?= $row['out_netprofit'] ?></div>
            <div class="cell_for_debtincl <?=$row['datarowclass']?>" data-debincl="<?=$row['profit_id']?>"><?= $row['debt_include'] ?></div>
            <div class="cell_for_saved2 <?= $row['saved_class'] ?> <?=$row['datarowclass']?>"><?= $row['out_saved'] ?></div>
            <div class="cell_for_owners2 <?= $row['out_od_class'] ?> <?=$row['datarowclass']?>"><?= $row['out_od'] ?></div>
            <div class="cell_for_debt2 <?= $row['debt_class'] ?> <?=$row['datarowclass']?>"><?= $row['out_debt'] ?></div>
        </div>
        <?php $nrow++; ?>
    <?php } ?>
    <?php if (isset($limitshow) && $limitshow != 0) { ?>
        <div class="line_table_netprofit <?= ($nrow % 2 == 0 ? 'grey' : 'white' ) ?>">
            <div class="tax_quarter ">&nbsp;</div>
            <div><span class="showallweekdata">[Show All]</span></div>
        </div>
<?php } ?>    
</div>
