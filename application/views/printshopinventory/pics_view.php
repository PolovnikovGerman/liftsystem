<form id="picsaddform">
    <div class="pics_attachments">
        <div class="pics_attachments_dat">
            <div id="inventoryattachlists">
                <?php /*if ($cnt==0) {*/?><!--
                    <div class="picsattach_row">No attachments</div>
                <?php /*} else { */?>
                    <div class="picsattach_title">
                        <div class="picsattachactions">&nbsp;</div>
                        <div class="picsattachdocname">Name</div>
                    </div>
                    <?php /*foreach($list as $row) {*/?>
                        <div class="picsattach_row">
                            <div class="picsattachactions" id="delatt<?/*=$row['ticket_doc_id']*/?>">
                                <img src="/img/cancel.png"/>
                            </div>
                            <div class="picsattachdocname">
                                <a href="<?/*=$row['doc_link']*/?>" target="_blank"><?/*=$row['doc_name']*/?></a>
                            </div>
                        </div>
                    <?php /*} */?>
                --><?php /*} */?>
            </div>
            <div class="clear"></div>
            <div id="file-invuploader"></div>
        </div>
        <div class="savepics">
            <a class="savepicsdat" href="javascript:void(0);">
                <img src="/img/saveticket.png" alt="Save"/>
            </a>
        </div>
    </div>
</form>
