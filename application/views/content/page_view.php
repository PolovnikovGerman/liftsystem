<div class="page_container">
    <div class="left_maincontent">
        <?=$left_menu?>
    </div>
    <div class="right_maincontent">
        <div class="maincontent">
            <div class="maincontentmenuarea databasemenu">
                <div class="maincontentmenu">
                    <div class="title">Content:</div>
                    <?php foreach ($menu as $item) { ?>
                        <div class="maincontentmenu_item <?=$start==str_replace('#','', $item['item_link']) ? 'active' : ''?>" data-link="<?=str_replace('#','', $item['item_link'])?>"><?=$item['item_name']?></div>
                    <?php } ?>
                </div>
                <div class="maincontent_view">
                    <input type="hidden" id="contentbrand" value="<?=$brand?>"/>
                    <?php foreach ($menu as $item) { ?>
                        <div class="contentpagearea" id="<?=str_replace('#','', $item['item_link'])?>">&nbsp;</div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
