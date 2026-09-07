<?php foreach ($artlocations as $artlocation): ?>
<div class="artapprvl_artbox">
    <?php if ($artlocation['art_type'] == 'Logo'): ?>
        <?php $this->load->view('leadordernew/artlocation_logo_view', ['artlocation' => $artlocation, 'edit' => $edit]); ?>
    <?php elseif ($artlocation['art_type'] == 'Text'): ?>
        <?php $this->load->view('leadordernew/artlocation_text_view', ['artlocation' => $artlocation, 'edit' => $edit]); ?>
    <?php elseif ($artlocation['art_type'] == 'Repeat'): ?>
        <?php $this->load->view('leadordernew/artlocation_repeat_view', ['artlocation' => $artlocation, 'edit' => $edit]); ?>
    <?php else: ?>
        <?php $this->load->view('leadordernew/artlocation_reference_view', ['artlocation' => $artlocation, 'edit' => $edit]); ?>
    <?php endif; ?>
</div>
<?php endforeach; ?>
