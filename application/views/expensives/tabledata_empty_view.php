<div class="expensivesviewtable">
    <div class="datarow" id="newcalcrow" style="display: none;"></div>
    <div class="datarow">
        <div class="expensivesviewtablerow greydatarow">
            <div class="expensive-emptydata">No data</div>
        </div>
    </div>
    <?php $numpp = 2; ?>
    <?php for ($i=0; $i<21;$i++) { ?>
    <div class="datarow">
        <div class="expensivesviewtablerow <?=$numpp%2==0 ? 'whitedatarow' : 'greydatarow'?>">
            <div class="expensive-emptydata">&nbsp;</div>
        </div>
        <?php $numpp++;?>
    </div>
    <?php } ?>
</div>
