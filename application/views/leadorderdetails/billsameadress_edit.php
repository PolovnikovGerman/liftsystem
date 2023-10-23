<div class="billing_content1 samebilling">
    <div class="billing_content1_line">
        <div class="billing_customerpotitle">PO#</div>
        <input type="text" class="billing_input3 input_border_gray billinginput leftalign" data-field="customer_ponum" placeholder="CuPo" value="<?= $billing['customer_ponum'] ?>">
    </div>
</div>
<div class="billing_content_linecenter">
    <div class="billing_samedata_area">
        <input type="checkbox" checked='checked' data-field="showbilladdress" title="Billing address as Shipping Address" data-entity="order" class="input_checkbox chkboxleadorddata"/>
        <div class="label">same as main shipping address</div>
    </div>
</div>
