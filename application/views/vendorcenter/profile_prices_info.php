<div class="vendordetails-section-header">Pricing Info:</div>
<div class="vendordetails-section-body">
    <div class="content-row">
        <div class="vendorpricingcontact_value">
            <fieldset>
                <legend>Pricing Contact</legend>
                <div class="vendorparam_value">
                    <?=empty($vendor['pricing_contact']) ? '&nbsp;' : $vendor['pricing_contact']?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorpricingphone_value">
            <fieldset>
                <legend>Pricing Phone</legend>
                <div class="vendorparam_value">
                    <?=empty($vendor['pricing_phone']) ? '&nbsp;' : $vendor['pricing_phone']?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorpricingemail_value">
            <fieldset>
                <legend>Pricing Email</legend>
                <div class="vendorparam_value">
                    <?=empty($vendor['pricing_email']) ? '&nbsp;' : $vendor['pricing_email']?>
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
