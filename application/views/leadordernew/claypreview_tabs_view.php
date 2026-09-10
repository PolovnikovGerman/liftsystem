<div class="leadorderclaytab active">
    <div class="claypreviewtab_title">Clay Models:</div>
    <div class="claypreviewtab_subtitle">
        <?php if (count($claydocs) > 0) : ?>
        To Approve
        <?php else: ?>
        No Clay Models
        <?php endif; ?>
    </div>
</div>
<div class="leadorderpreviewtab">
    <div class="claypreviewtab_title">Preview Pictures</div>
    <div class="claypreviewtab_subtitle">
        <?php if (count($previews) > 0) : ?>
            To Approve
        <?php else: ?>
            No Preview Pictures
        <?php endif; ?>
    </div>
</div>
<div class="leadorderclaycontent active">
    <?php if ($edit==1) : ?>
        <div class="claymodels_addoptn">+Add<br>Option</div>
    <?php endif; ?>
    <?php $this->load->view('leadordernew/claydocs_view', ['claydocs' => $claydocs, 'artwork' => $artwork, 'edit' => $edit])?>
</div>
<div class="leadorderpreviewcontent">
    <?php if ($edit==1) : ?>
        <div class="previewpict_addoptn">+Add<br>Option</div>
    <?php endif; ?>
    <?php $this->load->view('leadordernew/previewdocs_view', ['previews' => $previews, 'artwork' => $artwork, 'edit' => $edit]);?>
</div>