<div class="page_container">
    <div class="right_maincontent">
        <div class="maincontent">
            <div class="maincontentmenuarea databasemenu">
                <div class="maincontentmenu">
                    <div class="title">Content:</div>
                    <?php foreach ($menu as $item) { ?>
                        <div class="maincontentmenu_item <?=$start==str_replace('#','', $item['item_link']) ? 'active' : ''?> <?=ifset($item,'newver', 1)==0 ? 'oldver' :  ''?>" data-link="<?=str_replace('#','', $item['item_link'])?>"><?=$item['item_name']?></div>
                        <?php  if (ifset($item,'newver', 1)==0) { ?>
                            <div class="oldvesionlabel">&nbsp;</div>
                        <?php } ?>
                    <?php } ?>
                </div>
                <div class="maincontent_view">
                    <?php if (isset($btcontentview)) { ?>
                        <div class="sitecontentpagearea" id="btcontentview"><?=$btcontentview?></div>
                    <?php } ?>
                    <?php if (isset($sbcontentview)) { ?>
                        <div class="sitecontentpagearea" id="sbcontentview"><?=$sbcontentview?></div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
