<div class="upload_pricelist_content">
    <div class="priceuploadrow">
        <div class="pricelist_manage">
            <fieldset>
                <legend>Year</legend>
                <div class="pricelistyears">
                    <select class="pricelistyearselect">
                        <?php foreach ($years as $year) { ?>
                            <option value="<?=$year?>"><?=$year?></option>
                        <?php } ?>
                    </select>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="priceuploadrow">
        <div id="pricelistuploadbtn">Upload</div>
    </div>
</div>