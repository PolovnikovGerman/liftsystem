<div class="confirmshipcost_container">
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
<!--        <div class="shipoptions" style="width: 100%; padding-left: 20px">-->
<!--            <div class="shipoption newship">-->
<!--                <i class="fa fa-check-circle-o" aria-hidden="true"></i>-->
<!--            </div>-->
<!--            <span>Change to new shipping <b>--><?//=MoneyOutput($newship,2)?><!--</b></span>-->
<!--        </div>-->
<!--        <div class="shipoptions">-->
<!--            <div class="shipoption oldship">-->
<!--                <i class="fa fa-circle-o" aria-hidden="true"></i>-->
<!--            </div>-->
<!--            <span>Leave then same <b>--><?//=MoneyOutput($oldship,2)?><!--</b></span>-->
<!--        </div>-->
        <div class="confirmshipcost_manage">
            <div class="leavenewshipcost">&nbsp;</div>
            <div class="restoreoldshipcost">&nbsp;</div>
        </div>
    <?php } ?>
    <!-- choice -->
</div>