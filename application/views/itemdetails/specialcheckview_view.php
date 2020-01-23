<div class="itemspecialcheckout">
    <div class="specialcheckout_title">Special Checkout <?=$item_name?></div>
    <div class="specialcheckout_data">
        Special Checkout <?=($special_checkout==0 ? 'Not Available' : ' Available')?>
    </div>
    <div class="specialcheckout_options" style="display:<?=($special_checkout==0 ? 'none' : 'block')?>">
        <div class="specialcheckout_specialprices">
            <div class="specialcheckout_shipping">
                <?=($special_shipping==1 ? 'Free Ship' : 'Ship non Free')?>
            </div>
            <div class="specialcheckout_setup">
                <?=($special_setup==0 ? 'Setup Free' : 'Setup non Free')?>
            </div>
        </div>
        <div class="specialcheckout_prices"><?=$prices?></div>
    </div>
</div>