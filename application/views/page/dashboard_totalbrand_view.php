<div class="brantotalarea">
    <?php foreach ($totals as $total) { ?>
        <div class="datarow">
            <div class="totalrowlabel"><?=$total['label']?></div>
            <div class="totalrowvalue"><?=$total['value']?></div>
        </div>
    <?php } ?>
</div>