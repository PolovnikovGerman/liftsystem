<div class="maincontent">
    <div class="maincontentmenuarea databasemenu">
        <div class="maincontentmenu">
            <?php foreach ($menu as $item) { ?>
                <div class="maincontentmenu_item" data-link="<?=str_replace('#','', $item['item_link'])?>"><?=$item['item_name']?></div>
            <?php } ?>
        </div>
    </div>
    <?php if (isset($shippingview)) { ?>
        <div class="settingcontentarea" id="shippingview" style="display: none;"><?=$shippingview?></div>
    <?php } ?>
</div>

<div class="modal fade" id="pageModal" tabindex="-1" role="dialog" aria-labelledby="pageModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="artModalLabel">New message</h4>
            </div>
            <div class="modal-body" style="float: left;"></div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>