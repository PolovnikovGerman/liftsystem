<div class="datarow">
    <div class="vendorcategory_title"><?=$vendcnt?> Active Suppliers</div>
    <div class="vendorcategory_showall active">View All Suppliers</div>
</div>
<div class="datarow">
    <div class="vendorcategory_tabledat" id="vendorcategory_tabledat">
        <?php foreach($vendors as $vendor): ?>
            <div class="vendorcategory_vendordat" data-vendor="<?=$vendor['vendor_id']?>">
                <?=$vendor['vendor_name']?> (<?=$vendor['itmcnt']?>)
            </div>
        <?php endforeach; ?>
    </div>
</div>