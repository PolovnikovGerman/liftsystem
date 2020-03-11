<input type="hidden" id="w9worksortfld" value="<?=$w9sortfld?>" />
<input type="hidden" id="purchasesortfld" value="<?=$purchasesortfld?>" />
<input type="hidden" id="w9worksortdirec" value="<?=$w9sortdir?>" />
<input type="hidden" id="purchasesortdirec" value="<?=$purchasesortdir?>"/>
<div class="tablearticlehead">
    <div class="articletitle">W9 Work: </div>
    <div class="articlevalue"><?= MoneyOutput($w9totals) ?></div>
    <div class="managecategory" data-category="W9">manage categories</div>    
</div>
<div class="tableheaddatarow w9worktotaltablehead">
    <div class="category_name">
        Category
        <div class="tablesort" id="w9categorysort" <?=$w9sortfld=='category_name' ? 'style="display: block;"' : ''?>>
            <?=$w9sortdir=='asc' ? $sortascicon : $sortdescicon?>            
        </div>
    </div>
    <div class="amountvalue">
        Amount
        <div class="tablesort" id="w9amountsort" <?=$w9sortfld=='amount' ? 'style="display: block;"' : ''?>>
            <?=$w9sortdir=='asc' ? $sortascicon : $sortdescicon?>            
        </div>
    </div>
    <div class="amountperc">%
        <div class="tablesort" id="w9amounpercsort" <?=$w9sortfld=='amount_perc' ? 'style="display: block;"' : ''?>>
            <?=$w9sortdir=='asc' ? $sortascicon : $sortdescicon?>            
        </div>
    </div>        
</div>
<div class="tablebodyarea w9work">
    <?php $nrow = 0; ?>
    <?php foreach ($w9details as $row) { ?>
        <div class="tabledatarow <?= $nrow % 2 == 0 ? 'grey' : 'white' ?>">
            <div class="category_name entered" href="/finance/netprofit_showdetais?cat=<?=$row['netprofit_category_id']?>&year=<?=$year?>" title="<?= $row['category_name'] ?>"><?= $row['category_name'] ?></div>
            <div class="amountvalue <?= $row['amount_class'] ?>"><?= $row['amount_out'] ?></div>
            <div class="amountperc"><?= $row['amount_perc'] ?>%</div>
        </div>
        <?php $nrow++; ?>
    <?php } ?>
    <?php if ($nrow < 5) { ?>
        <?php for ($i = $nrow; $i < 5; $i++) { ?>
            <div class="tabledatarow <?= $i % 2 == 0 ? 'grey' : 'white' ?>">
                <div class="category_name">&nbsp;</div>
                <div class="amountvalue">&nbsp;</div>
                <div class="amountperc">&nbsp;</div>        
            </div>
        <?php } ?>
    <?php } ?>
</div>
<div class="tablearticlehead">
    <div class="articletitle">Purchases: </div>
    <div class="articlevalue"><?= MoneyOutput($purchasetotals) ?></div>
    <div class="managecategory" data-category="Purchase">manage categories</div>    
</div>
<div class="tableheaddatarow purchasetotaltablehead">
    <div class="category_name">
        Category
        <div class="tablesort" id="purchasecategorysort" <?=$purchasesortfld=='category_name' ? 'style="display: block;"' : ''?>>
            <?=$purchasesortdir=='asc' ? $sortascicon : $sortdescicon?>            
        </div>
    </div>
    <div class="amountvalue">
        Amount
        <div class="tablesort" id="purchaseamountsort" <?=$purchasesortfld=='amount' ? 'style="display: block;"' : ''?>>
            <?=$purchasesortdir=='asc' ? $sortascicon : $sortdescicon?>
        </div>
    </div>
    <div class="amountperc">
        %
        <div class="tablesort" id="purchaseamounpercsort" <?=$purchasesortfld=='amount_perc' ? 'style="display: block;"' : ''?>>
            <?=$purchasesortdir=='asc' ? $sortascicon : $sortdescicon?>
        </div>
    </div>        
</div>
<div class="tablebodyarea purchases">
    <?php $nrow = 0; ?>
    <?php foreach ($purchasedetails as $row) { ?>
        <div class="tabledatarow <?= $nrow % 2 == 0 ? 'grey' : 'white' ?>">
            <div class="category_name entered" href="/finance/netprofit_showdetais?cat=<?=$row['netprofit_category_id']?>&year=<?=$year?>" title="<?= $row['category_name'] ?>"><?= $row['category_name'] ?></div>
            <div class="amountvalue <?= $row['amount_class'] ?>"><?= $row['amount_out'] ?></div>
            <div class="amountperc"><?= $row['amount_perc'] ?>%</div>
        </div>
        <?php $nrow++; ?>
    <?php } ?>
    <?php if ($nrow < 20) { ?>
        <?php for ($i = $nrow; $i < 20; $i++) { ?>
            <div class="tabledatarow <?= $i % 2 == 0 ? 'grey' : 'white' ?>">
                <div class="category_name">&nbsp;</div>
                <div class="amountvalue">&nbsp;</div>
                <div class="amountperc">&nbsp;</div>        
            </div>
        <?php } ?>
    <?php } ?>
</div>