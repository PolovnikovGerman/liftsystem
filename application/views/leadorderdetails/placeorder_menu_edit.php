<div class="leadorderdetailspopup">
    <input type="hidden" id="orderdataid" value="<?=$order_id?>"/>
    <input type="hidden" id="ordersession" value="<?=$leadsession?>"/>
    <input type="hidden" id="callpage" value="<?=$current_page?>"/>
    <input type="hidden" id="ordermapuse" value="<?=$mapuse?>"/>
    <?php if (isset($item_error)) : ?>
        <input type="hidden" id="duplerroritem" value="<?=$item_error?>"/>
        <input type="hidden" id="duplerroritemmsg" value="<?=$item_error_msg?>"/>
    <?php endif; ?>
    <div id="currentorderdataarea">
        <?=$order_data?>
    </div>
</div>
