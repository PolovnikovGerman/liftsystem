<div class="vendordetails-section-header">Purchase Order Info:</div>
<div class="vendordetails-section-body">
    <div class="content-row">
        <div class="vendorpocontact_value">
            <fieldset>
                <legend>PO Contact</legend>
                <div class="vendorparam_value">
                    <?=empty($vendor['po_contact']) ? '&nbsp;' : $vendor['po_contact']?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorpophone_value">
            <fieldset>
                <legend>PO Phone</legend>
                <div class="vendorparam_value">
                    <?=empty($vendor['po_phone']) ? '&nbsp;' : $vendor['po_phone']?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorpoemail_value">
            <fieldset>
                <legend>Send All POs to Email</legend>
                <div class="vendorparam_value">
                    <?=empty($vendor['po_email']) ? '&nbsp;' : $vendor['po_email']?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorpoemail_value">
            <fieldset>
                <legend>CC All POs to Email</legend>
                <div class="vendorparam_value">
                    <?=empty($vendor['po_ccemail']) ? '&nbsp;' : $vendor['po_ccemail']?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorpoemail_value">
            <fieldset>
                <legend>Also CC All POs to Email</legend>
                <div class="vendorparam_value">
                    <?=empty($vendor['po_bcemail']) ? '&nbsp;' : $vendor['po_bcemail']?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorshipaddr_value">
            <fieldset>
                <legend>Ship From Address</legend>
                <div class="vendorparam_value">
                    <?=empty($vendor['shipping_pickup']) ? '&nbsp;' : nl2br($vendor['shipping_pickup'])?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorinnernote_value">
            <fieldset>
                <legend>Note to go on ALL POs</legend>
                <div class="vendorparam_value">
                    <?=empty($vendor['internal_po_note']) ? '&nbsp;' : nl2br($vendor['internal_po_note'])?>
                </div>
            </fieldset>
        </div>
    </div>
</div>