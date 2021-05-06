<?php $numpp = 1;?>
<?php foreach ($vendor_contacts as $row) { ?>
    <div class="vedorcontactrow">
        <div class="content-row">
            <div class="vendorcontactvalue numpp">
                <?php if ($editmode==0) { ?>
                    <?=$numpp?>
                <?php } else { ?>
                    <div class="removevendorcontact" data-idx="<?=$row['vendor_contact_id']?>">
                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                    </div>
                <?php } ?>
            </div>
            <div class="vendorcontactvalue">
                <?php if ($editmode==0) { ?>
                    <div class="viewparamvalue contactname"><?=$row['contact_name']?></div>
                <?php } else { ?>
                    <input class="vendordetailsinpt contactname" data-filed="contact_name" data-idx="<?=$row['vendor_contact_id']?>" value="<?=$row['contact_name']?>"/>
                <?php } ?>
            </div>
            <div class="vendorcontactvalue">
                <?php if ($editmode==0) { ?>
                    <div class="viewparamvalue contactphone"><?=$row['contact_phone']?></div>
                <?php } else { ?>
                    <input class="vendordetailsinpt contactphone" data-field="contact_phone" data-idx="<?=$row['vendor_contact_id']?>" value="<?=$row['contact_phone']?>"/>
                <?php } ?>
            </div>
            <div class="vendorcontactvalue">
                <?php if ($editmode==0) { ?>
                    <div class="viewparamvalue contactphone"><?=$row['contact_cellphone']?></div>
                <?php } else { ?>
                    <input class="vendordetailsinpt contactphone" data-field="contact_cellphone" data-idx="<?=$row['vendor_contact_id']?>" value="<?=$row['contact_cellphone']?>"/>
                <?php } ?>
            </div>
            <div class="vendorcontactvalue">
                <?php if ($editmode==0) { ?>
                    <div class="viewparamvalue contactemail"><?=$row['contact_email']?></div>
                <?php } else { ?>
                    <input class="vendordetailsinpt contactemail" data-field="contact_email" data-idx="<?=$row['vendor_contact_id']?>" value="<?=$row['contact_email']?>"/>
                <?php } ?>
            </div>
            <div class="vendorcontactvalue">
                <div class="vendorcontactcheck <?=$editmode==0 ? '' : 'edit'?>" data-idx="<?=$row['vendor_contact_id']?>" data-field="contact_po">
                    <?php if ($row['contact_po']==0) { ?>
                        <i class="fa fa-square-o" aria-hidden="true"></i>
                    <?php } else { ?>
                        <i class="fa fa-check-square" aria-hidden="true"></i>
                    <?php } ?>
                </div>
            </div>
            <div class="vendorcontactvalue">
                <div class="vendorcontactcheck <?=$editmode==0 ? '' : 'edit'?>" data-idx="<?=$row['vendor_contact_id']?>" data-field="contact_art">
                    <?php if ($row['contact_art']==0) { ?>
                        <i class="fa fa-square-o" aria-hidden="true"></i>
                    <?php } else { ?>
                        <i class="fa fa-check-square" aria-hidden="true"></i>
                    <?php } ?>
                </div>
            </div>
            <div class="vendorcontactvalue">
                <div class="vendorcontactcheck <?=$editmode==0 ? '' : 'edit'?>" data-idx="<?=$row['vendor_contact_id']?>" data-field="contact_pay">
                    <?php if ($row['contact_pay']==0) { ?>
                        <i class="fa fa-square-o" aria-hidden="true"></i>
                    <?php } else { ?>
                        <i class="fa fa-check-square" aria-hidden="true"></i>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="content-row">
            <div class="vendorparamlabel">Note / Description about Above:</div>
            <div class="vendorcontactvalue contactnote">
                <?php if ($editmode==0) { ?>
                    <div class="viewparamvalue contactnote"><?=$row['contact_note']?></div>
                <?php } else { ?>
                    <textarea class="vendordetailsinpt contactnote" data-field="contact_note" data-idx="<?=$row['vendor_contact_id']?>"><?=$row['contact_note']?></textarea>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php $numpp++;?>
<?php } ?>
