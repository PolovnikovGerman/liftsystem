<?php $numpp=0;?>
<?php foreach ($logos as $row) {?>
    <div class="datarow redraw_row <?=($numpp%2==0 ? 'whitedatarow' : 'greydatarow')?>" id="redraw<?=$row['artwork_logo_id']?>">
        <div class="redodata"><?=$row['redo']?></div>
        <div class="rushdata">
            <?php if ($row['rush']==0) { ?>
                &nbsp;
            <?php } else { ?>
                <i class="fa fa-star"></i>
            <?php } ?>
        </div>
        <div class="timedata"><?=$row['diff']?></div>
        <div class="proofdata"><?=$row['proof_num']?></div>
        <div class="orderdat"><?=$row['order_num']?></div>
        <?php if ($row['imagesourceclass']=='imagesourceview') { ?>
            <div class="filesourcedata imagesourceview" data-event="hover" data-timer = 5000 data-css="weekbrandtotals" data-bgcolor="#000000" data-bordercolor="#adadad" data-textcolor="#FFFFFF"
                 data-position="right" data-balloon="<img src='<?=$row['redrawsource']?>' alt='Preview' style='width:250px; height: auto'/>">
        <?php } else { ?>
            <div class="filesourcedata" data-redrawid="<?=$row['artwork_logo_id']?>" data-redrawsource="<?=$row['redrawsource']?>">
        <?php } ?>
            <?=$row['filename']?>
        </div>
        <div class="vectchkdata">
            <?php if ($row['filename']=='') { ?>&nbsp;<?php } else {?>
                <input type="checkbox" class="markasvector" id="vectchk<?=$row['artwork_logo_id']?>" name="vectchk<?=$row['artwork_logo_id']?>" value="<?=$row['artwork_logo_id']?>" />
            <?php } ?>
        </div>
        <?php if ($row['user_txt']==0) { ?>
            <div class="usrtextdata">&nbsp;</div>
        <?php } else { ?>
            <div class="usrtextdata" data-event="hover" data-css="redrawnessageballonarea" data-bgcolor="#FFFFFF"
                data-bordercolor="#000" data-position="right" data-textcolor="#000" data-balloon="<?=$row['user_title']?>" data-timer="6000">
                <i class="fa fa-file-text-o" aria-hidden="true"></i>
            </div>
        <?php } ?>

        <div class="messagedata"><?=$row['redraw_message']?></div>
        <?php if ($row['message_details']==0) { ?>
            <div class="messagedetails">&nbsp;</div>
        <?php } else { ?>
            <div class="messagedetails" data-event="hover" data-css="redrawnessageballonarea" data-bgcolor="#FFFFFF"
                data-bordercolor="#000" data-position="left" data-textcolor="#000" data-balloon="<?=$row['redraw_message']?>" data-timer="6000">
                <i class="fa fa-search" aria-hidden="true"></i>
            </div>
        <?php } ?>
        <div class="submitdata" data-logo="<?=$row['artwork_logo_id']?>">
            <img src="/img/redraw/upload_vector_btn.png" alt="upload"/>
        </div>
        <?php $numpp++;?>
    </div>
<?php } ?>
<?php for ($i=$numpp; $i<25; $i++) { ?>
    <div class="datarow redraw_row <?=($i%2==0 ?  'whitedatarow' :'greydatarow')?>">
        &nbsp;
    </div>
<?php } ?>