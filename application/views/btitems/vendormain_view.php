<div class="relievers_vendormain">
    <div class="sectionlabel">SUPPLIER:</div>
    <div class="vendormainbody">
        <div class="content-row">
            <div class="itemparamlabel vendorname">Supplier</div>
            <div class="itemparamvalue vendorname <?=empty($vendor['vendor_name']) ? 'missing_info' : ''?>"><?=$vendor['vendor_name']?></div>
        </div>
        <div class="content-row">
            <div class="itemparamlabel vendorname">Su. Item #:</div>
            <div class="itemparamvalue vendorname <?=empty($vendor_item['vendor_item_number']) ? 'missing_info' : ''?>"><?=$vendor_item['vendor_item_number']?></div>
        </div>
        <div class="content-row">
            <div class="itemparamlabel vendorname">Su. Item:</div>
            <div class="itemparamvalue vendorname <?=empty($vendor_item['vendor_item_name']) ? 'missing_info' : ''?>"><?=$vendor_item['vendor_item_name']?></div>
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
            <div class="itemparamvalue vendorinterval">12</div>
            <div class="itemparamvalue vendorponote"><?=$vendor['po_note']?></div>
        </div>
    </div>
</div>