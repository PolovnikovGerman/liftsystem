<div class="maincontent">
    <div class="maincontentmenuarea <?=$brand=='SB' ? 'stresballstab' : 'relieverstab'?>">
<!--        <div class="maincontentmenu">-->
<!--            <div class="title">Orders:</div>-->
<!--            --><?php //foreach ($menu as $item) { ?>
<!--                <div class="maincontentmenu_item --><?php //=$start==str_replace('#','', $item['item_link']) ? 'active' : ''?><!-- --><?php //=ifset($item,'newver', 1)==0 ? 'oldver' :  ''?><!--" data-link="--><?php //=str_replace('#','', $item['item_link'])?><!--">-->
<!--                    --><?php // if (ifset($item,'newver', 1)==0) { ?>
<!--                        <div class="oldvesionlabel">&nbsp;</div>-->
<!--                    --><?php //} ?>
<!--                    --><?php //=$item['item_name']?>
<!--                </div>-->
<!--            --><?php //} ?>
<!--        </div>-->
        <div class="maincontent_view">&nbsp;</div>
    </div>
</div>
<div class="modal fade" id="artModal" tabindex="-1" role="dialog" aria-labelledby="artModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="artModalLabel"><?=$header?></h4>
            </div>
            <div class="modal-body" style="float: left;">
                <?=$content?>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="artNextModal" tabindex="-1" role="dialog" aria-labelledby="artNextModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="artNextModalLabel">New message</h4>
            </div>
            <div class="modal-body" style="float: left;"></div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
<!-- loader -->
<div style="position: fixed; height: 100%; width: 100%; top: 0px; left: 0px; background: url(/img/page_view/overlay.png); text-align: center; z-index: 1100; display: none;" id="loader">
    <div style="width:100%;z-index: 15;" id="loaderimg">
        <div style="float: none; width:100%;z-index: 100;margin-top: 356px;">
            <img src="/img/page_view/loader.gif">
            <div class="clear"></div>
            <div style="color: #FFFFFF; font-size: 18px; font-weight: bold; padding: 14px 0 0 23px; text-align: center; text-shadow: 0 2px 2px #000000, 0 2px 2px #FFFFFF; vertical-align: middle;">
                Loading...
            </div>
        </div>
    </div>
</div>
