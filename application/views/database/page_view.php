<div class="maincontent">
    <div class="maincontentmenuarea databasemenu">
        <div class="maincontentmenu">
            <div class="title">Database:</div>
            <?php foreach ($menu as $item) { ?>
                <div class="maincontentmenu_item <?=str_replace('#','', $item['item_link'])?>lnk <?=$start==str_replace('#','', $item['item_link']) ? 'active' : ''?>" data-link="<?=str_replace('#','', $item['item_link'])?>"><?=$item['item_name']?></div>
            <?php } ?>
        </div>
        <div class="maincontent_view">
            <?php if (isset($vendorsview)) { ?>
                <div class="dbcontentarea" id="vendorsview" style="display: none;"><?=$vendorsview?></div>
            <?php } ?>
            <?php if (isset($legacyview)) { ?>
                <div class="dbcontentarea" id="legacyview" style="display: none; background: #ededed;">
                    <?=$legacyview?>
                </div>
            <?php } ?>
            <div class="dbcontentarea" id="itemdetailsview" style="display: none;">
                <div class="left_maincontent"></div>
                <div class="right_maincontent"></div>
            </div>
        </div>
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
