<div class="leadorderdetailspopupheader">
    <div class="line_1">
        <div id="currentorderheaddataarea">
            <?= $order_head ?>
        </div>
        <div class="block_4 <?=$order_id==0 ? 'neworder' : ''?>">
            <div class="block_4_text text_blue">
                <div class="block_4_text1 hidden">
                    <img src="/img/leadorder/arrow_l.png" width="8" height="10" alt="arrow left"> prev
                </div>
                <img src="/img/leadorder/partition.png" width="1" height="23" alt="partition" style="float: left; margin: 2px 4px;">
                <div class="block_4_text2 hidden">
                    next <img src="/img/leadorder/arrow_r.png" width="8" height="10" alt="arrow left">
                </div>
                <img src="/img/leadorder/partition.png" width="1" height="23" alt="partition" style="float: left; margin: 2px 4px;">
                <div class="block_4_text2 hidden">
                    duplicate <img src="/img/leadorder/arrow_d.png" width="10" height="8" alt="arrow left">
                </div>
                <img src="/img/leadorder/partition.png" width="1" height="23" alt="partition" style="float: left; margin: 2px 4px;">
                <div class="block_4_text2 hidden">send</div>
                <img src="/img/leadorder/partition.png" width="1" height="23" alt="partition"
                     style="float: left; margin: 2px 4px;">
                <div class="block_4_text2 hidden">pdf</div>
            </div>
        </div>
        <?php if ($order_id==0) { ?>
            <div class="placeorderbtn">ORDER</div>
        <?php } else { ?>
            <div class="button_save text_style_1 text_white">
                <div class="button_save_text orderdatasave">save</div>
            </div>
        <?php } ?>
        <?php if ($order_id>0) { ?>
            <div class="timeroutarea"></div>
        <?php } ?>
    </div>
</div>
