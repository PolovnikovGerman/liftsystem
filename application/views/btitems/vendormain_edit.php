<div class="relievers_vendormain">
    <input type="hidden" id="vendor_item_id" value="<?=$vendor_item['vendor_item_id']?>"/>
    <div class="sectionlabel">SUPPLIER:</div>
    <div class="vendormainbody">
        <div class="content-row">
            <div class="itemparamlabel vendorname">Supplier</div>
            <div class="itemparamvalue editmode vendorname">
                <select class="vendornameinp <?=empty($vendor['vendor_name']) ? 'missing_info' : ''?>" data-item="vendor_item_vendor">
                    <option value=""></option>
                    <?php foreach ($vendors as $vrow) { ?>
                        <option value="<?=$vrow['vendor_id']?>" <?=$vrow['vendor_id']==$vendor['vendor_id'] ? 'selected="selected"' : ''?>><?=$vrow['vendor_name']?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="content-row">
            <div class="itemparamlabel vendorname">Su. Item #:</div>
            <div class="itemparamvalue editmode vendorname">
                <input type="text" class="vendordatainpt vendoritemnum <?=empty($vendor_item['vendor_item_number']) ? 'missing_info' : ''?>" id="vendor_item_number" data-item="vendor_item_number" value="<?=$vendor_item['vendor_item_number']?>">
            </div>
        </div>
        <div class="content-row">
            <div class="itemparamlabel vendorname">Su. Item:</div>
            <div class="itemparamvalue editmode vendorname">
                <input type="text" class="vendordatainpt vendoritemname <?=empty($vendor_item['vendor_item_name']) ? 'missing_info' : ''?>" data-item="vendor_item_name" value="<?=$vendor_item['vendor_item_name']?>">
            </div>
        </div>
        <div class="content-row">
            <div class="itemparamlabel vendosrship">Ships From:</div>
            <div class="itemparamvalue vendorcountry <?=empty($vendor['shipaddr_country']) ? 'missing_info' : ''?>"><?=$vendor['shipaddr_country']?></div>
            <div class="itemparamvalue vendorzip <?=empty($vendor['vendor_zipcode']) ? 'missing_info' :''?>"><?=$vendor['vendor_zipcode']?></div>
            <div class="vendorshipstate"><?=$vendor['shipaddr_state']?></div>
        </div>
        <div class="content-row">
            <div class="itemparamlabel vedorponote">Notes on PO:</div>
            <div class="itemparamlabel vendorinterval">Only Intervals of:</div>
            <div class="itemparamvalue editmode vendorinterval">
                <input type="text" class="itemkeyinfoinput itemminqty" data-item="item_minqty" value="<?=$item['item_minqty']?>"/>
            </div>
            <div class="itemparamvalue vendorponote"><?=$vendor['po_note']?></div>
        </div>
    </div>
</div>