<div class="leadorderdetailspopup">
    <input type="hidden" id="orderdataid" value="<?=$order_id?>"/>
    <input type="hidden" id="ordersession" value="<?=$leadsession?>"/>
    <input type="hidden" id="callpage" value="<?=$current_page?>"/>
    <?php if (isset($locrecid)) {?>
    <input type="hidden" id="locrecid" value="<?=$locrecid?>"/>
    <?php } ?>
    <input type="hidden" id="loctimeout" value="<?=$timeout?>"/>
    <div class="timeroutarea"></div>
    </div>
    <div id="currentorderdataarea">
        <?=$order_data?>
    </div>    
</div>
