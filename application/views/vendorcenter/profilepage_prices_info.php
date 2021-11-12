<div class="vendordetails-section-header">Pricing Info:</div>
<div class="vendordetails-section-body">
    <div class="row content-row">
        <div class="col-12">
            <div class="vendorpricingcontact_value">
                <fieldset>
                    <legend>Pricing Contact</legend>
                    <div class="vendorparam_value">
                        <?php if ($editmode==0) { ?>
                            <?=empty($vendor['pricing_contact']) ? '&nbsp;' : $vendor['pricing_contact']?>
                        <?php } else { ?>
                            <input type="text" class="form-group vendordetailsinpt" data-item="pricing_contact" value="<?=$vendor['pricing_contact']?>"/>
                        <?php } ?>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
    <div class="row content-row">
        <div class="col-12">
            <div class="vendorpricingphone_value">
                <fieldset>
                    <legend>Pricing Phone</legend>
                    <div class="vendorparam_value">
                        <?php if ($editmode==0) { ?>
                            <?=empty($vendor['pricing_phone']) ? '&nbsp;' : formatPhoneNumber($vendor['pricing_phone'])?>
                        <?php } else { ?>
                            <input type="text" class="form-group vendordetailsphone" data-item="pricing_phone" value="<?=formatPhoneNumber($vendor['pricing_phone'])?>"/>
                        <?php } ?>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
    <div class="row content-row">
        <div class="col-12">
            <div class="vendorpricingemail_value">
                <fieldset>
                    <legend>Pricing Email</legend>
                    <div class="vendorparam_value">
                        <?php if ($editmode==0) { ?>
                            <?=empty($vendor['pricing_email']) ? '&nbsp;' : $vendor['pricing_email']?>
                        <?php } else { ?>
                            <input type="text" class="form-group vendordetailsinpt" data-item="pricing_email" value="<?=$vendor['pricing_email']?>"/>
                        <?php } ?>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
    <div class="row content-row">
        <div class="col-12">
            <div class="vendorpricingdocs_value">
                <fieldset>
                    <legend>Pricing Document</legend>
                    <?php if ($editmode==1) { ?>
                        <div class="addnewpricedoc">&nbsp;</div>
                    <?php } ?>
                    <div class="docspricelistsarea">
                        <?=$pricedocview?>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
</div>
