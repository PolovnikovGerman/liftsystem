<div class="page_container">
    <div class="right_maincontent">
        <div class="settings_submenu">
            <?=$submenu?>
        </div>
        <?php if (isset($btshippingview)) { ?>
            <div class="sitesettingcontentarea" id="btshippingview" style="display: none;"><?=$btshippingview?></div>
        <?php } ?>
        <?php if (isset($btnotificationsview)) { ?>
            <div class="sitesettingcontentarea" id="btnotificationsview" style="display: none;"><?=$btnotificationsview?></div>
        <?php } ?>
        <?php if (isset($btrushoptionsview)) { ?>
            <div class="sitesettingcontentarea" id="btrushoptionsview" style="display: none;"><?=$btrushoptionsview?></div>
        <?php } ?>
        <?php if (isset($sbshippingview)) { ?>
            <div class="sitesettingcontentarea" id="sbshippingview" style="display: none;"><?=$sbshippingview?></div>
        <?php } ?>
        <?php if (isset($sbnotificationsview)) { ?>
            <div class="sitesettingcontentarea" id="sbnotificationsview" style="display: none;"><?=$sbnotificationsview?></div>
        <?php } ?>
        <?php if (isset($sbrushoptionsview)) { ?>
            <div class="sitesettingcontentarea" id="sbrushoptionsview" style="display: none;"><?=$sbrushoptionsview?></div>
        <?php } ?>
    </div>
</div>