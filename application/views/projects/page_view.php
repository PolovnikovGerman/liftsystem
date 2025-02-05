<div class="maincontent">
    <div class="leftmenuarea">
        <?=$left_menu?>
    </div>
    <div class="maincontentmenuarea <?=$brand=='SB' ? 'stresballstab' : 'relieverstab'?>">
        <div class="maincontentmenu">
            <div class="title">Projects:</div>
            <?php foreach ($menu as $item) { ?>
                <div class="maincontentmenu_item <?=$start == str_replace('#','', $item['item_link']) ? 'active' : ''?> <?=ifset($item,'newver', 1)==0 ? 'oldver' :  ''?>" data-link="<?=str_replace('#','', $item['item_link'])?>">
                    <?php  if (ifset($item,'newver', 1)==0) { ?>
                        <div class="oldvesionlabel">&nbsp;</div>
                    <?php } ?>
                    <?=$item['item_name']?>
                </div>
            <?php } ?>
        </div>
        <div class="maincontent_view">
            <?php if (isset($projectsview)) { ?>
                <div class="projcontentarea" id="projectsview" style="display: none;"><?=$projectsview?></div>
            <?php } ?>
        </div>
    </div>
</div>
