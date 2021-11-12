<div class="vendordetails-section-header">Customer Service Info:</div>
<div class="vendordetails-section-body">
    <div class="row content-row">
        <div class="col-12">
            <div class="vendorcustomercontact_value">
                <fieldset>
                    <legend>Customer Service Contact</legend>
                    <div class="vendorparam_value">
                        <?php if ($editmode==0) { ?>
                            <?=empty($vendor['customer_contact']) ? '&nbsp;' : $vendor['customer_contact']?>
                        <?php } else { ?>
                            <input type="text" class="form-group vendordetailsinpt" data-item="customer_contact" value="<?=$vendor['customer_contact']?>"/>
                        <?php } ?>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
    <div class="row content-row">
        <div class="col-12">
            <div class="vendorcustomerphone_value">
                <fieldset>
                    <legend>Customer Service Phone</legend>
                    <div class="vendorparam_value">
                        <?php if ($editmode==0) { ?>
                            <?=empty($vendor['customer_phone']) ? '&nbsp;' : formatPhoneNumber($vendor['customer_phone'])?>
                        <?php } else { ?>
                            <input type="text" class="form-group vendordetailsphone" data-item="customer_phone" value="<?=formatPhoneNumber($vendor['customer_phone'])?>"/>
                        <?php } ?>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
    <div class="row content-row">
        <div class="col-12">
            <div class="vendorcustomeremail_value">
                <fieldset>
                    <legend>Customer Service Email</legend>
                    <div class="vendorparam_value">
                        <?php if ($editmode==0) { ?>
                            <?=empty($vendor['customer_email']) ? '&nbsp;' : $vendor['customer_email']?>
                        <?php } else { ?>
                            <input type="text" class="form-group vendordetailsinpt" data-item="customer_email" value="<?=$vendor['customer_email']?>"/>
                        <?php } ?>
                        <div class="vendorparam_value">
                </fieldset>
            </div>
        </div>
    </div>
</div>
