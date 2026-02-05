<div class="customformdtails_area">
    <div class="custom-form">
        <div class="formleft">
            <div class="csf-inputgroup">
                <input type="text" readonly="readonly" value="<?=date('m/d/Y',strtotime($data['date_add']))?>" data-fld="date_add" class="custom-input-1">
                <label>Date:</label>
            </div>
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
                <select class="custom-input-1" id="shape_type" data-fld="shape_type" data-form="<?=$data['custom_quote_id']?>">
                    <option value="">Choose closest category</option>
                    <option value="mascot" <?=$data['shape_type']=='mascot' ? 'selected="selected"' : ''?>>Mascot / Character</option>
                    <option value="product_replica" <?=$data['shape_type']=='product_replica' ? 'selected="selected"' : ''?>>Product Replica / Industrial</option>
                    <option value="vehicle" <?=$data['shape_type']=='vehicle' ? 'selected="selected"' : ''?>>Vehicle</option>
                    <option value="logo" <?=$data['shape_type']=='logo' ? 'selected="selected"' : ''?>>Logo / Words</option>
                    <option value="food" <?=$data['shape_type']=='food' ? 'selected="selected"' : ''?>>Food & Drink</option>
                    <option value="buildings" <?=$data['shape_type']=='buildings' ? 'selected="selected"' : ''?>>Buildings / Structures</option>
                    <option value="people" <?=$data['shape_type']=='people' ? 'selected="selected"' : ''?>>People</option>
                    <option value="keychain" <?=$data['shape_type']=='keychain' ? 'selected="selected"' : ''?>>Keychain</option>
                    <option value="other" <?=$data['shape_type']=='other' ? 'selected="selected"' : ''?>>Other</option>
                </select>
                <label>Type:</label>
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
<input type="hidden" id="leademail_id" value="<?=$data['leademail_id']?>"/>