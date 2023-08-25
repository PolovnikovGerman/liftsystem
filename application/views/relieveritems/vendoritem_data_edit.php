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
