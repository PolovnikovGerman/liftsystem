<div class="maincontent">
    <div class="maincontentmenuarea marketmenu">
        <div class="maincontentmenu">
            <?php foreach ($menu as $item) { ?>
                <div class="maincontentmenu_item" data-link="<?=str_replace('#','', $item['item_link'])?>"><?=$item['item_name']?></div>
            <?php } ?>
        </div>
    </div>
    <div class="maincontent_view">
        <?php if (isset($leadsview)) { ?>
            <div class="leadscontentarea" id="leadsview" style="display: none;"><?=$leadsview?></div>
        <?php } ?>
        <?php if (isset($itemslistview)) { ?>
            <div class="leadscontentarea" id="itemslistview" style="display: none;"><?=$itemslistview?></div>
        <?php } ?>
        <?php if (isset($onlinequotesview)) { ?>
            <div class="leadscontentarea" id="onlinequotesview" style="display: none;"><?=$onlinequotesview?></div>
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