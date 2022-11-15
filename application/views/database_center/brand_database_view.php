<div class="maincontent">
    <div class="leftmenuarea">
        <?=$left_menu?>
    </div>
    <div class="maincontentmenuarea <?=$brand=='SB' ? 'stresballstab' : 'relieverstab'?>">
        <div class="maincontentmenu">
            <?php if ($master_menu==1) { ?>
                <div class="subtitlelink" data-link="dbcentermaster">Master Database:</div>
            <?php } else {?>
                <div class="subtitle">&nbsp;</div>
            <?php } ?>
            <div class="title">Brand Database:</div>
            <?php foreach ($menu as $item) { ?>
                <div class="maincontentmenu_item <?=str_replace('#','', $item['item_link'])?>lnk <?=$start==str_replace('#','', $item['item_link']) ? 'active' : ''?> <?=ifset($item,'newver', 1)==0 ? 'oldver' :  ''?>" data-link="<?=str_replace('#','', $item['item_link'])?>">
                    <?php  if (ifset($item,'newver', 1)==0) { ?>
                        <div class="oldvesionlabel">&nbsp;</div>
                    <?php } ?>
                    <?=$item['item_name']?>
                </div>
            <?php } ?>
        </div>
        <div class="maincontent_view">
            <?php if (isset($itemsview)) { ?>
                <div class="dbcontentarea" id="btitems" style="display: none;"><?=$itemsview?></div>
            <?php } ?>
            <?php if (isset($legacyview)) { ?>
                <div class="dbcontentarea" id="legacyview" style="display: none; background: #ededed;">
                    <?=$legacyview?>
                </div>
            <?php } ?>
            <?php if (isset($shippingview)) { ?>
                <div class="dbcontentarea" id="shippingview" style="display: none;">
                    <?=$shippingview?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="editModalLabel">New message</h4>
            </div>
            <div class="modal-body" style="float: left;"></div>
            <div class="modal-footer"></div>
        </div>
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