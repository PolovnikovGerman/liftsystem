<div class="maincontent">
    <div class="maincontentmenuarea databasemenu">
        <div class="maincontentmenu">
            <?php foreach ($menu as $item) { ?>
                <div class="maincontentmenu_item" data-link="<?=str_replace('#','', $item['item_link'])?>"><?=$item['item_name']?></div>
            <?php } ?>
        </div>
    </div>
    <div class="maincontent_view">

        <?php if (isset($taskview)) { ?>
            <div class="artcontentarea" id="taskview" style="display: none;"><?=$taskview?></div>
        <?php } ?>
        <?php if (isset($orderlist)) { ?>
            <div class="artcontentarea" id="orderlist" style="display: none;"><?=$orderlist?></div>
        <?php } ?>
        <?php if (isset($requestlist)) { ?>
            <div class="artcontentarea" id="requestlist" style="display: none;"><?=$requestlist?></div>
        <?php } ?>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="artModalLabel">New message</h4>
            </div>
            <div class="modal-body" style="float: left;"></div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>