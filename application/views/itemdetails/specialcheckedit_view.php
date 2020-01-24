<input type="hidden" id="specialsession" value="<?=$session_id?>"/>
<div class="itemspecialcheckout">
    <div class="specialcheckout_title">Special Checkout Options</div>
    <div class="specialcheckout_title">Item <?= $item_name ?></div>
    <div class="specialcheckout_data">
        <div class="specialcheckout_select_label">Special Checkout</div>
        <select class="specialcheckout_selecttype" data-fld="special_checkout">
            <option value="0" <?= ($special_checkout == 0 ? 'selected="selected"' : '') ?> >Not Available</option>
            <option value="1" <?= ($special_checkout == 1 ? 'selected="selected"' : '') ?> >Available</option>
        </select>
        <div class="savespecialcheckout">
            <img src="/img/itemdetails/save_order.png" alt="Save Special Checkout"/>
        </div>

    </div>
    <div class="specialcheckout_options" style="display:<?= ($special_checkout == 0 ? 'none' : 'block') ?>">
        <div class="specialcheckout_specialprices">
            <div class="specialcheckout_shipping">
                Free Shipping
                <input type="checkbox" value="1" <?= ($special_shipping == 1 ? 'checked="checked"' : '') ?> class="specialcheckout_checkbox"
                                     data-fld="special_shipping"/>
            </div>
            <div class="specialcheckout_setup">
                Setup Price
                <input type="hidden" id="old_special_setup" value="<?= $special_setup ?>"/>
                <input type="text" class="specialsetupinpt" id="special_setup" name="special_setup"
                       value="<?= $special_setup ?>"/>
            </div>
        </div>
        <div class="specialcheckout_prices"><?= $prices ?></div>
    </div>
</div>
