<div class="itemdetails_inprintinfo">
    <div class="chapterlabel rightpart">Print Areas:</div>
    <div class="inprintarea">
        <div class="content-row">
            <div class="implintdatalabel cansell">Can Sell:</div>
            <div class="implintdatavalue sellopt" data-item="sellblank">
                <?php if ($item['sellblank']==0) { ?>
                    <i class="fa fa-square-o" aria-hidden="true"></i>
                <?php } else { ?>
                    <i class="fa fa-check-square-o" aria-hidden="true"></i>
                <?php } ?>
            </div>
            <div class="implintdatalabel selloptions">Blank</div>
            <div class="implintdatavalue sellopt" data-item="sellcolor">
                <?php if ($item['sellcolor']==0) { ?>
                    <i class="fa fa-square-o" aria-hidden="true"></i>
                <?php } else { ?>
                    <i class="fa fa-check-square-o" aria-hidden="true"></i>
                <?php } ?>
            </div>
            <div class="implintdatalabel selloptions">1 Color</div>
            <div class="implintdatavalue sellopt" data-item="sellcolors">
                <?php if ($item['sellcolors']==0) { ?>
                    <i class="fa fa-square-o" aria-hidden="true"></i>
                <?php } else { ?>
                    <i class="fa fa-check-square-o" aria-hidden="true"></i>
                <?php } ?>
            </div>
            <div class="implintdatalabel selloptions">2 Colors</div>
            <?php if ($editmode==1) { ?>
                <div class="newimprintloaction">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
                    <span>Add Location</span>
                </div>
            <?php } ?>
        </div>
        <div class="content-row">
            <div class="imprintheader">
                <div class="inprintheadname">Location Name</div>
                <div class="inprintheadsize">Imprint Size</div>
                <div class="inprintheadview">View</div>
            </div>
            <div class="imprintcontent"><?=$inpritdata?></div>
        </div>
        <div class="content-row">
            <div class="implintdatalabel vectorfile">Vector File: </div>
            <div class="implintdatavalue vectorfile" data-link="<?=$item['item_vector_img']?>">click to open</div>
        </div>
    </div>
</div>