<div class="uploadinvoicedocarea">    
    <div class="title">Order <?=$order_num?> Upload Invoice</div>
    <div class="uploadlist">
        <?php if (!empty($invoice_doc)) { ?>
        <div class="invoicefilesrcname">Order # <?=$order_num?> Invoice</div>
        <div class="invoicefilesrcnameremove">
            <img src="/img/close.png" alt="Remove"/>
        </div>        
        <?php } else { ?>
        &nbsp;
        <?php } ?>
    </div>    
    <div id="file-uploader">

    </div>
    <div class="invdocumentsave">
        <img src="/img/saveticket.png"/>
    </div>
</div>