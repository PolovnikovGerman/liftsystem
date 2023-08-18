<div class="sectionlabel">PRINTING:</div>
<div class="sectionbody">
    <div class="content-row">
        <div class="itemvendorfilebtn <?=empty($item['item_vector_img']) ? '' : 'vectorfile'?>" data-file="<?=$item['item_vector_img']?>">
            Vector AI File
        </div>
        <div class="custommethodarea">
            <div class="content-row">
                <div class="itemparamlabel custommethod">Method:</div>
            </div>
            <div class="content-row">
                <div class="itemparamvalue custommethod"><?=$item['imprint_method']?></div>
            </div>
        </div>
        <div class="customprinrcolorsarea">
            <div class="content-row">
                <div class="itemparamlabel printcolors">Print Colors:</div>
            </div>
            <div class="content-row">
                <div class="itemparamvalue printcolors"><?=$item['imprint_color']?></div>
            </div>
        </div>
    </div>
    <div class="printlocationstable">
        <div class="content-row">
            <div class="locationnametitle">Location Name:</div>
            <div class="locationplacetitle">Print Size:</div>
            <div class="locationviewtitle">View:</div>
        </div>
    </div>
    <div class="printlocationsdata">
        <?=$locations?>
    </div>
</div>
