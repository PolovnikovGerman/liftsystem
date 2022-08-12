<div class="customformdtails_area">
    <div class="custom-form">
        <div class="formleft">
            <div class="csf-inputgroup">
                <input type="text" readonly="readonly" value="<?=$data['customer_name']?>" data-fld="customer_name" class="custom-input-1">
                <label>Customer Name:</label>
            </div>
            <div class="csf-inputgroup">
                <input type="text" readonly="readonly" value="<?=$data['customer_company']?>" data-fld="customer_company" class="custom-input-1">
                <label>Company:</label>
            </div>
            <div class="csf-inputgroup">
                <input type="text" readonly="readonly" value="<?=$data['customer_phone']?>" data-fld="customer_phone" class="custom-input-1">
                <label>Phone:</label>
            </div>
            <div class="csf-inputgroup">
                <input type="text" readonly="readonly" value="<?=$data['customer_email']?>" data-fld="customer_email" class="custom-input-1">
                <label>Email:</label>
            </div>
            <div class="csf-inputgroup">
            <textarea  readonly="readonly" data-fld="shape_desription" placeholder="Describe the shape..."><?=$data['shape_desription']?></textarea>
            </div>
            <div class="csf-inputgroup">
                <div class="csf-group-1">
                    <input type="text" readonly="readonly" value=<?=$data['quota_qty']?> name="qty" class="custom-input-2">
                    <label>Qty:</label>
                </div>
                <div class="csf-group-2">
                    <input type="text" readonly="readonly" value="<?=empty($data['ship_date']) ? '' : date('m/d/Y', $data['ship_date'])?>" class="custom-input-2">
                    <label>Event:</label>
                </div>
            </div>
        </div>
        <div class="formright">
            <div class="csf-inputgroup">
                <input type="text" readonly="readonly" value="<?=$data['ship_address1']?>" class="custom-input-1" placeholder="Address">
                <label>Shipping:</label>
            </div>
            <div class="csf-inputgroup">
                <input type="text" readonly="readonly" id="<?=$data['ship_address2']?>" class="custom-input-1" placeholder="Address 2">
            </div>
            <div class="csf-inputgroup">
                <input type="text" readonly="readonly" value="<?=$data['ship_zipcode']?>" class="custom-input-5" placeholder="Zip">
                <input type="text" readonly="readonly" value="<?=$data['ship_state']?>" class="custom-input-4" placeholder="State">
                <input type="text" readonly="readonly" value="<?=$data['ship_city']?>" class="custom-input-3" placeholder="City">
            </div>
            <div class="csf-inputgroup">
                <input type="text" readonly="readonly" value="<?=$data['country_name']?>" class="custom-input-1" placeholder="Country">
            </div>
            <div id="attacharea">
                <?=$attach?>
                <!-- attached -->
            </div>
        </div>
    </div>
</div>
