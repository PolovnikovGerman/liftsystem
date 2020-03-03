<div class="maincontent">
    <div class="maincontentmenuarea marketmenu">
        <div class="maincontentmenu">
            <?php foreach ($menu as $item) { ?>
                <div class="maincontentmenu_item" data-link="<?=str_replace('#','', $item['item_link'])?>"><?=$item['item_name']?></div>
            <?php } ?>
        </div>
    </div>
    <div class="maincontent_view">
        <?php if (isset($vendorsview)) { ?>
            <div class="fulfillcontentarea" id="vendorsview" style="display: none;"><?=$vendorsview?></div>
        <?php } ?>
        <?php if (isset($fullfilstatusview)) { ?>
            <div class="fulfillcontentarea" id="fullfilstatusview" style="display: none;"><?=$fullfilstatusview?></div>
        <?php } ?>
        <?php if (isset($pototalsview)) { ?>
            <div class="fulfillcontentarea" id="pototalsview" style="display: none;"><?=$pototalsview?></div>
        <?php } ?>
        <?php if (isset($printshopinventview)) { ?>
            <div class="fulfillcontentarea" id="printshopinventview" style="display: none;"><?=$printshopinventview?></div>
        <?php } ?>
        <?php if (isset($invneedlistview)) { ?>
            <div class="fulfillcontentarea" id="invneedlistview" style="display: none;"><?=$invneedlistview?></div>
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