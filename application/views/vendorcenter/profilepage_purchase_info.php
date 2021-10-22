<div class="vendordetails-section-header">Purchase Order Info:</div>
<div class="vendordetails-section-body">
    <div class="row content-row">
        <div class="col-12">
            <div class="vendorpocontact_value">
                <fieldset>
                    <legend>PO Contact</legend>
                    <div class="vendorparam_value">
                        <?php if ($editmode==0) { ?>
                            <?=empty($vendor['po_contact']) ? '&nbsp;' : $vendor['po_contact']?>
                        <?php } else { ?>
                            <input type="text" class="form-group vendordetailsinpt" data-item="po_contact" value="<?=$vendor['po_contact']?>"/>
                        <?php } ?>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
    <div class="row content-row">
        <div class="col-12">
            <div class="vendorpophone_value">
                <fieldset>
                    <legend>PO Phone</legend>
                    <div class="vendorparam_value">
                        <?php if ($editmode==0) { ?>
                            <?=empty($vendor['po_phone']) ? '&nbsp;' : $vendor['po_phone']?>
                        <?php } else { ?>
                            <input type="text" class="form-group vendordetailsinpt" data-item="po_phone" value="<?=$vendor['po_phone']?>"/>
                        <?php } ?>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
    <div class="row content-row">
        <div class="col-12">
            <div class="vendorpoemail_value">
                <fieldset>
                    <legend>Send All POs to Email</legend>
                    <div class="vendorparam_value">
                        <?php if ($editmode==0) { ?>
                            <?=empty($vendor['po_email']) ? '&nbsp;' : $vendor['po_email']?>
                        <?php } else { ?>
                            <input type="text" class="form-group vendordetailsinpt" data-item="po_email" value="<?=$vendor['po_email']?>"/>
                        <?php } ?>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
    <div class="row content-row">
        <div class="col-12">
            <div class="vendorpoemail_value">
                <fieldset>
                    <legend>CC All POs to Email</legend>
                    <div class="vendorparam_value">
                        <?php if ($editmode==0) { ?>
                            <?=empty($vendor['po_ccemail']) ? '&nbsp;' : $vendor['po_ccemail']?>
                        <?php } else { ?>
                            <input type="text" class="form-group vendordetailsinpt" data-item="po_ccemail" value="<?=$vendor['po_ccemail']?>"/>
                        <?php } ?>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
    <div class="row content-row">
        <div class="col-12">
            <div class="vendorpoemail_value">
                <fieldset>
                    <legend>Also CC All POs to Email</legend>
                    <div class="vendorparam_value">
                        <?php if ($editmode==0) { ?>
                            <?=empty($vendor['po_bcemail']) ? '&nbsp;' : $vendor['po_bcemail']?>
                        <?php } else { ?>
                            <input type="text" class="form-group vendordetailsinpt" data-item="po_bcemail" value="<?=$vendor['po_bcemail']?>"/>
                        <?php } ?>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
    <div class="row content-row">
        <div class="col-12">
            <div class="vendorshipaddr_value">
                <fieldset>
                    <legend>Ship From Address</legend>
                    <div class="vendorparam_value">
                        <div class="row">
                            <div class="col-12">
                                <div class="fulladdress">
                                    <?php if ($editmode==0) { ?>
                                        <?=empty($vendor['shipaddr_line1']) ? '&nbsp;' : $vendor['shipaddr_line1']?>
                                    <?php }else { ?>
                                        <input class="form-group vendoraddress" data-item="shipaddr_line1" id="shipaddr_line1" name="shipaddr_line1" value="<?=$vendor['shipaddr_line1']?>"/>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="fulladdress">
                                    <?php if ($editmode==0) { ?>
                                        <?=empty($vendor['shipaddr_line2']) ? '&nbsp;' : $vendor['shipaddr_line2']?>
                                    <?php }else { ?>
                                        <input class="form-group vendoraddress" data-item="shipaddr_line2" id="shipaddr_line2" name="shipaddr_line2" value="<?=$vendor['shipaddr_line2']?>"/>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-7">
                                <div class="cityaddress">
                                    <?php if ($editmode==0) { ?>
                                        <?=empty($vendor['shipaddr_city']) ? '&nbsp;' : $vendor['shipaddr_city']?>
                                    <?php }else { ?>
                                        <input class="form-group vendoraddress" data-item="shipaddr_city" id="shipaddr_city" name="shipaddr_city" value="<?=$vendor['shipaddr_city']?>"/>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="stateaddress">
                                    <?php if ($editmode==0) { ?>
                                        <?=empty($vendor['shipaddr_state']) ? '&nbsp;' : $vendor['shipaddr_state']?>
                                    <?php }else { ?>
                                        <input class="form-group vendoraddress" data-item="shipaddr_state" id="shipaddr_state" name="shipaddr_state" value="<?=$vendor['shipaddr_state']?>"/>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="zipaddress">
                                    <?php if ($editmode==0) { ?>
                                        <?=empty($vendor['vendor_zipcode']) ? '&nbsp;' : $vendor['vendor_zipcode']?>
                                    <?php }else { ?>
                                        <input class="form-group vendoraddress" data-item="vendor_zipcode" id="vendor_zipcode" name="vendor_zipcode" value="<?=$vendor['vendor_zipcode']?>"/>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="fulladdress">
                                    <?php if ($editmode==0) { ?>
                                        <?=empty($vendor['shipaddr_country']) ? '&nbsp;' : $vendor['shipaddr_country']?>
                                    <?php }else { ?>
                                        <input class="form-group vendoraddress" data-item="shipaddr_country" id="shipaddr_country" name="shipaddr_country" value="<?=$vendor['shipaddr_country']?>"/>
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
</div>