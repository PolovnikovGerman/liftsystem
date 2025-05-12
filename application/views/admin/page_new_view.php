<?=$menu_view?>
<div class="contentdata_view">
    <?php if (isset($usersview)) { ?>
        <div class="admincontentarea" id="usersview" style="display: none;"><?=$usersview?></div>
    <?php } ?>
    <?php if (isset($parseremailsview)) { ?>
        <div class="admincontentarea" id="parseremailsview" style="display: none;"><?=$parseremailsview?></div>
    <?php } ?>
    <?php if (isset($artalertsview)) { ?>
        <div class="admincontentarea" id="artalertsview" style="display: none;"><?=$artalertsview?></div>
    <?php } ?>
    <?php if (isset($calendarsview)) { ?>
        <div class="admincontentarea" id="calendarsview" style="display: none;"><?=$calendarsview?></div>
    <?php } ?>
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
