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
                <?php foreach ($menu as $item) { ?>
                    <div class="contentpagearea" id="<?=str_replace('#','', $item['item_link'])?>">&nbsp;</div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
