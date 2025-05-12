<?=$menu_view?>
<div class="contentdata_view">
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
    <?php if (isset($searchesview)) { ?>
        <div class="marketingcontentarea" id="searchesview" style="display: none"><?=$searchesview?></div>
    <?php } ?>
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