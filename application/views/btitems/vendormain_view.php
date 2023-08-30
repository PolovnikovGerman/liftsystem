<div class="relievers_vendormain">
    <div class="sectionlabel">SUPPLIER:</div>
    <div class="vendormainbody <?=$missinfo==0 ? '' :  'missinginfo'?>">
        <div class="content-row">
            <div class="itemparamlabel vendorname">Supplier</div>
            <div class="itemparamvalue vendorname <?=empty($vendor_item['vendor_name']) ? 'missing_info' : ''?>"><?=$vendor_item['vendor_name']?></div>
        </div>
        <div id="vendoritemdetailsarea"><?=$vendoritem_view?></div>
        <div class="content-row">
            <div class="itemparamlabel vendosrship">Ships From:</div>
            <div class="itemparamvalue vendorcountry <?=empty($vendor_item['item_shipcountry']) ? 'missing_info' : ''?>"><?=$vendor_item['item_shipcountry_name']?></div>
            <div class="itemparamvalue vendorzip <?=empty($vendor_item['vendor_item_zipcode']) ? 'missing_info' :''?>"><?=$vendor_item['vendor_item_zipcode']?></div>
            <div class="vendorshipstate"><?=$vendor_item['item_shipstate']?></div>
        </div>
        <div class="content-row">
            <div class="itemparamlabel vedorponote">Notes on PO:</div>
            <div class="itemparamlabel vendorinterval">Only Intervals of:</div>
            <div class="itemparamvalue vendorinterval"><?=$item['item_minqty']?></div>
            <div class="itemparamvalue vendorponote"><?=$vendor_item['po_note']?></div>
        </div>
    </div>
</div>