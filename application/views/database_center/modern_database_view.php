<div class="contentsubmenu" style="width: calc(100% - 20px)">
    <div class="brandsubmenu">
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
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="editModalLabel">New message</h4>
            </div>
            <div class="modal-body" style="float: left;"></div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
<div class="modal fade" id="pageModal" tabindex="-1" role="dialog" aria-labelledby="pageModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="pageModalLabel">New message</h4>
            </div>
            <div class="modal-body" style="float: left;"></div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
<div class="modal fade" id="itemDetailsModal" tabindex="-1" role="dialog" aria-labelledby="itemDetailsModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><img src="/img/vendors/close_popup.png"></span></button>
                <h4 class="modal-title" id="itemDetailsModalLabel">New message</h4>
            </div>
            <div class="modal-body" style="float: left;"></div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
<div class="modal fade" id="itemImagesModal" tabindex="-1" role="dialog" aria-labelledby="itemImagesModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="itemImagesModalLabel">New message</h4>
            </div>
            <div class="modal-body" style="float: left;"></div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
<div class="modal fade" id="vendorDetailsModal" tabindex="-1" role="dialog" aria-labelledby="vendorDetailsModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><img src="/img/vendors/close_popup.png"></span>
                </button>
                <h4 class="modal-title" id="vendorDetailsModalLabel">New message</h4>
            </div>
            <div class="modal-body" style="float: left;"></div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalEditInventPrice" tabindex="-1" role="dialog" aria-labelledby="modalEditInventPriceLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalEditInventPriceLabel"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="float: left;"></div>
            <!-- <div class="modal-footer"></div> -->
        </div>
    </div>
</div>
<div class="modal fade" id="modalEditInventHistory" tabindex="-1" role="dialog" aria-labelledby="modalEditInventHistoryLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalEditInventHistoryLabel"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="float: left;"></div>
            <!-- <div class="modal-footer"></div> -->
        </div>
    </div>
</div>
<div class="modal fade" id="modalEditInventItem" tabindex="-1" role="dialog" aria-labelledby="modalEditInventItemLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalEditInventItemLabel"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="float: left;"></div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalEditInventColor" tabindex="-1" role="dialog" aria-labelledby="modalEditInventColorLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalEditInventColorLabel"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="float: left;"></div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
<div class="modal fade" id="artModal" tabindex="-1" role="dialog" aria-labelledby="artModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="artModalLabel">New message</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="float: left;"></div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
<div style="position: fixed; height: 100%; width: 100%; top: 0px; left: 0px; text-align: center; z-index: 1050; display: none;
background-color: #000012F2; opacity: 0.75" id="imageoptionsbackground">&nbsp;</div>