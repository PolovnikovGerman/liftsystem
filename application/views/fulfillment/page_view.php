<div class="maincontent">
    <div class="leftmenuarea">
        <?=$left_menu?>
    </div>
    <div class="maincontentmenuarea <?=$brand=='SB' ? 'stresballstab' : ($brand=='SR' ? 'relieverstab' : 'sigmasystem')?>">
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
            <?php if(isset($printschedulerview)) { ?>
                <div class="fulfillcontentarea" id="printschedulerview" style="display: none;"><?=$printschedulerview?></div>
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

<div class="modal fade" id="modalManage" tabindex="-1" role="dialog" aria-labelledby="modalManageLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalManageLabel">PO Payment Methods</h4>
            </div>
            <div class="modal-body" style="float: left;"></div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditpurchase" tabindex="-1" role="dialog" aria-labelledby="modalEditpurchaseLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalEditpurchaseLabel">Enter PO Value</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="float: left;"></div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditInventPrice" tabindex="-1" role="dialog" aria-labelledby="modalEditInventPriceLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalEditInventPriceLabel"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="float: left;"></div>
            <!-- <div class="modal-footer"></div> -->
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditInventHistory" tabindex="-1" role="dialog" aria-labelledby="modalEditInventHistoryLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalEditInventHistoryLabel"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="float: left;"></div>
            <!-- <div class="modal-footer"></div> -->
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditInventItem" tabindex="-1" role="dialog" aria-labelledby="modalEditInventItemLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalEditInventItemLabel"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="float: left;"></div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditInventColor" tabindex="-1" role="dialog" aria-labelledby="modalEditInventColorLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalEditInventColorLabel"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="float: left;"></div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
