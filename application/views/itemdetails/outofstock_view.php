<div class="itemoutstock_label">Out of Stock</div>
<div class="itemoutstock_value">
    <input type="checkbox" disabled="disabled" <?= $outstock == 1 ? 'checked' : '' ?>/>
</div>
<?php if ($outstock == 1) { ?>
    <div class="itemoutstock_link">View Banner</div>
<?php } ?>
