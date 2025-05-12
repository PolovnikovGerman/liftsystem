<?=$menu_view?>
<div class="contentdata_view">
    <?php if (isset($leadsview)) { ?>
        <div class="leadscontentarea" id="leadsview" style="display: none;"><?=$leadsview?></div>
    <?php } ?>
    <?php if (isset($itemslistview)) { ?>
        <div class="leadscontentarea" id="itemslistview" style="display: none;"><?=$itemslistview?></div>
    <?php } ?>
    <?php if (isset($onlinequotesview)) { ?>
        <div class="leadscontentarea" id="onlinequotesview" style="display: none;"><?=$onlinequotesview?></div>
    <?php } ?>
    <?php if (isset($proofrequestsview)) { ?>
        <div class="leadscontentarea" id="proofrequestsview" style="display: none;"><?=$proofrequestsview?></div>
    <?php } ?>
    <?php if (isset($questionsview)) { ?>
        <div class="leadscontentarea" id="questionsview" style="display: none;"><?=$questionsview?></div>
    <?php } ?>
    <?php if (isset($customsbformview)) { ?>
        <div class="leadscontentarea" id="customsbformview" style="display: none;"><?=$customsbformview?></div>
    <?php } ?>
    <?php if (isset($checkoutattemptsview)) { ?>
        <div class="leadscontentarea" id="checkoutattemptsview" style="display: none;"><?=$checkoutattemptsview?></div>
    <?php } ?>
    <?php if (isset($leadquotesview)) { ?>
        <div class="leadscontentarea" id="leadquotesview" style="display: none;"><?=$leadquotesview?></div>
    <?php } ?>
    <?php if (isset($leadordersview)) { ?>
        <div class="leadscontentarea" id="customorders" style="display: none;"><?=$leadordersview?></div>
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
<!-- Leads edit Popup -->
<div class="modal fade" id="leadformModal" tabindex="-1" role="dialog" aria-labelledby="leadformModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header <?=$brand=='SB' ? 'stresballstab' : 'relieverstab'?>">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="leadformModalLabel">New message</h4>
            </div>
            <div class="modal-body <?=$brand=='SB' ? 'stresballstab' : 'relieverstab'?>" style="float: left;"></div>
            <div class="modal-footer <?=$brand=='SB' ? 'stresballstab' : 'relieverstab'?>"></div>
        </div>
    </div>
</div>
<!-- For Orders and Proof Requests -->
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
