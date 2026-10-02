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
<?php if ($edit == 1): ?>
    <div class="art_line1" id="newartbuttonareaview">
        <div class="button_newart">
            <div class="button_newart_text">+ New Art</div>
            <select class="art_select input_border_gray" id="arttypechoice">
                <option value="Logo">Logo</option>
                <option value="Text">Text</option>
                <option value="Repeat">Repeat</option>
                <option value="Reference">Reference</option>
            </select>
        </div>
    </div>
<?php endif; ?>
