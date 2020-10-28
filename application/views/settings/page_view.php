<div class="maincontent">
    <div class="maincontentmenuarea databasemenu">
        <div class="maincontentmenu">
            <div class="title">Settings:</div>
            <?php foreach ($menu as $item) { ?>
                <div class="maincontentmenu_item <?=$start==str_replace('#','', $item['item_link']) ? 'active' : ''?>" data-link="<?=str_replace('#','', $item['item_link'])?>"><?=$item['item_name']?></div>
            <?php } ?>
        </div>
        <?php if (isset($calendarsview)) { ?>
            <div class="settingcontentarea" id="calendarsview" style="display: none;"><?=$calendarsview?></div>
        <?php } ?>
        <?php if (isset($countriesview)) { ?>
            <div class="settingcontentarea" id="countriesview" style="display: none;"><?=$countriesview?></div>
        <?php } ?>
        <?php if (isset($btsettingsview)) { ?>
            <div class="settingcontentarea" id="btsettingsview" style="display: none;"><?=$btsettingsview?></div>
        <?php } ?>
        <?php if (isset($sbsettingsview)) { ?>
            <div class="settingcontentarea" id="sbsettingsview" style="display: none;"><?=$sbsettingsview?></div>
        <?php } ?>
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