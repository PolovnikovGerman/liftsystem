<div class="inventsalesreport" id="inventsalesrep">
    <input type="hidden" id="inventorytotals" value="<?= $totals ?>"/>
    <input type="hidden" id="showonlinemaxvalue" value="0"/>
    <input type="hidden" id="inventsalesreportbrand" value="<?=$brand?>"/>

    <div class="head_title">
        <div class="on_boat">&nbsp;</div>
        <div class="history_low">
            <div class="critical_low">
                <div class="color_critical_low"></div>
                <div class="text_critical_low">Severe (25% & Under)</div>
            </div>
            <div class="getting_low">
                <div class="color_getting_low"></div>
                <div class="text_getting_low">Low (50% & Under)</div>
            </div>
        </div>
    </div>
    <?php echo $fullview?>
</div>
