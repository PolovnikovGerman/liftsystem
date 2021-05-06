<div class="vendordetails-body">
    <div class="left-part">
        <div class="generalinfo">
            <div class="chapterlabel leftpart">General Info:</div>
            <div class="content-row">
                <div class="vendorparamlabel type">Type:</div>
                <?php if ($editmode==0) { ?>
                    <div class="viewparamvalue type"><?=$vendor['vendor_type']?></div>
                <?php } else { ?>
                    <select class="vendordetailsselect vendortype" data-item="vendor_type">
                        <option value="Supplier" <?=$vendor['vendor_type']=='Supplier' ? 'selected="selected"' : ''?>>Supplier</option>
                        <option value="Artwork" <?=$vendor['vendor_type']=='Artwork' ? 'selected="selected"' : ''?>>Artwork</option>
                        <option value="Shipping" <?=$vendor['vendor_type']=='Shipping' ? 'selected="selected"' : ''?>>Shipping</option>
                        <option value="Other" <?=$vendor['vendor_type']=='Other' ? 'selected="selected"' : ''?>>Other</option>
                    </select>
                <?php } ?>
            </div>
            <div class="content-row">
                <div class="vendorparamlabel country">Country:</div>
                <?php if ($editmode==0) { ?>
                    <div class="viewparamvalue country"><?=$vendor['country_name']?></div>
                <?php } else { ?>
                    <select class="vendordetailsselect vendorcountry" data-item="country_id">
                        <option value="">Select..</option>
                        <?php foreach ($countries as $country) { ?>
                            <option value="<?=$country['country_id']?>" <?=$country['country_id']==$vendor['country_id'] ? 'selected="selected"' : ''?>><?=$country['country_name']?></option>
                        <?php } ?>
                    </select>
                <?php } ?>
            </div>
            <div class="content-row">
                <div class="vendorparamlabel altname">Alt Name:</div>
                <?php if ($editmode==0) { ?>
                    <div class="viewparamvalue altname"><?=$vendor['alt_name']?></div>
                <?php } else { ?>
                    <input class="vendordetailsinpt altname" data-item="alt_name" value="<?=$vendor['alt_name']?>"/>
                <?php } ?>
            </div>
            <div class="content-row">
                <div class="vendorparamlabel asinumber">ASI #:</div>
                <?php if ($editmode==0) { ?>
                    <div class="viewparamvalue asinumber"><?=$vendor['vendor_asinumber']?></div>
                <?php } else { ?>
                    <input class="vendordetailsinpt asinumber" data-item="vendor_asinumber" value="<?=$vendor['vendor_asinumber']?>"/>
                <?php } ?>
            </div>
            <div class="content-row">
                <div class="vendorparamlabel ouraccount">Our Account #:</div>
                <?php if ($editmode==0) { ?>
                    <div class="viewparamvalue ouraccount"><?=$vendor['our_account_number']?></div>
                <?php } else { ?>
                    <input class="vendordetailsinpt ouraccount" data-item="our_account_number" value="<?=$vendor['our_account_number']?>"/>
                <?php } ?>
            </div>
            <div class="content-row">
                <div class="vendorparamlabel website">Web:</div>
                <?php if ($editmode==0) { ?>
                    <div class="viewparamvalue website"><?=$vendor['vendor_website']?></div>
                <?php } else { ?>
                    <input class="vendordetailsinpt website" data-item="vendor_website" value="<?=$vendor['vendor_website']?>"/>
                <?php } ?>
            </div>
            <div class="content-row">
                <div class="vendorparamlabel addres">Address:</div>
                <?php if ($editmode==0) { ?>
                    <div class="viewparamvalue address"><?=$vendor['address']?></div>
                <?php } else { ?>
                    <textarea class="vendordetailsinpt address" data-item="address" value="<?=$vendor['address']?>"/>
                <?php } ?>
            </div>
        </div>
        <div class="poinfo">
            <div class="chapterlabel leftpart">PO Info:</div>
            <div class="content-row">
                <div class="vendorparamlabel shippingpickup">Shipping/ Pickup Address (if diff):</div>
                <?php if ($editmode==0) { ?>
                    <div class="viewparamvalue shippingpickup"><?=$vendor['shipping_pickup']?></div>
                <?php } else { ?>
                    <textarea class="vendordetailsinpt shippingpickup" data-item="shipping_pickup" value="<?=$vendor['shipping_pickup']?>"/>
                <?php } ?>
            </div>
            <div class="content-row">
                <div class="vendorparamlabel zip">Ship From Zip:</div>
                <?php if ($editmode==0) { ?>
                    <div class="viewparamvalue zip"><?=$vendor['vendor_zipcode']?></div>
                <?php } else { ?>
                    <input class="vendordetailsinpt zip" data-item="vendor_zipcode" value="<?=$vendor['vendor_zipcode']?>"/>
                <?php } ?>
            </div>
            <div class="content-row">
                <div class="vendorparamlabel payments">Payments Accepted:</div>
            </div>
            <div class="content-row">
                <div class="vendorparamcheck <?=$editmode==1 ? 'edit' : ''?>" data-field="payment_accept_visa">
                    <?php if ($vendor['payment_accept_visa']==0) { ?>
                        <i class="fa fa-square-o" aria-hidden="true"></i>
                    <?php } else { ?>
                        <i class="fa fa-check-square" aria-hidden="true"></i>
                    <?php } ?>
                </div>
                <div class="vendorchecklabel">Visa / MC</div>
                <div class="vendorparamcheck <?=$editmode==1 ? 'edit' : ''?>" data-field="payment_accept_amex">
                    <?php if ($vendor['payment_accept_amex']==0) { ?>
                        <i class="fa fa-square-o" aria-hidden="true"></i>
                    <?php } else { ?>
                        <i class="fa fa-check-square" aria-hidden="true"></i>
                    <?php } ?>
                </div>
                <div class="vendorchecklabel amex">Amex</div>
                <div class="vendorparamcheck <?=$editmode==1 ? 'edit' : ''?>" data-field="payment_accept_terms">
                    <?php if ($vendor['payment_accept_terms']==0) { ?>
                        <i class="fa fa-square-o" aria-hidden="true"></i>
                    <?php } else { ?>
                        <i class="fa fa-check-square" aria-hidden="true"></i>
                    <?php } ?>
                </div>
                <div class="vendorchecklabel terms">Net Terms</div>
            </div>
            <div class="content-row">
                <div class="vendorparamcheck <?=$editmode==1 ? 'edit' : ''?>" data-field="payment_accept_check">
                    <?php if ($vendor['payment_accept_check']==0) { ?>
                        <i class="fa fa-square-o" aria-hidden="true"></i>
                    <?php } else { ?>
                        <i class="fa fa-check-square" aria-hidden="true"></i>
                    <?php } ?>
                </div>
                <div class="vendorchecklabel">Check</div>
                <div class="vendorparamcheck <?=$editmode==1 ? 'edit' : ''?>" data-field="payment_accept_wire">
                    <?php if ($vendor['payment_accept_wire']==0) { ?>
                        <i class="fa fa-square-o" aria-hidden="true"></i>
                    <?php } else { ?>
                        <i class="fa fa-check-square" aria-hidden="true"></i>
                    <?php } ?>
                </div>
                <div class="vendorchecklabel terms">Wire</div>
            </div>
            <div class="content-row">
                <div class="vendorparamlabel notes">Notes on PO for Vendor:</div>
            </div>
            <div class="content-row">
                <?php if ($editmode==0) { ?>
                    <div class="viewparamvalue ponotes"><?=$vendor['po_note']?></div>
                <?php } else { ?>
                    <textarea class="vendordetailsinpt ponotes" data-item="po_note" value="<?=$vendor['po_note']?>"/>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="right-part">
        <div class="contacts_info">
            <div class="chapterlabel contactinfo">Contact Info:</div>
            <div class="content-row">
                <div class="vendorcontactlabel name">Contact name</div>
                <div class="vendorcontactlabel phone">Phone</div>
                <div class="vendorcontactlabel mobile">Mobile</div>
                <div class="vendorcontactlabel email">Email</div>
                <div class="vendorcontactlabel po">PO</div>
                <div class="vendorcontactlabel art">Art</div>
                <div class="vendorcontactlabel pay">Pay</div>
            </div>
            <div id="vendorcontacts">
                <?=$contacts?>
            </div>
        </div>
        <div class="documents_info">
            <div class="chapterlabel docsinfo">Documents Info:</div>
            <div id="vendordocuments">
                <?=$docs?>
            </div>
        </div>
        <div class="internalnotes">
            <div class="chapterlabel internalnote">Internal Notes About Vendor:</div>
            <div class="content-row">
                <div class="vendorparamlabel notes">For Internal Use Only:</div>
            </div>
            <div class="content-row">
                <?php if ($editmode==0) { ?>
                    <div class="viewparamvalue interrnalnote"><?=$vendor['internal_po_note']?></div>
                <?php } else { ?>
                    <textarea class="vendordetailsinpt interrnalnote" data-item="internal_po_note" value="<?=$vendor['internal_po_note']?>"/>
                <?php } ?>
            </div>
        </div>

    </div>
</div>