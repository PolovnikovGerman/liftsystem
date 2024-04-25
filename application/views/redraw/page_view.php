<div class="maincontent">
    <div class="leftmenuarea">
        <?=$left_menu?>
    </div>
    <div class="maincontentmenuarea <?=$brand=='SB' ? 'stresballstab' : ($brand=='SR' ? 'relieverstab' : 'sigmasystem')?>" data-brand="<?=$brand?>">
        <div class="maincontentmenu">
            <div class="title">Redraw:</div>
            <?php foreach ($menu as $item) { ?>
                <div class="maincontentmenu_item <?=$start==str_replace('#','', $item['item_link']) ? 'active' : ''?> <?=ifset($item,'newver', 1)==0 ? 'oldver' :  ''?>" data-link="<?=str_replace('#','', $item['item_link'])?>">
                    <?php  if (ifset($item,'newver', 1)==0) { ?>
                        <div class="oldvesionlabel">&nbsp;</div>
                    <?php } ?>
                    <?=$item['item_name']?>
                </div>
            <?php } ?>
        </div>
        <div class="maincontent_view">
            <?php if (isset($redrawlistview)) { ?>
                <div class="redrawcontentarea" id="redrawlistview" style="display: none;"><?=$redrawlistview?></div>
            <?php } ?>
            <?php if (isset($completlistview)) { ?>
                <div class="redrawcontentarea completeddata" id="redrawcompletview" style="display: none;"><?=$completlistview?></div>
            <?php } ?>
        </div>
    </div>
</div>
<div class="modal fade" id="modalRedrawUpload" tabindex="-1" role="dialog" aria-labelledby="modalRedrawUploadLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalRedrawUploadLabel">Upload Vectorized Image</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="float: left;"></div>
            <!-- <div class="modal-footer"></div> -->
        </div>
    </div>
</div>
