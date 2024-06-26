<div class="maincontent">
    <div class="leftmenuarea">
        <?=$left_menu?>
    </div>
    <div class="maincontentmenuarea <?=$brand=='SB' ? 'stresballstab' : ($brand=='SR' ? 'relieverstab' : 'sigmasystem')?>">
        <div class="maincontentmenu">
            <div class="title">Analytics:</div>
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
            <?php if (isset($reportsalestypeview)) { ?>
                <div class="analyticcontentarea" id="reportsalestypeview" style="display: none;"><?=$reportsalestypeview?></div>
            <?php } ?>
            <?php if (isset($reportitemsoldyearview)) { ?>
                <div class="analyticcontentarea" id="reportitemsoldyearview" style="display: none;"><?=$reportitemsoldyearview?></div>
            <?php } ?>
            <?php if (isset($reportitemsoldmonthview)) { ?>
                <div class="analyticcontentarea" id="reportitemsoldmonthview" style="display: none;"><?=$reportitemsoldmonthview?></div>
            <?php } ?>
            <?php if (isset($checkoutreportview)) { ?>
                <div class="analyticcontentarea" id="checkoutreportview" style="display: none;"><?=$checkoutreportview?></div>
            <?php } ?>
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