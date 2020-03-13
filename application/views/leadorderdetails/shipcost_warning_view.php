<div class="confirmshipcost_container">
    <input type="hidden" value="<?=$oldship?>" id="orderoldshipcostvalue" />
    <div class="title">
        Shipping Cost changed from <b><?=MoneyOutput($oldship,2)?></b> on <b><?=MoneyOutput($newship,2)?></b>
    </div>
    <!-- choice -->
    <div class="confirmshipcost_manage">
        <div class="leavenewshipcost">&nbsp;</div>
        <div class="restoreoldshipcost">&nbsp;</div>        
    </div>
</div>