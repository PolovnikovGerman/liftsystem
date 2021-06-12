<div class="maincontent">
    <div class="maincontentmenuarea marketmenu">
        <div class="maincontentmenu">
            <div class="title">Fulfillment:</div>
            <?php foreach ($menu as $item) { ?>
                <div class="maincontentmenu_item <?=$start==str_replace('#','', $item['item_link']) ? 'active' : ''?> <?=ifset($item,'newver', 1)==0 ? 'oldver' :  ''?> " data-link="<?=str_replace('#','', $item['item_link'])?>">
                    <?php  if (ifset($item,'newver', 1)==0) { ?>
                        <div class="oldvesionlabel">&nbsp;</div>
                    <?php } ?>
                    <?=$item['item_name']?>
                </div>
            <?php } ?>
        </div>
        <div class="maincontent_view">
            <?php if (isset($vendorsview)) { ?>
                <div class="fulfillcontentarea" id="vendorsview" style="display: none;"><?=$vendorsview?></div>
            <?php } ?>
            <?php if (isset($fullfilstatusview)) { ?>
                <div class="fulfillcontentarea" id="fullfilstatusview" style="display: none;"><?=$fullfilstatusview?></div>
            <?php } ?>
            <?php if (isset($pototalsview)) { ?>
                <div class="fulfillcontentarea" id="pototalsview" style="display: none;"><?=$pototalsview?></div>
            <?php } ?>
            <?php if (isset($printshopinventview)) { ?>
                <div class="fulfillcontentarea" id="printshopinventview" style="display: none;"><?=$printshopinventview?></div>
            <?php } ?>
            <?php if (isset($invneedlistview)) { ?>
                <div class="fulfillcontentarea" id="invneedlistview" style="display: none;"><?=$invneedlistview?></div>
            <?php } ?>
            <?php if (isset($salesrepinventview)) { ?>
                <div class="fulfillcontentarea" id="salesrepinventview" style="display: none;"><?=$salesrepinventview?></div>
            <?php } ?>
            <?php if (isset($printshopreportview)) { ?>
                <div class="fulfillcontentarea" id="printshopreportview" style="display: none;"><?=$printshopreportview?></div>
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