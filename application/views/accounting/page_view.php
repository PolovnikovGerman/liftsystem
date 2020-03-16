<div class="maincontent">
    <div class="maincontentmenuarea marketmenu">
        <div class="maincontentmenu">
            <?php foreach ($menu as $item) { ?>
                <div class="maincontentmenu_item" data-link="<?=str_replace('#','', $item['item_link'])?>"><?=$item['item_name']?></div>
            <?php } ?>
        </div>
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