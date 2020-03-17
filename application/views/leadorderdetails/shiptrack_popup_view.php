<div class="shiptrackpopuparea">
    <input type="hidden" id="tracksession" value="<?=$tracksession?>"/>
    <div class="title">
        <div class="data"><?=$order['order_num']?></div>
        <div class="label"><?=($order['order_confirmation']=='' ? 'historical' : $order['order_confirmation'])?></div>
        <div class="data"><?=$order['customer_name']?></div>
        <div class="statusarea">
            <div class="label">Status</div>
            <div class="status"><?=$status?></div>
        </div>
    </div>
    <div class="shiptrackdata">
        <div class="shiptrackdate">
            <div class="eventdate">Event Date:</div>
            <div class="datevalue"><?=(isset($shipping['out_eventdate']) ? $shipping['out_eventdate'] : '&nbsp;')?></div>
            <div class="shipsondate">Ships on:</div>
            <div class="shipsondatevalue">&nbsp;</div>
            <div class="shipsoncalendar">&nbsp;</div>
        </div>
        <div class="shiptrackdate">
            <div class="trackallbtn" <?=($showalltrack==0 ? 'style="display: none;"' : 'style="display: block;"')?>>&nbsp;</div>
        </div>
        <div class="shiptrackaddresarea">
            <?=$addres?>
        </div>
    </div>
    <div class="shiptracksendarea">&nbsp;</div>
    <div class="orderstatussave text_white">
        <div class="button_save_text">save</div>
    </div>
</div>