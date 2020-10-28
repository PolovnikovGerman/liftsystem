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
    </div>
</div>