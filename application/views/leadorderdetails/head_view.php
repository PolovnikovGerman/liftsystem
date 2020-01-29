<div class="leadorderdetailspopupheader">
    <div class="line_1">
        <div id="currentorderheaddataarea">
            <?=$order_head?>
        </div>
        <div class="block_4">
            <div class="block_4_text text_blue">
                <div class="block_4_text1 moveprvorder <?=($prvorder==0 ? 'hidden' : 'active')?>" data-order="<?=$prvorder?>">
                    <img src="/img/leadorder/arrow_l.png" width="8" height="10" alt="arrow left"> prev
                </div>
                <img src="/img/leadorder/partition.png" width="1" height="23" alt="partition" style="float: left; margin: 2px 4px;">
                <div class="block_4_text2 movenxtorder <?=($nxtorder==0 ? 'hidden' :'active' )?>" data-order="<?=$nxtorder?>">
                    next <img src="/img/leadorder/arrow_r.png" width="8" height="10" alt="arrow left">
                </div>
                <img src="/img/leadorder/partition.png" width="1" height="23" alt="partition" style="float: left; margin: 2px 4px;">
                <div class="block_4_text2 active dublicateorder" data-order="<?=$order_dublcnum?>">
                    duplicate <img src="/img/leadorder/arrow_d.png" width="10" height="8" alt="arrow left">
                </div>
                <img src="/img/leadorder/partition.png" width="1" height="23" alt="partition" style="float: left; margin: 2px 4px;">
                <div class="block_4_text2 sendorder <?=$order_system=='old' ? 'hidden' : 'active'?>">send</div>
                <img src="/img/leadorder/partition.png" width="1" height="23" alt="partition" style="float: left; margin: 2px 4px;">
                <div class="block_4_text2 pdfprintorder <?=$order_system=='old' ? 'hidden' : 'active'?>">pdf</div>
            </div>
        </div>
        <div id="editbuttonarea">
            <?php if ($unlocked==1) { ?>
                <div class="button_edit text_style_4 text_white">
                    <div class="button_edit_text">edit</div>
                </div>
            <?php } else { ?>
                <?=$editbtnview?>
            <?php } ?>
        </div>
    </div>
</div>
