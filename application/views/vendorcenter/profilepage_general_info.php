<div class="vendordetails-section-header">General Info:</div>
<div class="vendordetails-section-body">
    <div class="row content-row">
        <div class="col-6">
            <div class="vendorslug_value">
                <fieldset>
                    <legend>Vendor #</legend>
                    <div class="vendorparam_value">
                        <?=$vendor['vendor_slug']?>
                    </div>
                </fieldset>
            </div>
        </div>
        <?php if ($editmode==1) { ?>
            <div class="col-6">
                <div class="vendorchangemode">
                    <?=$vendor['vendor_status']==1 ? 'Make Inactive' : 'Make Active'?>
                </div>
            </div>
        <?php } ?>
    </div>
    <div class="row content-row">
        <div class="col-12">
            <div class="vendorname_value vendorvaluearea" data-item="vendor_name">
                <fieldset>
                    <legend>Vendor Name</legend>
                    <div class="vendorparam_value">
                        <?php if ($editmode==0) { ?>
                            <?=$vendor['vendor_name']?>
                        <?php } else { ?>
                            <input type="text" class="form-group vendordetailsinpt" data-item="vendor_name" value="<?=$vendor['vendor_name']?>"/>
                        <?php } ?>
                    </div>
                </fieldset>
                <span class="vendormandatoryfld">*Required</span>
            </div>
        </div>
    </div>
    <div class="row content-row">
        <div class="col-12">
            <div class="vendoraltname_value">
                <fieldset>
                    <legend>Alternate Name</legend>
                    <div class="vendorparam_value">
                        <?php if ($editmode==0) { ?>
                            <?=empty($vendor['alt_name']) ? '&nbsp;' : $vendor['alt_name']?>
                        <?php } else { ?>
                            <input type="text" class="form-group vendordetailsinpt" data-item="alt_name" value="<?=$vendor['alt_name']?>"/>
                        <?php } ?>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
    <div class="row content-row">
        <div class="col-6">
            <div class="vendortype_value vendorvaluearea" data-item="vendor_type">
                <fieldset>
                    <legend>Type</legend>
                    <div class="vendorparam_value">
                        <?php if ($editmode==0) { ?>
                            <?=$vendor['vendor_type']?>
                        <?php } else { ?>
                            <select class="form-group vendordetailsselect vendortype" data-item="vendor_type">
                                <option value="Supplier" <?=$vendor['vendor_type']=='Supplier' ? 'selected="selected"' : ''?>>Supplier</option>
                                <option value="Artwork" <?=$vendor['vendor_type']=='Artwork' ? 'selected="selected"' : ''?>>Artwork</option>
                                <option value="Shipping" <?=$vendor['vendor_type']=='Shipping' ? 'selected="selected"' : ''?>>Shipping</option>
                                <option value="Other" <?=$vendor['vendor_type']=='Other' ? 'selected="selected"' : ''?>>Other</option>
                            </select>
                        <?php } ?>
                    </div>
                </fieldset>
                <span class="vendormandatoryfld">*Required</span>
            </div>
        </div>
        <div class="col-6">
            <div class="vendorcountry_value vendorvaluearea" data-item="country_id">
                <fieldset>
                    <legend>Country</legend>
                    <div class="vendorparam_value">
                        <?php if ($editmode==0) { ?>
                            <?=$vendor['country_name']?>
                        <?php } else { ?>
                            <select class="form-group vendordetailsselect vendorcountry" data-item="country_id">
                                <option value="">Select..</option>
                                <?php foreach ($countries as $country) { ?>
                                    <option value="<?=$country['country_id']?>" <?=$country['country_id']==$vendor['country_id'] ? 'selected="selected"' : ''?>><?=$country['country_name']?></option>
                                <?php } ?>
                            </select>
                        <?php } ?>
                    </div>
                </fieldset>
                <span class="vendormandatoryfld">*Required</span>
            </div>
        </div>
    </div>
    <div class="row content-row">
        <div class="col-6">
            <div class="vendorasi_value">
                <fieldset>
                    <legend>ASI #</legend>
                    <div class="vendorparam_value">
                        <?php if ($editmode==0) { ?>
                            <?=empty($vendor['vendor_asinumber']) ? '&nbsp;' : $vendor['vendor_asinumber']?>
                        <?php } else { ?>
                            <input type="text" class="form-group vendordetailsinpt" data-item="vendor_asinumber" value="<?=$vendor['vendor_asinumber']?>"/>
                        <?php } ?>
                    </div>
                </fieldset>
            </div>
        </div>
        <div class="col-6">
            <div class="vedorouraccount_value">
                <fieldset>
                    <legend>Our Account #</legend>
                    <div class="vendorparam_value">
                        <?php if ($editmode==0) { ?>
                            <?=empty($vendor['our_account_number']) ? '&nbsp;' : $vendor['our_account_number']?>
                        <?php } else { ?>
                            <input type="text" class="form-group vendordetailsinpt" data-item="our_account_number" value="<?=$vendor['our_account_number']?>"/>
                        <?php } ?>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
    <div class="row content-row">
        <div class="col-12">
            <div class="vendorweb_value">
                <fieldset>
                    <legend>Website</legend>
                    <div class="vendorparam_value <?=empty($vendor['vendor_website']) ? '' : 'vendorweburl'?>" data-weburl="<?=$vendor['vendor_website']?>">
                        <?php if ($editmode==0) { ?>
                            <?=empty($vendor['vendor_website']) ? '&nbsp;': $vendor['vendor_website']?>
                        <?php } else { ?>
                            <input type="text" class="vendordetailsinpt" data-item="vendor_website" value="<?=$vendor['vendor_website']?>"/>
                        <?php } ?>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
    <div class="row content-row">
        <div class="col-12">
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
    </div>
    <div class="row content-row">
        <div class="col-12">
            <div class="vendoraddress_value">
                <fieldset>
                    <legend>Address</legend>
                    <div class="vendorparam_value">
                        <div class="row">
                            <div class="col-12">
                                <div class="fulladdress">
                                    <?php if ($editmode==0) { ?>
                                        <?=empty($vendor['address_line1']) ? '&nbsp;' : $vendor['address_line1']?>
                                    <?php } else { ?>
                                        <input class="vendoraddress" data-item="address_line1" id="address_line1" name="address_line1" value="<?=$vendor['address_line1']?>"/>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="fulladdress">
                                    <?php if ($editmode==0) { ?>
                                        <?=empty($vendor['address_line2']) ? '&nbsp;' : $vendor['address_line2']?>
                                    <?php } else { ?>
                                        <input class="vendoraddress" data-item="address_line2" id="address_line2" name="address_line2" value="<?=$vendor['address_line2']?>"/>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-7">
                                <div class="cityaddress">
                                    <?php if ($editmode==0) { ?>
                                        <?=empty($vendor['address_city']) ? '&nbsp;' : $vendor['address_city']?>
                                    <?php } else { ?>
                                        <input class="vendoraddress"  data-item="address_city" id="address_city" name="address_city" value="<?=$vendor['address_city']?>"/>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="stateaddress">
                                    <?php if ($editmode==0) { ?>
                                        <?=empty($vendor['address_state'])  ? '&nbsp;' : $vendor['address_state']?>
                                    <?php } else { ?>
                                        <input class="vendoraddress" data-item="address_state" id="address_state" name="address_state" value="<?=$vendor['address_state']?>"/>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="zipaddress">
                                    <?php if ($editmode==0) { ?>
                                        <?=empty($vendor['address_zip']) ? '&nbsp;' : $vendor['address_zip']?>
                                    <?php } else { ?>
                                        <input class="vendoraddress" data-item="address_zip" id="address_zip" name="address_zip" value="<?=$vendor['address_zip']?>"/>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="fulladdress">
                                    <?php if ($editmode==0) { ?>
                                        <?=empty($vendor['address_country']) ? '&nbsp;' : $vendor['address_country']?>
                                    <?php } else { ?>
                                        <input class="vendoraddress" data-item="address_country" id="address_country" name="address_country" value="<?=$vendor['address_country']?>"/>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
    <div class="row content-row">
        <div class="col-12">
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
</div>