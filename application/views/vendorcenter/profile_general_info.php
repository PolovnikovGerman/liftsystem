<div class="vendordetails-section-header">General Info:</div>
<div class="vendordetails-section-body">
    <div class="content-row">
        <div class="vendorslug_value">
            <fieldset>
                <legend>Vendor #</legend>
                <div class="vendorparam_value">
                    <?=$vendor['vendor_slug']?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorname_value">
            <fieldset>
                <legend>Vendor Name</legend>
                <div class="vendorparam_value"><?=$vendor['vendor_name']?></div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendoraltname_value">
            <fieldset>
                <legend>Alternate Name</legend>
                <div class="vendorparam_value">
                    <?=empty($vendor['alt_name']) ? '&nbsp;' : $vendor['alt_name']?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendortype_value">
            <fieldset>
                <legend>Type</legend>
                <div class="vendorparam_value"><?=$vendor['vendor_type']?></div>
            </fieldset>
        </div>
        <div class="vendorcountry_value">
            <fieldset>
                <legend>Country</legend>
                <div class="vendorparam_value"><?=$vendor['country_name']?></div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorasi_value">
            <fieldset>
                <legend>ASI #</legend>
                <div class="vendorparam_value">
                    <?=empty($vendor['vendor_asinumber']) ? '&nbsp;' : $vendor['vendor_asinumber']?>
                </div>
            </fieldset>
        </div>
        <div class="vedorouraccount_value">
            <fieldset>
                <legend>Our Account #</legend>
                <div class="vendorparam_value">
                    <?=empty($vendor['our_account_number']) ? '&nbsp;' : $vendor['our_account_number']?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorweb_value">
            <fieldset>
                <legend>Website</legend>
                <div class="vendorparam_value">
                    <?=empty($vendor['vendor_website']) ? '&nbsp;': $vendor['vendor_website']?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorphone_value">
            <fieldset>
                <legend>Main Phone</legend>
                <div class="vendorparam_value">
                    <?=empty($vendor['vendor_phone']) ? '&nbsp;' : $vendor['vendor_phone']?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendoraddress_value">
            <fieldset>
                <legend>Address</legend>
                <div class="vendorparam_value">
                    <?=empty($vendor['address']) ? '&nbsp;' : nl2br($vendor['address'])?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorinnernote_value">
            <fieldset>
                <legend>Notes (Internal Use Only)</legend>
                <div class="vendorparam_value">
                    <?=empty($vendor['general_note']) ? '&nbsp;' : nl2br($vendor['general_note'])?>
                </div>
            </fieldset>
        </div>
    </div>
</div>

