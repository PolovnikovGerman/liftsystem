<?php if ($cnt==0) {?>
    <div class="ticketattach_row">No attachments</div>
<?php } else { ?>
    <div class="ticketattach_title">
        <div class="ticketattachactions">&nbsp;</div>    
        <div class="ticketattachdocname">Name</div>
    </div>
    <?php foreach($list as $row) {?>    
        <div class="ticketattach_row">
            <div class="ticketattachactions" id="delatt<?=$row['ticket_doc_id']?>">
                <img src="/img/cancel.png"/>
            </div>        
            <div class="ticketattachdocname">
                <a href="<?=$row['doc_link']?>" target="_blank"><?=$row['doc_name']?></a>        
            </div>
        </div>
    <?php } ?>
<?php } ?>
