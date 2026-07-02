<div class="bl_ship_tax_content">            
    <div class="ship_tax_content_line1">
        <div class="viewmultishipdetails text_blue">edit ship details</div>
        <div class="shipdocs_label multyship">Ship Docs:</div>
        <div class="shipdocs_link multyship">
            <i class="fa fa-file-text-o"></i>
            <span><?=count($shipdocs)?> files</span>
        </div>
        <div class="shipdocview">
            <div class="shipdocscloseview">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" version="1.1" style="shape-rendering:geometricPrecision;text-rendering:geometricPrecision;image-rendering:optimizeQuality;" viewBox="0 0 847 847" x="0px" y="0px" fill-rule="evenodd" clip-rule="evenodd"><g><path class="btn-closemodal-svg" d="M423 592l-196 196c-110,111 -279,-58 -169,-169l196 -196 -196 -196c-110,-110 59,-279 169,-169l196 196 196 -196c111,-110 280,59 169,169l-196 196 196 196c111,111 -58,280 -169,169l-196 -196z"></path></g></svg>
            </div>
            <div class="datarow">
                <div class="shipdocviewtitle"><?=count($shipdocs)?> ship docs</div>
            </div>
            <div class="shipdocviewarea"><?=$shipdocs_view?></div>
            <div class="datarow">
                <div class="shipdocviewadd" id="shipdocviewadd">+ add file</div>
            </div>
        </div>
        <div class="shipotherparamsarea multyship">
            <div class="rushselectarea multyship">
                <div class="label">Ships on:</div>
                <div class="rushdataselect" id="rushdatalistarea"><?= $rushview ?></div>
            </div>
            <input type="text" class="shiprushcost input_text_right input_border_black" value="<?=number_format($shipping['rush_price'],2)?>"/>
        </div>
    </div>
    <div class="ship_tax_content_line1">
        <div class="multishipadresslist">
            <?=$shipcostview?>
        </div>        
        <div class="shipdetailsarea">
            <div class="label">Shipping</div>
            <div class="dataarea">
                <input type="text" class="shippingcost input_text_right input_border_black" value="<?=number_format(floatval($order['shipping']),2)?>"/>
            </div>
            <div class="labeltax">Sales Tax</div>
            <div class="dataarea">
                <input type="text" class="salestaxcost input_text_right input_border_black" readonly="readonly" value="<?= MoneyOutput($order['tax']) ?>"/>
            </div>            
        </div>
    </div>
</div>