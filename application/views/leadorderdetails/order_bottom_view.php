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
<!--        --><?php //else : ?>
<!--            <div class="emptybalancenotification">&nbsp;</div>-->
        <?php endif; ?>
<!--        <div class="icon_file" style="margin-top: 11px; margin-right: 11px;">&nbsp;</div>-->
        <div class="totalduedataviewarea">
            <?=$totaldueview?>
        </div>
    </div>
</div>
