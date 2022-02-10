<div class="label shipdate">Ship Date:</div>
<div class="datevalue">
    <?php if ($user_role=='masteradmin') { ?>
        <input type="text" class="input_border_gray rushpastvalue" readonly id="rushpast" value="<?=empty($shipping['shipdate']) ? '' : date('m/d/Y', $shipping['shipdate']) ?>"/>
    <?php } else { ?>
        <?=$shipping['out_shipdate']?>
    <?php } ?>
</div>
<div class="label arrivedate">Arrival Date:</div>
<div class="datevalue <?=$user_role=='masteradmin' ? '' : $shipping['arriveclass']?>" id="arrivedateoutvalue">
    <?php if ($user_role=='masteradmin') { ?>
        <input type="text" class="input_border_gray rushpastvalue <?=$shipping['arriveclass']?>" readonly id="arrivedatepast" value="<?=empty($shipping['arrive_date']) ? '' : date('m/d/Y', $shipping['arrive_date']) ?>"/>
    <?php } else { ?>
        <?=$shipping['out_arrivedate']?>
    <?php } ?>
</div>
<div class="label eventdate">Event Date:</div>
<div class="datevalue">
    <input type="text" class="input_border_gray eventdatevalue" data-field="event_date"  value="<?=empty($shipping['event_date']) ? '' : date('m/d/Y',$shipping['event_date']) ?>"/>
</div>