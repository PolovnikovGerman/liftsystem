<div class="maincontent">
    <div class="maincontentmenuarea databasemenu">
        <div class="menupage_head">
            <?=$page_menu?>
        </div>
        <div class="maincontent_view">
            <?php if (isset($vendorsview)) { ?>
                <div class="dbcontentarea" id="vendorsview" style="display: none;"><?=$vendorsview?></div>
            <?php } ?>
            <?php if (isset($settingsview)) { ?>
                <div class="dbcontentarea" id="settingsview" style="display: none;"><?=$settingsview?></div>
            <?php } ?>
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
            <div class="mobileviewonly">
                <div class="container-fluid pr-0 pl-0">
                    <div class="modal-container">
                        <div class="modalvendor-title">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true"><img src="/img/vendors/close_popup.png"></span>
                            </button>
                            <div id="mobileheadercontent">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="desktopviewonly">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><img src="/img/vendors/close_popup.png"></span>
                    </button>
                    <h4 class="modal-title" id="vendorDetailsModalLabel">New message</h4>
                </div>
            </div>
            <div class="modal-body" style="float: left;"></div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
