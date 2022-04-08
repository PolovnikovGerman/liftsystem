<div class="maincontent">
    <div class="maincontentmenuarea marketmenu">
        <div class="menupage_head">
            <main class="container-fluid">
                <div class="mastermenu_head">
                    <div class="row">
                        <div class="col-12 col-sm-3 col-md-3 col-lg-2 col-xl-2">
                            <div class="menulabel">
                                Fulfillment
                            </div>
                        </div>
                        <div class="col-12 col-sm-9 col-md-9 col-lg-10 col-xl-10">
                            <div class="row">
                                <?php foreach ($menu as $mrow) { ?>
                                    <div class="headmenuitem <?=$start==str_replace('#', '',$mrow['item_link']) ? 'active' : ''?> <?=ifset($mrow,'newver', 1)==0 ? 'oldver' :  ''?>"
                                         data-lnk="<?=str_replace('#', '',$mrow['item_link'])?>">
                                        <?php  if (ifset($mrow,'newver', 1)==0) { ?>
                                            <div class="oldvesionlabel">&nbsp;</div>
                                        <?php } ?>
                                        <?=$mrow['item_name']?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div class="maincontent_view">
            <?php if (isset($purchaseordersview)) { ?>
                <div class="fulfillcontentarea" id="pototalsview" style="display: none;"><?=$purchaseordersview?></div>
            <?php } ?>
        </div>
    </div>
</div>

<div class="modal fade" id="pageModal" tabindex="-1" role="dialog" aria-labelledby="pageModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="pageModalLabel">New message</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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

