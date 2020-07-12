<div class="maincontent">
    <div class="maincontentmenuarea databasemenu">
        <div class="maincontentmenu">
            <?php foreach ($menu as $item) { ?>
                <div class="maincontentmenu_item <?=$start==str_replace('#','', $item['item_link']) ? 'active' : ''?>" data-link="<?=str_replace('#','', $item['item_link'])?>"><?=$item['item_name']?></div>
            <?php } ?>
        </div>
    </div>
    <?php if (isset($shippingview)) { ?>
        <div class="settingcontentarea" id="shippingview" style="display: none;"><?=$shippingview?></div>
    <?php } ?>
    <?php if (isset($calendarsview)) { ?>
        <div class="settingcontentarea" id="calendarsview" style="display: none;"><?=$calendarsview?></div>
    <?php } ?>
    <?php if (isset($notificationsview)) { ?>
        <div class="settingcontentarea" id="notificationsview" style="display: none;"><?=$notificationsview?></div>
    <?php } ?>
    <?php if (isset($rushoptionsview)) { ?>
        <div class="settingcontentarea" id="rushoptionsview" style="display: none;"><?=$rushoptionsview?></div>
    <?php } ?>
    <?php if (isset($countriesview)) { ?>
        <div class="settingcontentarea" id="countriesview" style="display: none;"><?=$countriesview?></div>
    <?php } ?>
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