<div class="vendordetails-body">
    <div class="vendordata-tabs <?=$vendor['vendor_status']==1 ? 'active' : 'inactive'?>">
        <div class="vendor-tab current <?=$editmode==1 ? 'editmode' : ''?>" data-tab="profile">Profile</div>
        <div class="vendor-tab <?=$editmode==1 ? 'editmode' : ''?>" data-tab="login">Vend Login</div>
        <div class="vendor-tab <?=$editmode==1 ? 'editmode' : ''?>" data-tab="history">History</div>
        <div class="vendor-tab <?=$editmode==1 ? 'editmode' : ''?>" data-tab="items">Items</div>
    </div>
    <div class="vendordata-content current <?=$editmode==0 ? ($vendor['vendor_status']==1 ? 'active' : 'inactive') : 'editmode'?>" data-tab="profile">
        <?=$profile_view?>
    </div>
    <div class="vendordata-content <?=$vendor['vendor_status']==1 ? 'active' : 'inactive'?>" data-tab="login">
        <span>Vendor Login</span>
    </div>
    <div class="vendordata-content <?=$vendor['vendor_status']==1 ? 'active' : 'inactive'?>" data-tab="history">
        <span>History</span>
    </div>
    <div class="vendordata-content <?=$vendor['vendor_status']==1 ? 'active' : 'inactive'?>" data-tab="items">
        <span>Items</span>
    </div>
</div>