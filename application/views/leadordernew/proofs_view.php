<?php $proofhead = $proofs['head'];?>
<div class="artproofs_header">
    <div class="artproofheader_title">Proofs:</div>
    <div class="artproofheader_apprvl <?=$proofhead['class']?>">
        <?php if (empty($proofhead['apprtime'])) : ?>
            <span><?=$proofhead['status']?></span>
        <?php else : ?>
            <span class="artproofapprvl_icon"><img src="/img/leadorder/tick-black.svg"></span>
            <span><?=$proofhead['status']?></span>
            <div class="artproofapprvl_days"><?=$proofhead['apprtime']?></div>
        <?php endif; ?>
    </div>
    <div class="artproofheader_open"><span>Open</span><span><img src="/img/leadorder/icon-link.svg"></span></div>
    <div class="artproofheader_send <?=$edit==0 ? '' : 'active'?>">Send<span><i class="fa fa-envelope-o" aria-hidden="true"></i></span></div>
</div>
<div class="artproofs_body"><?=$prooflist?></div>
<div class="proofdocsuploads">
    <div class="artpopupclosewin" id="proofdocsclosewin">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" version="1.1" style="shape-rendering:geometricPrecision;text-rendering:geometricPrecision;image-rendering:optimizeQuality;" viewBox="0 0 847 847" x="0px" y="0px" fill-rule="evenodd" clip-rule="evenodd"><g><path class="btn-closemodal-svg" d="M423 592l-196 196c-110,111 -279,-58 -169,-169l196 -196 -196 -196c-110,-110 59,-279 169,-169l196 196 196 -196c111,-110 280,59 169,169l-196 196 196 196c111,111 -58,280 -169,169l-196 -196z"></path></g></svg>
    </div>
    <div class="proofdocsupload_container">&nbsp;</div>
</div>