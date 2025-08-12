<div class="contentsubmenu" style="width: calc(100% - 20px)">
    <div class="brandmenusection">
        <?php foreach ($brandmenu as $item) : ?>
            <div class="contentsubmenu_item <?=str_replace('#','', $item['item_link'])?>lnk <?=$start==str_replace('#','', $item['item_link']) ? 'active' : ''?> " data-link="<?=str_replace('#','', $item['item_link'])?>">
                <?=$item['item_name']?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php if ($mastersection==1) : ?>
        <div class="mastermenusection">
            <?php foreach ($mastermenu as $item) : ?>
                <div class="contentsubmenu_item <?=str_replace('#','', $item['item_link'])?>lnk <?=$start==str_replace('#','', $item['item_link']) ? 'active' : ''?>" data-link="<?=str_replace('#','', $item['item_link'])?>">
                    <?=$item['item_name']?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<div class="contentdata_view">
    <?php if (isset($itemsview)) { ?>
        <div class="dbcontentarea" id="btitemsview" style="display: none;"><?=$itemsview?></div>
    <?php } ?>
    <?php if (isset($customersview)) { ?>
        <div class="dbcontentarea" id="btcustomers" style="display: none;"><?=$customersview?></div>
    <?php } ?>
    <?php if (isset($legacyview)) { ?>
        <div class="dbcontentarea" id="legacyview" style="display: none; background: #ededed;"><?=$legacyview?></div>
    <?php } ?>
    <?php if (isset($sbpagesview)) { ?>
        <div class="dbcontentarea" id="sbpages" style="display: none; background: #ededed;"><?=$sbpagesview?></div>
    <?php } ?>
    <?php if (isset($btpagesview)) { ?>
        <div class="dbcontentarea" id="btpages" style="display: none; background: #ededed;"><?=$btpagesview?></div>
    <?php } ?>
    <?php if (isset($shippingview)) { ?>
        <div class="dbcontentarea" id="shippingview" style="display: none;"><?=$shippingview?></div>
    <?php } ?>
    <?php if (isset($sritemsview)) { ?>
        <div class="dbcontentarea" id="sritemsview" style="display: none;"><?=$sritemsview?></div>
    <?php } ?>
    <?php if (isset($vendorsview)) { ?>
        <div class="dbcontentarea" id="mastervendors" style="display: none;"><?=$vendorsview?></div>
    <?php } ?>
    <?php if (isset($inventoryview)) { ?>
        <div class="dbcontentarea" id="inventoryview" style="display: none;"><?=$inventoryview?></div>
    <?php } ?>
    <?php if (isset($settingsview)) { ?>
        <div class="dbcontentarea" id="settingsview" style="display: none;"><?=$settingsview?></div>
    <?php } ?>
    <div class="dbcontentarea" id="itemdetailsview" style="display: none;">
        <div class="left_maincontent"></div>
        <div class="right_maincontent"></div>
    </div>
</div>
