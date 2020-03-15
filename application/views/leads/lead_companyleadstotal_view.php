<div class="leadcmptotaldetailsarea">
    <div class="ordertotaldetailrow">
        <div class='ordertotalperiod'><?= $label ?></div>
    </div>
    <div class="leadcmptotalhead">
        <div class="username"><br/>Rep</div>
        <div class="leadtotals"># New<br/>Leads</div>
        <div class="leadtotals"># Wrk<br/>Leads</div>
        <div class="leadtotals"># Out<br/>Calls</div>
    </div>
    <div class="leadcmptotaldata">
        <div class="leadcmptotaldatarow total">
            <div class="username">Total:</div>
            <div class="leadtotals"><?=$totals['newleads']?></div>
            <div class="leadtotals"><?=$totals['wrkleads']?></div>
            <div class="leadtotals outcalls"><?=$totals['outcalls']?></div>
        </div>
        <?php $numpp=1;?>
        <?php foreach ($leads as $row) { ?>
            <div class="leadcmptotaldatarow <?=($numpp%2==0 ? 'greydatarow' : 'whitedatarow')?>">
                <div class="username"><?=$row['user_name']?></div>
                <div class="leadtotals"><?=$row['newleads']?></div>
                <div class="leadtotals"><?=$row['wrkleads']?></div>
                <div class="leadtotals outcalls"><?=$row['outcalls']?></div>
            </div>
            <?php $numpp++; ?>
        <?php } ?>
    </div>
</div>