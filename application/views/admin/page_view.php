<div class="maincontent">
    <div class="maincontentmenuarea adminmenu">
        <div class="maincontentmenu">
            <?php foreach ($menu as $item) { ?>
                <div class="maincontentmenu_item" data-link="<?=str_replace('#','', $item['item_link'])?>"><?=$item['item_name']?></div>
            <?php } ?>
        </div>
    </div>
    <div class="maincontent_view">
        <?php if (isset($usersview)) { ?>
            <div class="admincontentarea" id="usersview" style="display: none;"><?=$usersview?></div>
        <?php } ?>
        <?php if (isset($parseremailsview)) { ?>
            <div class="admincontentarea" id="parseremailsview" style="display: none;"><?=$parseremailsview?></div>
        <?php } ?>
        <?php if (isset($artalertsview)) { ?>
            <div class="admincontentarea" id="artalertsview" style="display: none;"><?=$artalertsview?></div>
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
