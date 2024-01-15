<div class="relievers_vendormain">
    <input type="hidden" id="vendor_item_id" value="<?=$vendor_item['vendor_item_id']?>"/>
    <div class="sectionlabel">SUPPLIER:</div>
    <div class="vendormainbody <?=$missinfo==0 ? '' : 'missinginfo'?>">
        <div class="content-row">
            <div class="itemparamlabel vendorname">Supplier</div>
            <div class="itemparamvalue vendorname">
                <select class="vendornameinp <?=empty($vendor_item['vendor_name']) ? 'missing_info' : ''?>" data-item="vendor_item_vendor">
                    <option value=""></option>
                    <?php foreach ($vendors as $vrow) { ?>
                        <option value="<?=$vrow['vendor_id']?>" <?=$vrow['vendor_id']==$vendor_item['vendor_item_vendor'] ? 'selected="selected"' : ''?>><?=$vrow['vendor_name']?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div id="vendoritemdetailsarea"><?=$vendoritem_view?></div>
        <div class="content-row">
            <div class="itemparamlabel vendosrship">Ships From:</div>
            <div class="itemparamvalue editmode vendorcountry">
                <select class="vendoritemcountyinp <?=empty($vendor_item['item_shipcountry']) ? 'missing_info' : ''?>" data-item="item_shipcountry">
                    <option value=""></option>
                    <?php foreach ($countries as $country) { ?>
                        <option value="<?=$country['country_id']?>" <?=$country['country_id']==$vendor_item['item_shipcountry'] ? 'selected="selected"' : ''?>><?=$country['country_iso_code_3']?> <?=$country['country_name']?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="itemparamvalue vendorzip editmode">
                <input type="text" class="vendordatainpt vendoritemname <?=empty($vendor_item['vendor_item_zipcode']) ? 'missing_info' : ''?>" data-item="vendor_item_zipcode" value="<?=$vendor_item['vendor_item_zipcode']?>">
            </div>
            <div class="vendorshipstate"><?=$vendor_item['item_shipstate']?></div>
        </div>
        <div class="content-row">
            <div class="itemparamlabel vedorponote">Notes on PO:</div>
            <div class="itemparamlabel vendorinterval">Only Intervals of:</div>
            <div class="itemparamvalue editmode vendorinterval">
                <input type="text" class="itemkeyinfoinput itemminqty" data-item="item_minqty" value="<?=$item['item_minqty']?>"/>
            </div>
            <div class="itemparamvalue editmode vendorponote">
                <textarea class="vendordatainpt ponotedata" data-item="po_note"><?=$vendor_item['po_note']?></textarea>
            </div>
        </div>
    </div>
</div>