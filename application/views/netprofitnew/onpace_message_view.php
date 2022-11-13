<div class="onpacemessage_area">
    <div class="datarow">
        <div class="onpacetitle">&nbsp;</div>
        <?php foreach ($details as $detail) { ?>
            <div class="onpacemessage_month"><?=$detail['month']?></div>
        <?php } ?>
    </div>
    <div class="datarow">
        <div class="onpacetitle">%</div>
        <?php foreach ($details as $detail) { ?>
            <div class="onpacemessage_monthval"><?=$detail['percent']?></div>
        <?php } ?>
    </div>
</div>