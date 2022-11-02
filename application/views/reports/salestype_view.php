<input type="hidden" id="salestypereportbrand" value="<?=$brand?>"/>
<div class="salestype_viewarea">
    <?php if (!empty($customs_view)) { ?>
        <div class="salestype_dataarea">
            <div class="salestype_reportarea">Custom Shaped Stress Balls</div>
            <?=$customs_view?>
        </div>
    <?php } ?>
    <?php if (!empty($stocks_view)) { ?>
        <div class="salestype_dataarea">
            <div class="salestype_reportarea">Stock Shape Stress Balls</div>
            <?=$stocks_view?>
        </div>
    <?php } ?>
    <?php if (!empty($ariel_view)) { ?>
        <div class="salestype_dataarea">
            <div class="salestype_reportarea">Ariel Stress Balls</div>
            <?=$ariel_view?>
        </div>
    <?php } ?>
    <?php if (!empty($alpi_view)) { ?>
        <div class="salestype_dataarea">
            <div class="salestype_reportarea">Alpi Stress Balls</div>
            <?=$alpi_view?>
        </div>
    <?php } ?>
    <?php if (!empty($mailine_view)) { ?>
        <div class="salestype_dataarea">
            <div class="salestype_reportarea">Mailine Stress Balls</div>
            <?=$mailine_view?>
        </div>
    <?php } ?>
    <?php if (!empty($esp_view)) { ?>
        <div class="salestype_dataarea">
            <div class="salestype_reportarea">Other Stress Balls</div>
            <?=$esp_view?>
        </div>
    <?php } ?>
    <?php if (!empty($hit_view)) { ?>
        <div class="salestype_dataarea">
            <div class="salestype_reportarea">Hit Items</div>
            <?=$hit_view?>
        </div>
    <?php } ?>
    <?php if (!empty($other_view)) { ?>
        <div class="salestype_dataarea">
            <div class="salestype_reportarea">ESP / Other Items</div>
            <?=$other_view?>
        </div>
    <?php } ?>
</div>
