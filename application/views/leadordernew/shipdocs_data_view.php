<div class="shipdocs_txt">Ship Docs:</div>
<div class="shipdocs_icons">
<?php if ($edit==0) : ?>
    <?php if (count($shipdocs) > 0) : ?>
        <div class="shipdocs_link"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <span><?=count($shipdocs)?> files</span></div>
    <?php endif; ?>
<?php else : ?>
    <div class="shipdocs_link"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <span><?=count($shipdocs)?> files</span></div>
<?php endif; ?>
    <div class="shipdocview">
        <div class="shipdocscloseview">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" version="1.1" style="shape-rendering:geometricPrecision;text-rendering:geometricPrecision;image-rendering:optimizeQuality;" viewBox="0 0 847 847" x="0px" y="0px" fill-rule="evenodd" clip-rule="evenodd"><g><path class="btn-closemodal-svg" d="M423 592l-196 196c-110,111 -279,-58 -169,-169l196 -196 -196 -196c-110,-110 59,-279 169,-169l196 196 196 -196c111,-110 280,59 169,169l-196 196 196 196c111,111 -58,280 -169,169l-196 -196z"></path></g></svg>
        </div>
        <div class="datarow">
            <div class="shipdocviewtitle"><?=count($shipdocs)?> ship docs</div>
        </div>
        <div class="shipdocviewarea">
            <?php $numpp = 1;?>
            <?php foreach ($shipdocs as $shipdoc) : ?>
                <div class="datarow">
                    <div class="shipdocnumpp"><?=$numpp?></div>
                    <div class="shipdocviewdoc truncateoverflowtext" data-link="<?=$shipdoc['shipdoc_link']?>"
                         data-source="<?=$shipdoc['shipdoc_src']?>"><?=$shipdoc['shipdoc_src']?></div>
                </div>
                <?php $numpp++;?>
            <?php endforeach; ?>
        </div>
        <?php if ($edit==1) : ?>
            <div class="datarow">
                <div class="shipdocviewadd" id="shipdocviewadd">+ add file</div>
            </div>
        <?php endif; ?>
    </div>
</div>