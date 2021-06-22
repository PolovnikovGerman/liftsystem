<div class="vendordetails-section-header">Customer Service Info:</div>
<div class="vendordetails-section-body">
    <div class="content-row">
        <div class="vendorcustomercontact_value">
            <fieldset>
                <legend>Customer Service Contact</legend>
                <div class="vendorparam_value">
                    <?=empty($vendor['customer_contact']) ? '&nbsp;' : $vendor['customer_contact']?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorcustomerphone_value">
            <fieldset>
                <legend>Customer Service Phone</legend>
                <div class="vendorparam_value">
                    <?=empty($vendor['customer_phone']) ? '&nbsp;' : $vendor['customer_phone']?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorcustomeremail_value">
            <fieldset>
                <legend>Customer Service Email</legend>
                <div class="vendorparam_value">
                    <?=empty($vendor['customer_email']) ? '&nbsp;' : $vendor['customer_email']?>
                <div class="vendorparam_value">
            </fieldset>
        </div>
    </div>
</div>
