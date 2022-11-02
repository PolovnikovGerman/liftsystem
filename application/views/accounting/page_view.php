<div class="maincontent">
    <div class="leftmenuarea">
        <?=$left_menu?>
    </div>
    <div class="maincontentmenuarea <?=$brand=='SB' ? 'stresballstab' : 'relieverstab'?>">
        <div class="maincontentmenu">
            <div class="title">Accounting:</div>
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
            <?php if (isset($profitordesview)) { ?>
                <div class="accountcontentarea" id="profitordesview" style="display: none;"><?=$profitordesview?></div>
            <?php } ?>
            <?php if (isset($profitdatesview)) { ?>
                <div class="accountcontentarea" id="profitdatesview" style="display: none;"><?=$profitdatesview?></div>
            <?php } ?>
            <?php if (isset($purchaseordersview)) { ?>
                <div class="accountcontentarea" id="purchaseordersview" style="display: none;"><?=$purchaseordersview?></div>
            <?php } ?>
            <?php if (isset($openinvoicesview)) { ?>
                <div class="accountcontentarea" id="openinvoicesview" style="display: none;"><?=$openinvoicesview?></div>
            <?php } ?>
            <?php if (isset($financebatchesview)) { ?>
                <div class="accountcontentarea" id="financebatchesview" style="display: none;"><?=$financebatchesview?></div>
            <?php } ?>
            <?php if (isset($netprofitview)) { ?>
                <div class="accountcontentarea" id="netprofitview" style="display: none;"><?=$netprofitview?></div>
            <?php } ?>
            <?php if (isset($ownertaxesview)) { ?>
                <div class="accountcontentarea" id="ownertaxesview" style="display: none;"><?=$ownertaxesview?></div>
            <?php } ?>
            <?php if (isset($expensesview)) { ?>
                <div class="accountcontentarea" id="expensesview" style="display: none;"><?=$expensesview?></div>
            <?php } ?>
            <?php if (isset($accreceivview)) { ?>
                <div class="accountcontentarea" id="accreceivview" style="display: none;"><?=$accreceivview?></div>
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

<div class="modal fade" id="artModal" tabindex="-1" role="dialog" aria-labelledby="artModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="artModalLabel">New message</h4>
            </div>
            <div class="modal-body" style="float: left;"></div>
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
