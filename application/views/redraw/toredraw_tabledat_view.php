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
        <div class="filesourcedata <?=$row['imagesourceclass']?>" data-redrawid="<?=$row['artwork_logo_id']?>" data-redrawsource="<?=$row['redrawsource']?>"><?=$row['filename']?></div>
        <div class="vectchkdata">
            <?php if ($row['filename']=='') { ?>&nbsp;<?php } else {?>
                <input type="checkbox" class="markasvector" id="vectchk<?=$row['artwork_logo_id']?>" name="vectchk<?=$row['artwork_logo_id']?>" value="<?=$row['artwork_logo_id']?>" />
            <?php } ?>
        </div>
        <div class="usrtextdata <?=$row['usrtext_class']?>" title="<?=$row['user_title']?>">
            <?php if ($row['user_txt']==0) { ?>
                &nbsp;
            <?php } else { ?>
                <i class="fa fa-file-text-o" aria-hidden="true"></i>
            <?php } ?>
        </div>
        <div class="messagedata"><?=$row['redraw_message']?></div>
        <div class="messagedetails <?=$row['message_class']?>" data-redraw="/redraw/redrawmsg?msg=<?=$row['artwork_logo_id']?>">
            <?php if ($row['message_details']==0) { ?>
                &nbsp;
            <?php } else { ?>
                <i class="fa fa-search" aria-hidden="true"></i>
            <?php } ?>
        </div>
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