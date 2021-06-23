<div class="vendordetails-section-header">Purchase Order Info:</div>
<div class="vendordetails-section-body">
    <div class="content-row">
        <div class="vendorpocontact_value">
            <fieldset>
                <legend>PO Contact</legend>
                <div class="vendorparam_value">
                    <?php if ($editmode==0) { ?>
                        <?=empty($vendor['po_contact']) ? '&nbsp;' : $vendor['po_contact']?>
                    <?php } else { ?>
                        <input type="text" class="vendordetailsinpt" data-item="po_contact" value="<?=$vendor['po_contact']?>"/>
                    <?php } ?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorpophone_value">
            <fieldset>
                <legend>PO Phone</legend>
                <div class="vendorparam_value">
                    <?php if ($editmode==0) { ?>
                        <?=empty($vendor['po_phone']) ? '&nbsp;' : $vendor['po_phone']?>
                    <?php } else { ?>
                        <input type="text" class="vendordetailsinpt" data-item="po_phone" value="<?=$vendor['po_phone']?>"/>
                    <?php } ?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorpoemail_value">
            <fieldset>
                <legend>Send All POs to Email</legend>
                <div class="vendorparam_value">
                    <?php if ($editmode==0) { ?>
                        <?=empty($vendor['po_email']) ? '&nbsp;' : $vendor['po_email']?>
                    <?php } else { ?>
                        <input type="text" class="vendordetailsinpt" data-item="po_email" value="<?=$vendor['po_email']?>"/>
                    <?php } ?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorpoemail_value">
            <fieldset>
                <legend>CC All POs to Email</legend>
                <div class="vendorparam_value">
                    <?php if ($editmode==0) { ?>
                        <?=empty($vendor['po_ccemail']) ? '&nbsp;' : $vendor['po_ccemail']?>
                    <?php } else { ?>
                        <input type="text" class="vendordetailsinpt" data-item="po_ccemail" value="<?=$vendor['po_ccemail']?>"/>
                    <?php } ?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorpoemail_value">
            <fieldset>
                <legend>Also CC All POs to Email</legend>
                <div class="vendorparam_value">
                    <?php if ($editmode==0) { ?>
                        <?=empty($vendor['po_bcemail']) ? '&nbsp;' : $vendor['po_bcemail']?>
                    <?php } else { ?>
                        <input type="text" class="vendordetailsinpt" data-item="po_bcemail" value="<?=$vendor['po_bcemail']?>"/>
                    <?php } ?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorshipaddr_value">
            <fieldset>
                <legend>Ship From Address</legend>
                <div class="vendorparam_value">
                    <?php if ($editmode==0) { ?>
                        <?=empty($vendor['shipping_pickup']) ? '&nbsp;' : nl2br($vendor['shipping_pickup'])?>
                    <?php }else { ?>
                        <textarea class="vendordetailsinpt address" data-item="shipping_pickup"><?=$vendor['shipping_pickup']?></textarea>
                    <?php } ?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorinnernote_value">
            <fieldset>
                <legend>Note to go on ALL POs</legend>
                <div class="vendorparam_value">
                    <?php if ($editmode==0) { ?>
                        <?=empty($vendor['po_note']) ? '&nbsp;' : nl2br($vendor['po_note'])?>
                    <?php } else { ?>
                        <textarea class="vendordetailsinpt notes" data-item="po_note"><?=$vendor['po_note']?></textarea>
                    <?php } ?>
                </div>
            </fieldset>
        </div>
    </div>
</div>