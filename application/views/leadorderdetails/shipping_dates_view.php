<div class="label shipdate">Ship Date:</div>
<div class="datevalue"><?=$shipping['out_shipdate']?></div>
<div class="label arrivedate">Arrival Date:</div>
<div class="datevalue <?=$shipping['arriveclass']?>" id="arrivedateoutvalue"><?=$shipping['out_arrivedate']?></div>
<div class="label eventdate">Event Date:</div>
<div class="datevalue">
    <input type="text" class="input_border_gray eventdatevalue" <?=($edit==1 ? 'data-field="event_date"' : 'readonly="readonly"')?>  value="<?=$shipping['out_eventdate'] ?>"/>
</div>