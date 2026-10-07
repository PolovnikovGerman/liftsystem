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
    <div class="artlogouploadarea">
        <div class="artpopupclosewin" id="artcloseupload">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" version="1.1" style="shape-rendering:geometricPrecision;text-rendering:geometricPrecision;image-rendering:optimizeQuality;" viewBox="0 0 847 847" x="0px" y="0px" fill-rule="evenodd" clip-rule="evenodd"><g><path class="btn-closemodal-svg" d="M423 592l-196 196c-110,111 -279,-58 -169,-169l196 -196 -196 -196c-110,-110 59,-279 169,-169l196 196 196 -196c111,-110 280,59 169,169l-196 196 196 196c111,111 -58,280 -169,169l-196 -196z"></path></g></svg>
        </div>
        <div class="artlogoupload_container">&nbsp;</div>
    </div>
    <div class="artrepeatdataarea">
        <div class="artpopupclosewin" id="artcloserepeat">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" version="1.1" style="shape-rendering:geometricPrecision;text-rendering:geometricPrecision;image-rendering:optimizeQuality;" viewBox="0 0 847 847" x="0px" y="0px" fill-rule="evenodd" clip-rule="evenodd"><g><path class="btn-closemodal-svg" d="M423 592l-196 196c-110,111 -279,-58 -169,-169l196 -196 -196 -196c-110,-110 59,-279 169,-169l196 196 196 -196c111,-110 280,59 169,169l-196 196 196 196c111,111 -58,280 -169,169l-196 -196z"></path></g></svg>
        </div>
        <div class="artrepeat_container">
            <div class="datarow">
                <div class="orderarchive_title">Enter Order #</div>
                <div class="orderarchive_data"><input class="orderarchiveselect" id="archiveord" value=""/></div>
            </div>
            <div class="datarow">
                <div class="orderarchive_save"><img src="/img/artpage/saveticket.png" alt="Save"/></div>
            </div>
        </div>
    </div>
    <!-- div for messages -->
    <div class="artmessagesdataarea">
        <div class="artpopupclosewin" id="artclosemessages">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" version="1.1" style="shape-rendering:geometricPrecision;text-rendering:geometricPrecision;image-rendering:optimizeQuality;" viewBox="0 0 847 847" x="0px" y="0px" fill-rule="evenodd" clip-rule="evenodd"><g><path class="btn-closemodal-svg" d="M423 592l-196 196c-110,111 -279,-58 -169,-169l196 -196 -196 -196c-110,-110 59,-279 169,-169l196 196 196 -196c111,-110 280,59 169,169l-196 196 196 196c111,111 -58,280 -169,169l-196 -196z"></path></g></svg>
        </div>
        <div class="artmessages_container"></div>
    </div>

<?php endif; ?>
<div id="artlocationsarea" class="artlocationsarea">
<?php foreach ($artlocations as $artlocation): ?>
<div class="artapprvl_artbox <?=$edit==1 ? 'edit' : ''?>">
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
</div>