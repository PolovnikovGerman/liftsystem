<div class="taxratesarea">
    <div class="header"><?=$title?></div>
    <div class="tabledata">
        <div class="tabledataheader">
            <div class="ratevalue">Rates</div>
            <div class="incomevalues">Income</div>
        </div>
        <?php foreach ($rates as $row) { ?>
        <div class="tabledatarow">
            <div class="ratevalue"><?=$row['rate']?></div>
            <div class="incomevalues"><?=$row['income']?></div>            
        </div>
        <?php } ?>
    </div>
</div>