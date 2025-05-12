<?=$menu_view?>
<div class="contentdata_view">
    <?php if (isset($reportsalestypeview)) { ?>
        <div class="analyticcontentarea" id="reportsalestypeview" style="display: none;"><?=$reportsalestypeview?></div>
    <?php } ?>
    <?php if (isset($reportitemsoldyearview)) { ?>
        <div class="analyticcontentarea" id="reportitemsoldyearview" style="display: none;"><?=$reportitemsoldyearview?></div>
    <?php } ?>
    <?php if (isset($reportitemsoldmonthview)) { ?>
        <div class="analyticcontentarea" id="reportitemsoldmonthview" style="display: none;"><?=$reportitemsoldmonthview?></div>
    <?php } ?>
    <?php if (isset($checkoutreportview)) { ?>
        <div class="analyticcontentarea" id="checkoutreportview" style="display: none;"><?=$checkoutreportview?></div>
    <?php } ?>
</div>
