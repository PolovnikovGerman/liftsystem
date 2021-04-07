<div class="vendordata">
    <form id="vendordat">
        <input type="hidden" id="vendor_id" name="vendor_id" value="<?= $vendor['vendor_id'] ?>"/>

        <div class="clearfix"></div>
        <div class="input_row">
            <div class="labeltxt">Vendor Name:</div>
            <div class="inputval">
                <input type="text" class="large" name="vendor_name" id="vendor_name"
                       value="<?= $vendor['vendor_name'] ?>"/>
            </div>
        </div>
        <div class="input_row">
            <div class="labeltxt">Vendor Zip Code:</div>
            <div class="inputval">
                <input type="text" class="short" name="vendor_zipcode" id="vendor_zipcode"
                       value="<?= $vendor['vendor_zipcode'] ?>"/>
            </div>
        </div>
        <div class="input_row">
            <div class="labeltxt">Calendar:</div>
            <div class="inputval">
                <select name="calendar_id" id="calendar_id" class="calendarselect">
                    <option value="">Select calendar</option>
                    <?php foreach ($calendars as $row) { ?>
                        <option value="<?= $row['calendar_id'] ?>" <?= ($row['calendar_id'] == $vendor['calendar_id'] ? 'selected="selected"' : '') ?>>
                            <?= $row['calendar_name'] ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="input_row">
            <div class="labeltxt">Vendor Phone:</div>
            <div class="inputval">
                <input type="text" class="medium" name="vendor_phone" id="vendor_phone"
                       value="<?= $vendor['vendor_phone'] ?>"/>
            </div>
        </div>
        <div class="input_row">
            <div class="labeltxt">Vendor Email:</div>
            <div class="inputval">
                <input type="text" class="long" name="vendor_email" id="vendor_email"
                       value="<?= $vendor['vendor_email'] ?>"/>
            </div>
        </div>
        <div class="input_row">
            <div class="labeltxt">Vendor WEB:</div>
            <div class="inputval">
                <input type="text" class="long" name="vendor_website" id="vendor_website"
                       value="<?= $vendor['vendor_website'] ?>"/>
            </div>
        </div>

        <div class="input_row">
            <div class="savevendor">
                <a id="savevendor" href="javascript:void(0)">
                    Save
                </a>
            </div>
        </div>
    </form>
</div>
