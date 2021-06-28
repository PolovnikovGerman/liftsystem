<div class="vendordetails-section-header">Documents:</div>
<div class="vendordetails-section-body">
<div class="content-row">
    <div class="vendordocument_value">
        <?php if ($otherdocs==0) { ?>
            <div class="vendorparam_value <?=$editmode==1 ? 'addnewotherdorcs' : ''?>">Empty Documents List</div>
        <?php } else { ?>
            <div class="vendorparam_value documentlist"><?=$otherdocs?> documents - click to view</div>
        <?php } ?>
    </div>
</div>
</div>
