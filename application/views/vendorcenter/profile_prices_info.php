<div class="vendordetails-section-header">Pricing Info:</div>
<div class="vendordetails-section-body">
    <div class="content-row">
        <div class="vendorpricingcontact_value">
            <fieldset>
                <legend>Pricing Contact</legend>
                <div class="vendorparam_value">
                    <?php if ($editmode==0) { ?>
                        <?=empty($vendor['pricing_contact']) ? '&nbsp;' : $vendor['pricing_contact']?>
                    <?php } else { ?>
                        <input type="text" class="vendordetailsinpt" data-item="pricing_contact" value="<?=$vendor['pricing_contact']?>"/>
                    <?php } ?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorpricingphone_value">
            <fieldset>
                <legend>Pricing Phone</legend>
                <div class="vendorparam_value">
                    <?php if ($editmode==0) { ?>
                        <?=empty($vendor['pricing_phone']) ? '&nbsp;' : $vendor['pricing_phone']?>
                    <?php } else { ?>
                        <input type="text" class="vendordetailsinpt" data-item="pricing_phone" value="<?=$vendor['pricing_phone']?>"/>
                    <?php } ?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorpricingemail_value">
            <fieldset>
                <legend>Pricing Email</legend>
                <div class="vendorparam_value">
                    <?php if ($editmode==0) { ?>
                        <?=empty($vendor['pricing_email']) ? '&nbsp;' : $vendor['pricing_email']?>
                    <?php } else { ?>
                        <input type="text" class="vendordetailsinpt" data-item="pricing_email" value="<?=$vendor['pricing_email']?>"/>
                    <?php } ?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorpricingdocs_value">
            <fieldset>
                <legend>Pricing Document</legend>
                <?=$pricedocview?>
            </fieldset>
        </div>
    </div>
</div>
