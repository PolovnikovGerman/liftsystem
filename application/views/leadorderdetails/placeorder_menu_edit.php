<div class="leadorderdetailspopup">
    <input type="hidden" id="orderdataid" value="<?=$order_id?>"/>
    <input type="hidden" id="ordersession" value="<?=$leadsession?>"/>
    <input type="hidden" id="callpage" value="<?=$current_page?>"/>
    <div class="line_1">
        <div class="block_1 text_style_2">
            <div class="block_1_text">
                <input type="text" readonly="readonly" class="calendarinpt" id="order_date" value="<?=date('D - M j, Y',$order_date)?>" data-order="<?=date('Y,m,d',$order_date)?>"/>
            </div>
        </div>
        <div class="block_2">
            <div class="block_2_text">
                <div class="block_2_text1 text_gray">order:</div>
                <div class="block_2_text2 text_style_3 text_bold"><?=$order_num?></div>
                <div class="block_2_text3 text_gray <?=(empty($order_confirmation) ? '' :'text_bold')?>">
                    <?=((empty($order_confirmation) && $order_id>0) ? 'historical' : $order_confirmation)?>
                </div>
            </div>
        </div>
        <div class="block_3">
            <div class="block_3_text">
                <div class="block_2_text1 text_gray">customer:</div>
                <input type="text" class="block_3_input input_border_black inputleadorddata" data-entity="order" data-field="customer_name" value="<?=$customer_name?>"/>
            </div>
        </div>
        <div class="block_4">
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
                <img src="/img/leadorder/partition.png" width="1" height="23" alt="partition" style="float: left; margin: 2px 4px;">
                <div class="block_4_text2 hidden">pdf</div>
            </div>
        </div>
        <!-- UI button -->
        <div class="placeorderbtn">ORDER</div>
    </div>
    <div id="currentorderdataarea">
        <?=$order_data?>
    </div>    
</div>
