<div class="vendordetails-section-header">Payment Info:</div>
<div class="vendordetails-section-body">
    <div class="row content-row">
        <div class="col-12">
            <div class="vendorpaymentcontact_value">
                <fieldset>
                    <legend>Payment Contact</legend>
                    <div class="vendorparam_value">
                        <?php if ($editmode==0) { ?>
                            <?=empty($vendor['payment_contact']) ? '&nbsp;' : $vendor['payment_contact']?>
                        <?php } else { ?>
                            <input type="text" class="form-group vendordetailsinpt" data-item="payment_contact" value="<?=$vendor['payment_contact']?>"/>
                        <?php } ?>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
    <div class="row content-row">
        <div class="col-12">
            <div class="vendorpaymentphone_value">
                <fieldset>
                    <legend>Payment Phone</legend>
                    <div class="vendorparam_value">
                        <?php if ($editmode==0) { ?>
                            <?=empty($vendor['payment_phone']) ? '&nbsp;' : $vendor['payment_phone']?>
                        <?php } else { ?>
                            <input type="text" class="vendordetailsinpt" data-item="payment_phone" value="<?=$vendor['payment_phone']?>"/>
                        <?php } ?>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
    <div class="row content-row">
        <div class="col-12">
            <div class="vendorpaymentemail_value">
                <fieldset>
                    <legend>Payment Email</legend>
                    <div class="vendorparam_value">
                        <?php if ($editmode==0) { ?>
                            <?=empty($vendor['payment_email']) ? '&nbsp;' : $vendor['payment_email']?>
                        <?php } else { ?>
                            <input type="text" class="vendordetailsinpt" data-item="payment_phone" value="<?=$vendor['payment_phone']?>"/>
                        <?php } ?>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
    <div class="row content-row">
        <div class="col-12">
            <div class="vendorpaymentterms_value">
                <fieldset>
                    <legend>Pay Before or After</legend>
                    <div class="col-12">
                        <div class="row" style="clear: both">
                            <div class="col-6">
                                <div class="vedorpaymentterm <?=$vendor['payment_prepay']==1 ? 'checked' : ''?>" data-item="payment_prepay">
                                    <div class="vendorparam_icon" data-item="payment_prepay">
                                        <?php if ($vendor['payment_prepay']==1) { ?>
                                            <i class="fa fa-check-circle-o" aria-hidden="true"></i>
                                        <?php } else { ?>
                                            <i class="fa fa-circle-o" aria-hidden="true"></i>
                                        <?php } ?>
                                    </div>
                                </div>
                                Prepay
                            </div>
                            <div class="col-6">
                                <div class="vedorpaymentterm <?=$vendor['payment_terms']==1 ? 'checked' : ''?>" data-item="payment_terms">
                                    <div class="vendorparam_icon" data-item="payment_terms">
                                        <?php if ($vendor['payment_terms']==1) { ?>
                                            <i class="fa fa-check-circle-o" aria-hidden="true"></i>
                                        <?php } else { ?>
                                            <i class="fa fa-circle-o" aria-hidden="true"></i>
                                        <?php } ?>
                                    </div>
                                    Terms
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
            <div class="vendorpaymentmethod_value">
                <fieldset>
                    <legend>Accepted Methods</legend>
                    <div class="col-12">
                        <div class="row" style="clear: both;">
                            <div class="col-6">
                                <div class="vedorpaymentmethod <?=$vendor['payment_accept_visa']==1 ? 'checked' : ''?>" data-item="payment_accept_visa" style="clear: both;">
                                    <div class="vendorparamcheck" data-item="payment_accept_visa">
                                        <?php if ($vendor['payment_accept_visa']==1) { ?>
                                            <i class="fa fa-check-square" aria-hidden="true"></i>
                                        <?php } else { ?>
                                            <i class="fa fa-square-o" aria-hidden="true"></i>
                                        <?php } ?>
                                    </div>
                                    Visa / MC
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="vedorpaymentmethod <?=$vendor['payment_accept_amex']==1 ? 'checked' : ''?>" data-item="payment_accept_amex" >
                                    <div class="vendorparamcheck" data-item="payment_accept_amex">
                                        <?php if ($vendor['payment_accept_amex']==1) { ?>
                                            <i class="fa fa-check-square" aria-hidden="true"></i>
                                        <?php } else { ?>
                                            <i class="fa fa-square-o" aria-hidden="true"></i>
                                        <?php } ?>
                                    </div>
                                    Amex
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="vedorpaymentmethod <?=$vendor['payment_accept_check']==1 ? 'checked' : ''?>" data-item="payment_accept_check">
                                    <div class="vendorparamcheck" data-item="payment_accept_check">
                                        <?php if ($vendor['payment_accept_check']==1) { ?>
                                            <i class="fa fa-check-square" aria-hidden="true"></i>
                                        <?php } else { ?>
                                            <i class="fa fa-square-o" aria-hidden="true"></i>
                                        <?php } ?>
                                    </div>
                                    Check
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="vedorpaymentmethod <?=$vendor['payment_accept_ach']==1 ? 'checked' : ''?>" data-item="payment_accept_ach">
                                    <div class="vendorparamcheck" data-item="payment_accept_ach">
                                        <?php if ($vendor['payment_accept_ach']==1) { ?>
                                            <i class="fa fa-check-square" aria-hidden="true"></i>
                                        <?php } else { ?>
                                            <i class="fa fa-square-o" aria-hidden="true"></i>
                                        <?php } ?>
                                    </div>
                                    ACH
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="vedorpaymentmethod <?=$vendor['payment_accept_paypal']==1 ? 'checked' : ''?>" data-item="payment_accept_paypal">
                                    <div class="vendorparamcheck" data-item="payment_accept_paypal">
                                        <?php if ($vendor['payment_accept_paypal']==1) { ?>
                                            <i class="fa fa-check-square" aria-hidden="true"></i>
                                        <?php } else { ?>
                                            <i class="fa fa-square-o" aria-hidden="true"></i>
                                        <?php } ?>
                                    </div>
                                    Paypal
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="vedorpaymentmethod <?=$vendor['payment_accept_wire']==1 ? 'checked' : ''?>" data-item="payment_accept_wire">
                                    <div class="vendorparamcheck" data-item="payment_accept_wire">
                                        <?php if ($vendor['payment_accept_wire']==1) { ?>
                                            <i class="fa fa-check-square" aria-hidden="true"></i>
                                        <?php } else { ?>
                                            <i class="fa fa-square-o" aria-hidden="true"></i>
                                        <?php } ?>
                                    </div>
                                    Wire
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
            <div class="vendoraccountdetail_value">
                <fieldset>
                    <legend>ACH / Wire / Paypal / Check Details</legend>
                    <div class="vendorparam_value">
                        <i class="fa fa-lock" aria-hidden="true"></i> Unlock to view
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
    <div class="row content-row">
        <div class="col-12">
            <div class="vendorinnernote_value">
                <fieldset>
                    <legend>Payment Notes (Internal Use Only)</legend>
                    <div class="vendorparam_value">
                        <?=empty($vendor['payment_note']) ? '&nbsp;' : nl2br($vendor['payment_note'])?>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
</div>