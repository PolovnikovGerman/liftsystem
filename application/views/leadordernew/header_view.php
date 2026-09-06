<input type="hidden" id="orderdataid" value="<?=$order_id?>"/>
<input type="hidden" id="unlockedrec" value="<?=$unlocked?>"/>
<input type="hidden" id="ordersession" value="<?=$leadsession?>"/>
<input type="hidden" id="currentpage" value="<?=$callpage?>"/>
<div class="neworder_header">
    <div class="neworder_close">
        <span aria-hidden="true">×</span>
    </div>
    <div class="namecustomer">
        <div class="namecustomer_title">Customer:</div>
        <div class="namecustomer_box"><?=$customer?></div>
    </div>
</div>
<div class="neworder_whiteline">
    <div class="whiteline_title">ORDER</div>
    <div class="whiteline_datebox"><?=date('D - M j, Y', $order_date)?></div>
    <div class="whiteline_ordnumberbox">
        <div class="ordnumberbox_title">order:</div>
        <div class="ordnumberbox_number"><?=$order_num?></div>
        <div class="ordnumberbox_code"><?=$order_confirm?></div>
    </div>
    <div class="whiteline_btns">
        <div class="btnsbox">
            <div class="btnsbox-button prevorder <?=!empty($prvorder) ? '' : 'unactive'?>" data-order="<?=$prvorder?>"><span><i class="fa fa-caret-left" aria-hidden="true"></i></span>prev</div>
            <div class="btnsbox-button nxtorder" <?=!empty($nxtorder) ? '' : 'unactive'?> data-order="<?=$nxtorder?>">next <span><i class="fa fa-caret-right" aria-hidden="true"></i></span></div>
            <div class="btnsbox-button unactive duplicateorder" data-order="<?=$order_id?>">duplicate <span><i class="fa fa-caret-up" aria-hidden="true"></i></span></div>
            <div class="btnsbox-button sendpdf" data-order="<?=$order_id?>">send</div>
            <div class="btnsbox-button viewpdf" data-order="<?=$order_id?>">pdf</div>
        </div>
        <div class="btnsbox-btnedit" data-order="<?=$order_id?>">edit</div>
    </div>
</div>
