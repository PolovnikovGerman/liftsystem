<div class="multishippopuparea">
    <input type="hidden" id="shipsession" value="<?=$shipsession?>"/>
    <div class="multishiptitle">
        <div class="numaddress"><?=$numadderss?> Addresses</div>
        <div class="pagetitle">Multiple Shipping Address Details</div>
        <?php if ($manage==1) { ?>
        <div class="manageship <?=$edit==0 ? 'edit' : 'save'?>" style="display: none;">&nbsp;</div>
        <?php } ?>        
    </div>
    <div class="multishipsubtitle">
        <div class="eventdate">Event Date:</div>
        <div class="eventdatevalue">
            <input type="text" value="<?=$shipping['out_eventdate']?>" <?=$edit==0 ? 'readonly="readonly"' : 'data-field="event_date"'?> class="input_border_gray eventdatevalue"/>
        </div>        
        <div class="rushselectarea">Production Time - Ships on:</div>
        <div id="rushdatalistarea" class="rushdataselect"><?=$rushview?></div>            
        <div class="rushpricevalue">
            <?php if ($edit==0) { ?>
            <input type="text" class="shiprushcost input_border_black" value="<?=($shipping['rush_price']==0 ? '' : MoneyOutput($shipping['rush_price']))?>" readonly="readonly"/>                
            <?php } else { ?>
            <input type="text" class="shiprushcost input_border_black" value="<?=$shipping['rush_price']?>" data-field="rush_price"/>
            <?php } ?>            
        </div>
    </div>
    <div class="multishipadressarea"><?=$shipaddrview?></div>
    <div id="multishiptotals"><?=$totals_view?></div>
</div>