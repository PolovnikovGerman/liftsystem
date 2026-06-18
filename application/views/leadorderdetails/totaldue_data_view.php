<div class="block_8 <?=$class=='closed' ? 'text_black' : 'text_white'?>">
    <?php if ($class=='closed') { ?>
        <div class="block_9">
            <div class="block_9_text">
                <div class="block_8bottom_text1">Total Due:</div>
                <div class="block_8_text2 text_bold">PAID</div>
            </div>
        </div>
    <?php } else { ?>
        <?php if ($totaldue > 0) : ?>
            <div class="blockduedataarea">
                <input type="text" style="display: none;" id="checkoutlink" value="<?=$checkoutlink?>"/>
                <div class="icon_link_checkout"><img src="/img/leadorder/link-hyperlink-icon-white.svg" alt="Checkout link"/></div>
                <div class="sendcheckoutnotification"><i class="fa fa-envelope-o"></i></div>
                <div class="sendcheckoutnotificationarea">
                    <div class="sendcheckoutnotificationclosewin">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" version="1.1" style="shape-rendering:geometricPrecision;text-rendering:geometricPrecision;image-rendering:optimizeQuality;" viewBox="0 0 847 847" x="0px" y="0px" fill-rule="evenodd" clip-rule="evenodd"><g><path class="btn-closemodal-svg" d="M423 592l-196 196c-110,111 -279,-58 -169,-169l196 -196 -196 -196c-110,-110 59,-279 169,-169l196 196 196 -196c111,-110 280,59 169,169l-196 196 196 196c111,111 -58,280 -169,169l-196 -196z"></path></g></svg>
                    </div>
                    <div class="sendcheckoutnotificationcontent">&nbsp;</div>
                </div>
        <?php else : ?>
            <div class="blockduenegative block_8_text">
        <?php endif; ?>
            <div class="block_8bottom_text1">Total Due:</div>
            <div class="block_8_text2 text_bold <?=($class=='open' ? 'text_white' : 'text_lightred blinked')?>"><?=($totaldue<0 ? '(' : '').MoneyOutput(abs($totaldue)).($totaldue<0 ? ')' : '')?></div>
        </div>
<!--        <div class="block_8_backgr3">&nbsp;</div>                       -->
    <?php } ?>    
</div>