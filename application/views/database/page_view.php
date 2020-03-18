<div class="maincontent">
    <div class="maincontentmenuarea databasemenu">
        <div class="maincontentmenu">
            <?php foreach ($menu as $item) { ?>
                <div class="maincontentmenu_item" data-link="<?=str_replace('#','', $item['item_link'])?>"><?=$item['item_name']?></div>
            <?php } ?>
        </div>
    </div>
    <?php if (isset($itempriceview)) { ?>
        <div class="dbcontentarea" id="itempriceview" style="display: none;"><?=$itempriceview?></div>
    <?php } ?>
    <?php if (isset($itemcategoryview)) { ?>
        <div class="dbcontentarea" id="itemcategoryview" style="display: none;"><?=$itemcategoryview?></div>
    <?php } ?>
    <?php if (isset($itemsequenceview)) { ?>
        <div class="dbcontentarea" id="itemsequenceview" style="display: none;"><?=$itemsequenceview?></div>
    <?php } ?>
    <?php if (isset($itemmisinfoview)) { ?>
        <div class="dbcontentarea" id="itemmisinfoview" style="display: none;"><?=$itemmisinfoview?></div>
    <?php } ?>
    <?php if (isset($itemprofitview)) { ?>
        <div class="dbcontentarea" id="itemprofitview" style="display: none;"><?=$itemprofitview?></div>
    <?php } ?>
    <?php if (isset($itemtemplateview)) { ?>
        <div class="dbcontentarea" id="itemtemplateview" style="display: none;"><?=$itemtemplateview?></div>
    <?php } ?>
    <?php if (isset($categoryview)) { ?>
        <div class="dbcontentarea" id="categoryview" style="display: none;"><?=$categoryview?></div>
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