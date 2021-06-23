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
        <?php if ($editmode==1) { ?>
            <div class="vendorchangemode">
                <?=$vendor['vendor_status']==1 ? 'Make Inactive' : 'Make Active'?>
            </div>
        <?php } ?>
    </div>
    <div class="content-row">
        <div class="vendorname_value">
            <fieldset>
                <legend>Vendor Name</legend>
                <div class="vendorparam_value">
                    <?php if ($editmode==0) { ?>
                        <?=$vendor['vendor_name']?>
                    <?php } else { ?>
                        <input type="text" class="vendordetailsinpt" data-item="vendor_name" value="<?=$vendor['vendor_name']?>"/>
                    <?php } ?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendoraltname_value">
            <fieldset>
                <legend>Alternate Name</legend>
                <div class="vendorparam_value">
                    <?php if ($editmode==0) { ?>
                        <?=empty($vendor['alt_name']) ? '&nbsp;' : $vendor['alt_name']?>
                    <?php } else { ?>
                        <input type="text" class="vendordetailsinpt" data-item="alt_name" value="<?=$vendor['alt_name']?>"/>
                    <?php } ?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendortype_value">
            <fieldset>
                <legend>Type</legend>
                <div class="vendorparam_value">
                    <?php if ($editmode==0) { ?>
                        <?=$vendor['vendor_type']?>
                    <?php } else { ?>
                        <select class="vendordetailsselect vendortype" data-item="vendor_type">
                            <option value="Supplier" <?=$vendor['vendor_type']=='Supplier' ? 'selected="selected"' : ''?>>Supplier</option>
                            <option value="Artwork" <?=$vendor['vendor_type']=='Artwork' ? 'selected="selected"' : ''?>>Artwork</option>
                            <option value="Shipping" <?=$vendor['vendor_type']=='Shipping' ? 'selected="selected"' : ''?>>Shipping</option>
                            <option value="Other" <?=$vendor['vendor_type']=='Other' ? 'selected="selected"' : ''?>>Other</option>
                        </select>
                    <?php } ?>
                </div>
            </fieldset>
        </div>
        <div class="vendorcountry_value">
            <fieldset>
                <legend>Country</legend>
                <div class="vendorparam_value">
                    <?php if ($editmode==0) { ?>
                        <?=$vendor['country_name']?>
                    <?php } else { ?>
                        <select class="vendordetailsselect vendorcountry" data-item="country_id">
                            <option value="">Select..</option>
                            <?php foreach ($countries as $country) { ?>
                                <option value="<?=$country['country_id']?>" <?=$country['country_id']==$vendor['country_id'] ? 'selected="selected"' : ''?>><?=$country['country_name']?></option>
                            <?php } ?>
                        </select>
                    <?php } ?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorasi_value">
            <fieldset>
                <legend>ASI #</legend>
                <div class="vendorparam_value">
                    <?php if ($editmode==0) { ?>
                        <?=empty($vendor['vendor_asinumber']) ? '&nbsp;' : $vendor['vendor_asinumber']?>
                    <?php } else { ?>
                        <input type="text" class="vendordetailsinpt" data-item="vendor_asinumber" value="<?=$vendor['vendor_asinumber']?>"/>
                    <?php } ?>
                </div>
            </fieldset>
        </div>
        <div class="vedorouraccount_value">
            <fieldset>
                <legend>Our Account #</legend>
                <div class="vendorparam_value">
                    <?php if ($editmode==0) { ?>
                        <?=empty($vendor['our_account_number']) ? '&nbsp;' : $vendor['our_account_number']?>
                    <?php } else { ?>
                        <input type="text" class="vendordetailsinpt" data-item="our_account_number" value="<?=$vendor['our_account_number']?>"/>
                    <?php } ?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorweb_value">
            <fieldset>
                <legend>Website</legend>
                <div class="vendorparam_value">
                    <?php if ($editmode==0) { ?>
                        <?=empty($vendor['vendor_website']) ? '&nbsp;': $vendor['vendor_website']?>
                    <?php } else { ?>
                        <input type="text" class="vendordetailsinpt" data-item="vendor_website" value="<?=$vendor['vendor_website']?>"/>
                    <?php } ?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorphone_value">
            <fieldset>
                <legend>Main Phone</legend>
                <div class="vendorparam_value">
                    <?php if ($editmode==0) { ?>
                        <?=empty($vendor['vendor_phone']) ? '&nbsp;' : $vendor['vendor_phone']?>
                    <?php } else { ?>
                        <input type="text" class="vendordetailsinpt" data-item="vendor_phone" value="<?=$vendor['vendor_phone']?>"/>
                    <?php } ?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendoraddress_value">
            <fieldset>
                <legend>Address</legend>
                <div class="vendorparam_value">
                    <?php if ($editmode==0) { ?>
                        <?=empty($vendor['address']) ? '&nbsp;' : nl2br($vendor['address'])?>
                    <?php } else {  ?>
                        <textarea class="vendordetailsinpt address" data-item="address"><?=$vendor['address']?></textarea>
                    <?php }  ?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorinnernote_value">
            <fieldset>
                <legend>Notes (Internal Use Only)</legend>
                <div class="vendorparam_value">
                    <?php if ($editmode==0) { ?>
                        <?=empty($vendor['general_note']) ? '&nbsp;' : nl2br($vendor['general_note'])?>
                    <?php } else { ?>
                        <textarea class="vendordetailsinpt notes" data-item="general_note"><?=$vendor['general_note']?></textarea>
                    <?php } ?>
                </div>
            </fieldset>
        </div>
    </div>
</div>