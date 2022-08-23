<div class="maincontent">
    <div class="maincontentmenuarea databasemenu">
        <div class="menupage_head">
            <?=$page_menu?>
        </div>
        <div class="maincontentmenu">
            <div class="title">Database:</div>
            <?php foreach ($menu as $item) { ?>
                <div class="maincontentmenu_item <?=str_replace('#','', $item['item_link'])?>lnk <?=$start==str_replace('#','', $item['item_link']) ? 'active' : ''?> <?=ifset($item,'newver', 1)==0 ? 'oldver' :  ''?>" data-link="<?=str_replace('#','', $item['item_link'])?>">
                    <?php  if (ifset($item,'newver', 1)==0) { ?>
                        <div class="oldvesionlabel">&nbsp;</div>
                    <?php } ?>
                    <?=$item['item_name']?>
                </div>
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
            <?php if (isset($shippingview)) { ?>
                <div class="dbcontentarea" id="shippingview" style="display: none;">
                    <?=$shippingview?>
                </div>
            <?php } ?>
            <?php if (isset($itempriceview)) { ?>
                <div class="page_container dbitemspage" id="itempriceview" style="display: none;"><?=$itempriceview?></div>
            <?php } ?>
            <?php if (isset($itemcategoryview)) { ?>
                <div class="page_container dbitemspage" id="itemcategoryview" style="display: none;"><?=$itemcategoryview?></div>
            <?php } ?>
            <?php if (isset($itemsequenceview)) { ?>
                <div class="page_container dbitemspage" id="itemsequenceview" style="display: none;"><?=$itemsequenceview?></div>
            <?php } ?>
            <?php if (isset($itemmisinfoview)) { ?>
                <div class="page_container dbitemspage" id="itemmisinfoview" style="display: none;"><?=$itemmisinfoview?></div>
            <?php } ?>
            <?php if (isset($itemprofitview)) { ?>
                <div class="page_container dbitemspage" id="itemprofitview" style="display: none;"><?=$itemprofitview?></div>
            <?php } ?>
            <?php if (isset($itemtemplateview)) { ?>
                <div class="page_container dbitemspage" id="itemtemplateview" style="display: none;"><?=$itemtemplateview?></div>
            <?php } ?>
            <?php if (isset($categoryview)) { ?>
                <div class="page_container dbitemspage" id="categoryview" style="display: none;"><?=$categoryview?></div>
            <?php } ?>
            <?php if (isset($itemexportview)) { ?>
                <div class="page_container dbitemspage" id="itemexportview" style="display: none;"><?=$itemexportview?></div>
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