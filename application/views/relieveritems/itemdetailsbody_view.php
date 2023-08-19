<div class="itemdetails-tab active" data-tabview="infoarea">Main Info</div>
<div class="itemdetails-tab" data-tabview="history">History</div>
<div class="itemdetails-infoarea">
    <div class="leftpartitembody">
        <?=$keyinfo?>
        <?=$similar?>
    </div>
    <div class="centralpartbody">
        <?=$vendor_main?>
        <?=$itemimages?>
    </div>
    <div class="rightpartbody">
        <div class="pricesarea">
            <div class="relievers_vendorprices">
                <?=$vendor_prices?>
            </div>
            <?=$itemprices?>
        </div>
        <div class="relievers_customisation">
            <?=$customview?>
        </div>
        <div class="relievers_metasearch">
            <?=$metaview?>
            <div class="itemshippingarea"><?=$shipping?></div>
        </div>
    </div>
</div>
<?php if ($history_cnt > 0) { ?>
    <div class="itemdetails-history">
        <?=$history?>
    </div>
<?php } ?>
