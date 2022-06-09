<div class="maincontent">
    <div class="maincontentmenuarea databasemenu">
        <div class="menupage_head">
            <?=$page_menu?>
        </div>
        <div class="dbcenter-content">
            <div class="maincontentmenu">
                <div class="title">Database:</div>
                <?php foreach ($menu as $item) { ?>
                    <div class="maincontentmenu_item <?=$start==str_replace('#','', $item['item_link']) ? 'active' : ''?> <?=ifset($item,'newver', 1)==0 ? 'oldver' :  ''?>" data-link="<?=str_replace('#','', $item['item_link'])?>">
                        <?php  if (ifset($item,'newver', 1)==0) { ?>
                            <div class="oldvesionlabel">&nbsp;</div>
                        <?php } ?>
                        <?=$item['item_name']?>
                    </div>
                <?php } ?>
            </div>
            <div class="maincontent_view">
                <?php if (isset($itemsview)) { ?>
                    <div class="dbcontentarea" id="itemsview" style="display: none;"><?=$itemsview?></div>
                <?php } ?>
                <?php if (isset($customersview)) { ?>
                    <div class="dbcontentarea" id="customersview" style="display: none;"><?=$customersview?></div>
                <?php } ?>
                <?php if (isset($pagesview)) { ?>
                    <div class="dbcontentarea" id="srpagesview" style="display: none;"><?=$pagesview?></div>
                <?php } ?>
                <?php if (isset($settingsview)) { ?>
                    <div class="dbcontentarea" id="settingsview" style="display: none;"><?=$settingsview?></div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="itemDetailsModal" tabindex="-1" role="dialog" aria-labelledby="itemDetailsModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><img src="/img/dbitems/close_item_popup.png"></span></button>
                <h4 class="modal-title" id="itemDetailsModalLabel">New message</h4>
            </div>
            <div class="modal-body" style="float: left;"></div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
