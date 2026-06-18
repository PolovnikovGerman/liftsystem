<div class="marglef">
    <div id="leadorderprofitarea" style="float: left;">
        <?=$profit_view?>
    </div>
    <div class="orderamountdetailsarea">&nbsp;</div>
<!--    <div class="ticketdataviewarea">-->
<!--        --><?php //=$ticketview?>
<!--    </div>-->
    <div class="shippingdataviewarea">
        <?=$shippview?>
    </div>
    <div class="bottomfinancearea">
        <?php if ($balance > 0) : ?>
            <input type="text" style="display: none;" id="checkoutlink" value="<?=$checkoutlink?>"/>
            <div class="icon_link_checkout"><img src="/img/leadorder/link-hyperlink-icon.svg" alt="Checkout link"/></div>
            <div class="sendcheckoutnotification"><i class="fa fa-envelope-o"></i></div>
            <div class="sendcheckoutnotificationarea">
                <div class="sendcheckoutnotificationclosewin">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" version="1.1" style="shape-rendering:geometricPrecision;text-rendering:geometricPrecision;image-rendering:optimizeQuality;" viewBox="0 0 847 847" x="0px" y="0px" fill-rule="evenodd" clip-rule="evenodd"><g><path class="btn-closemodal-svg" d="M423 592l-196 196c-110,111 -279,-58 -169,-169l196 -196 -196 -196c-110,-110 59,-279 169,-169l196 196 196 -196c111,-110 280,59 169,169l-196 196 196 196c111,111 -58,280 -169,169l-196 -196z"></path></g></svg>
                </div>
                <div class="sendcheckoutnotificationcontent">&nbsp;</div>
            </div>
        <?php endif; ?>
        <div class="totalduedataviewarea">
            <?=$totaldueview?>
        </div>
    </div>
</div>
