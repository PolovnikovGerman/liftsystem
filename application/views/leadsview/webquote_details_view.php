<div class="contant-popup">
    <div class="onlinequoteform">
        <div class="onlinequote-row">
            <div class="onlinequote-group onlinequote-company">
                <label>Company:</label>
                <input class="onlinequote-input" type="text" name="onlinequote-company" readonly="readonly" value="<?=$data['email_sendercompany']?>"/>
            </div>
            <div class="onlinequote-group onlinequote-contact">
                <label>Contact:</label>
                <input class="onlinequote-input" type="text" name="onlinequote-contact" readonly="readonly" value="<?=$data['email_sender']?>"/>
            </div>
            <div class="onlinequote-group onlinequote-date">
                <label>Date:</label>
                <input class="onlinequote-input" type="text" name="onlinequote-date" readonly="readonly" value="<?=$data['email_date']?>"/>
            </div>
        </div>
        <div class="onlinequote-row">
            <div class="onlinequote-group onlinequote-email">
                <label>Email:</label>
                <input class="onlinequote-input" type="text" name="onlinequote-email" readonly="readonly" value="<?=$data['email_sendermail']?>"/>
            </div>
            <div class="onlinequote-group onlinequote-contact">
                <label>Phone:</label>
                <input class="onlinequote-input" type="text" name="onlinequote-phone" readonly="readonly" value="<?=$data['email_senderphone']?>"/>
            </div>
        </div>
        <div class="onlinequotev-line">&nbsp;</div>
        <div class="onlinequote-row">
            <div class="onlinequote-group onlinequote-item">
                <label>Item:</label>
                <input class="onlinequote-select" type="text" name="onlinequote-item" readonly="readonly" value="<?=$data['email_item_number']?> - <?=$data['email_item_name']?>"/>
            </div>
            <div class="onlinequote-group onlinequote-qty">
                <label>Quantity:</label>
                <input class="onlinequote-input" type="text" name="onlinequote-contact" readonly="readonly" value="<?=$data['email_qty']?>"/>
            </div>
            <div class="onlinequote-group onlinequote-colors">
                <label>Colors:</label>
                <input class="onlinequote-input" type="text" name="onlinequote-date" readonly="readonly" value="<?=$data['colors']?>"/>
            </div>
        </div>
        <div class="onlinequote-row">
            <div class="onlinequote-group onlinequote-imprint">
                <label>Imprint:</label>
                <input class="onlinequote-select" readonly="readonly" value="<?=$data['colorimprint']?>"/>
            </div>
            <div class="onlinequote-group onlinequote-total">
                <label>TOTAL:</label>
                <input class="onlinequote-input" type="text" name="onlinequote-total" readonly="readonly" value="<?=$data['total']?>"/>
                <div class="btn-filepdf" data-link="<?=$data['email_quota_link']?>">
                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>
</div>
