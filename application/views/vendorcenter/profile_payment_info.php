<div class="vendordetails-section-header">Payment Info:</div>
<div class="vendordetails-section-body">
    <div class="content-row">
        <div class="vendorpaymentcontact_value">
            <fieldset>
                <legend>Payment Contact</legend>
                <div class="vendorparam_value">
                    <?=empty($vendor['payment_contact']) ? '&nbsp;' : $vendor['payment_contact']?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorpaymentphone_value">
            <fieldset>
                <legend>Payment Phone</legend>
                <div class="vendorparam_value">
                    <?=empty($vendor['payment_phone']) ? '^nbsp;' : $vendor['payment_phone']?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorpaymentemail_value">
            <fieldset>
                <legend>Payment Email</legend>
                <div class="vendorparam_value">
                    <?=empty($vendor['payment_email']) ? '&nbsp;' : $vendor['payment_email']?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorpaymentterms_value">
            <fieldset>
                <legend>Pay Before or After</legend>
                <div class="vedorpaymentterm <?=$vendor['payment_prepay']==1 ? 'checked' : ''?>" style="clear: both;">
                    <?php if ($vendor['payment_prepay']==1) { ?>
                        <i class="fa fa-check-circle-o" aria-hidden="true">
                    <?php } else { ?>
                        <i class="fa fa-circle-o" aria-hidden="true"></i>
                    <?php } ?>
                    Prepay
                </div>
                <div class="vedorpaymentterm <?=$vendor['payment_terms']==1 ? 'checked' : ''?>">
                    <?php if ($vendor['payment_terms']==1) { ?>
                        <i class="fa fa-check-circle-o" aria-hidden="true">
                    <?php } else { ?>
                        <i class="fa fa-circle-o" aria-hidden="true"></i>
                    <?php } ?>
                    Terms
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorpaymentmethod_value">
            <fieldset>
                <legend>Accepted Methods</legend>
                <div class="vedorpaymentmethod" style="clear: both;">
                    <?php if ($vendor['payment_accept_visa']==1) { ?>
                        <i class="fa fa-check-square" aria-hidden="true"></i>
                    <?php } else { ?>
                        <i class="fa fa-square-o" aria-hidden="true"></i>
                    <?php } ?>
                    Visa / MC
                </div>
                <div class="vedorpaymentmethod">
                    <?php if ($vendor['payment_accept_amex']==1) { ?>
                        <i class="fa fa-check-square" aria-hidden="true"></i>
                    <?php } else { ?>
                        <i class="fa fa-square-o" aria-hidden="true"></i>
                    <?php } ?>
                    Amex
                </div>
                <div class="vedorpaymentmethod">
                    <?php if ($vendor['payment_accept_check']==1) { ?>
                        <i class="fa fa-check-square" aria-hidden="true"></i>
                    <?php } else { ?>
                        <i class="fa fa-square-o" aria-hidden="true"></i>
                    <?php } ?>
                     Check
                </div>
                <div class="vedorpaymentmethod">
                    <?php if ($vendor['payment_accept_ach']==1) { ?>
                        <i class="fa fa-check-square" aria-hidden="true"></i>
                    <?php } else { ?>
                        <i class="fa fa-square-o" aria-hidden="true"></i>
                    <?php } ?>
                    ACH
                </div>
                <div class="vedorpaymentmethod">
                    <?php if ($vendor['payment_accept_paypal']==1) { ?>
                        <i class="fa fa-check-square" aria-hidden="true"></i>
                    <?php } else { ?>
                        <i class="fa fa-square-o" aria-hidden="true"></i>
                    <?php } ?>
                     Paypal
                </div>
                <div class="vedorpaymentmethod">
                    <?php if ($vendor['payment_accept_wire']==1) { ?>
                        <i class="fa fa-check-square" aria-hidden="true"></i>
                    <?php } else { ?>
                        <i class="fa fa-square-o" aria-hidden="true"></i>
                    <?php } ?>
                     Wire
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
        <div class="vendoraccountdetail_value">
            <fieldset>
                <legend>ACH / Wire / Paypal / Check Details</legend>
                <div class="vendorparam_value">
                    <i class="fa fa-lock" aria-hidden="true"></i> Unlock to view
                </div>
            </fieldset>
        </div>
    </div>
    <div class="content-row">
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