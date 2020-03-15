<div class="closedtotal_area">
    <div class="closeprevview <?=($prev==1 ? 'active' : '')?>" data-start="<?=$dateend?>">&nbsp;</div>
    <?php foreach ($data as $row) { ?>
        <div class="closeviewmonthlabel"><?=$row['label']?></div>
        <div class="closeviewmonthdata"><?=$row['percent']?></div>
    <?php } ?>
    <div class="closenextview <?=($next==1 ? 'active' : '')?>" data-start="<?=$datestart?>">&nbsp;</div>
</div>
<div class="closedtotalshowfeature">Show Future Weeks</div>