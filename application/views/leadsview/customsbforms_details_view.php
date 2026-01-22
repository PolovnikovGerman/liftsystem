<input type="hidden" id="leademail_id" value="<?=$data['leademail_id']?>"/>
<div class="contant-popup">
    <div class="ipcustomsbform">
        <div class="customform-left">
            <div class="customform-row">
                <div class="customform-group customform-date">
                    <label>Date:</label>
                    <input type="text" name="customform-date" readonly="readonly" value="<?=date('m/d/Y',strtotime($data['date_add']))?>"/>
                </div>
            </div>
            <div class="customform-row">
                <div class="customform-group customform-company">
                    <label>Company:</label>
                    <input type="text" name="customform-company" readonly="readonly" value="<?=$data['customer_company']?>"/>
                </div>
            </div>
            <div class="customform-row">
                <div class="customform-group customform-contact">
                    <label>Contact:</label>
                    <input type="text" name="customform-contact" readonly="readonly" value="<?=$data['customer_name']?>"/>
                </div>
            </div>
            <div class="customform-row">
                <div class="customform-group customform-phone">
                    <label>Phone:</label>
                    <input type="text" name="customform-phone" readonly="readonly" value="<?=$data['customer_phone']?>"/>
                </div>
            </div>
            <div class="customform-row">
                <div class="customform-group customform-email">
                    <label>Email:</label>
                    <input type="text" name="customform-email" readonly="readonly" value="<?=$data['customer_email']?>"/>
                </div>
            </div>
            <div class="customform-row">
                <div class="customform-group">
                    <textarea readonly="readonly"><?=$data['shape_desription']?></textarea>
                </div>
            </div>
            <div class="customform-row">
                <div class="customform-group customform-qty">
                    <label>Qty:</label>
                    <input class="" type="text" name="customform-qty" readonly="readonly" value="<?=$data['quota_qty']?>"/>
                </div>
                <div class="customform-group customform-event">
                    <label>Event:</label>
                    <input type="text" name="customform-event" readonly="readonly" value="<?=empty($data['ship_date']) ? '' : date('m/d/Y', $data['ship_date'])?>"/>
                </div>
            </div>
        </div>
        <div class="customform-right">
            <div class="customform-row">
                <div class="customform-group  customform-addressline">
                    <label>Shipping:</label>
                    <input type="text" name="customform-addressline-1" readonly="readonly" value="<?=$data['ship_address1']?>"/>
                </div>
            </div>
            <div class="customform-row">
                <div class="customform-group customform-addressline">
                    <input type="text" name="customform-addressline-2" readonly="readonly" value="<?=$data['ship_address2']?>"/>
                </div>
            </div>
            <div class="customform-row">
                <div class="customform-group customform-city">
                    <input type="text" name="customform-city" readonly="readonly" value="<?=$data['ship_city']?>"/>
                </div>
                <div class="customform-group customform-state">
                    <input type="text" name="customform-state" readonly="readonly" value="<?=$data['ship_state']?>">
                </div>
                <div class="customform-group customform-zipcode">
                    <input type="text" name="customform-zipcode" readonly="readonly" value="<?=$data['ship_city']?>"/>
                </div>
            </div>
            <div class="customform-row">
                <div class="customform-group customform-country">
                    <input type="text" name="customform-country" readonly="readonly" value="<?=$data['country_name']?>"/>
                </div>
            </div>
            <div class="customform-row">
                <div class="customform-images">
                    <div class="customform-imgstitle">Attached images:</div>
                    <?=$attach?>
                </div>
            </div>
        </div>
    </div>
</div>
