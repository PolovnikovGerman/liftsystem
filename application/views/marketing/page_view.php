<div class="maincontent">
    <div class="maincontentmenuarea marketmenu">
        <div class="maincontentmenu">
            <div class="title">Marketing:</div>
            <?php foreach ($menu as $item) { ?>
                <div class="maincontentmenu_item <?=$start==str_replace('#', '', $item['item_link']) ? 'active' : ''?> <?=ifset($item,'newver', 1)==0 ? 'oldver' :  ''?>" data-link="<?= str_replace('#', '', $item['item_link']) ?>">
                    <?php  if (ifset($item,'newver', 1)==0) { ?>
                        <div class="oldvesionlabel">&nbsp;</div>
                    <?php } ?>
                    <?= $item['item_name'] ?>
                </div>
            <?php } ?>
        </div>
        <div class="maincontent_view">
            <?php if (isset($searchestimeview)) { ?>
                <div class="marketingcontentarea" id="searchestimeview" style="display: none;"><?= $searchestimeview ?></div>
            <?php } ?>
            <?php if (isset($searcheswordview)) { ?>
                <div class="marketingcontentarea" id="searcheswordview" style="display: none"><?=$searcheswordview?></div>
            <?php } ?>
            <?php if (isset($searchesipadrview)) { ?>
                <div class="marketingcontentarea" id="searchesipadrview" style="display: none"><?=$searchesipadrview?></div>
            <?php } ?>
            <?php if (isset($signupview)) { ?>
                <div class="marketingcontentarea" id="signupview" style="display: none"><?=$signupview?></div>
            <?php } ?>
            <?php if (isset($couponsview)) { ?>
                <div class="marketingcontentarea" id="couponsview" style="display: none"><?=$couponsview?></div>
            <?php } ?>
        </div>
    </div>
</div>

<div class="modal fade" id="pageModal" tabindex="-1" role="dialog" aria-labelledby="pageModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="pageModalLabel">New message</h4>
            </div>
            <div class="modal-body" style="float: left;"></div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>