<input type="hidden" value="<?=$oldship?>" id="orderoldshipcostvalue" />
<input type="hidden" value="<?=$citychange?>" id="citychangevalid"/>
<input type="hidden" value="new" id="shiptypeselect"/>
<input type="hidden" value="<?=$costchange?>" id="warnshipchange"/>
<?php if ($citychange!=0) { ?>
    <div class="validatecitylabel">Specify location:</div>
    <div class="validatecitydata">
        <select class="form-group validcity">
            <?php foreach ($citylist as $list) { ?>
                <option value="<?=$list?>"><?=$list?></option>
            <?php } ?>
        </select>
    </div>
    <?php if ($costchange==0) { ?>
        <div class="savewarning">
            <img src="/img/leadorder/save_payment_btn.png"/>
        </div>
    <?php } ?>
<?php } ?>
<?php if ($costchange==1) { ?>
    <div class="title">Shipping Cost changed from <b><?=MoneyOutput($oldship,2)?></b> on <b><?=MoneyOutput($newship,2)?></b></div>
    <div class="confirmshipcost_manage">
        <div class="leavenewshipcost">&nbsp;</div>
        <div class="restoreoldshipcost">&nbsp;</div>
    </div>
<?php } ?>
<!-- choice -->
