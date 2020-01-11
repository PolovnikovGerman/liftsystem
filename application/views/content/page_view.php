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
