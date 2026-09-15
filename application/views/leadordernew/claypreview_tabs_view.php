<div class="leadorderclaytab active">
    <div class="claypreviewtab_title">Clay Models:</div>
    <div class="claypreviewtab_subtitle">
        <?php if ($clayhistory==1) : ?>
            Historical View
        <?php else: ?>
            <?php if (count($claydocs['options']) > 0) : ?>
            To Approve
            <?php else: ?>
            No Clay Models
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<div class="leadorderpreviewtab">
    <div class="claypreviewtab_title">Preview Pictures</div>
    <div class="claypreviewtab_subtitle">
        <?php if ($previewhistory==1) : ?>
            Historical View
        <?php else: ?>
            <?php if (count($previews) > 0) : ?>
                To Approve
            <?php else: ?>
                No Preview Pictures
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<div class="leadorderclaycontent active">
    <?php if ($edit==1 && $clayhistory==0) : ?>
        <div class="claymodels_addoptn">+Add<br>Option</div>
    <?php endif; ?>
    <?php if ($clayhistory==1) : ?>
        <?php $this->load->view('leadordernew/claydocs_history_view', ['claydocs' => $claydocs, 'artwork' => $artwork, 'edit' => $edit])?>
    <?php else : ?>
        <?php $this->load->view('leadordernew/claydocs_view', ['claydocs' => $claydocs, 'artwork' => $artwork, 'edit' => $edit])?>
    <?php endif; ?>
</div>
<div class="leadorderpreviewcontent">
    <?php if ($edit==1 && $previewhistory==0) : ?>
        <div class="previewpict_addoptn">+Add<br>Option</div>
    <?php endif; ?>
    <?php if ($previewhistory==1) : ?>
        <?php $this->load->view('leadordernew/previewdocs_history_view', ['previews' => $previews, 'artwork' => $artwork, 'edit' => $edit]);?>
    <?php else: ?>
        <?php $this->load->view('leadordernew/previewdocs_view', ['previews' => $previews, 'artwork' => $artwork, 'edit' => $edit]);?>
    <?php endif; ?>
</div>