<div class="page_container">
    <div class="left_maincontent">
        <div class="left_tab active" data-brand="all"><img src="/img/page_view/universal_lefttab_logo.png"/></div>
        <div class="left_tab"  data-brand="SB"><img src="/img/page_view/sb_lefttab_logo.png"/></div>
        <div class="left_tab"  data-brand="BT"><img src="/img/page_view/bt_lefttab_logo.png"/></div>
    </div>
    <div class="right_maincontent">
        <div class="maincontent">
            <div class="maincontentmenuarea databasemenu">
                <div class="maincontentmenu">
                    <?php foreach ($menu as $item) { ?>
                        <div class="maincontentmenu_item" data-link="<?=str_replace('#','', $item['item_link'])?>"><?=$item['item_name']?></div>
                    <?php } ?>
                </div>
            </div>
            <div class="maincontent_view">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="pageModal" tabindex="-1" role="dialog" aria-labelledby="pageModalLabel">
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