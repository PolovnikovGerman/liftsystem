<div class="ordercogdetailsviewarea <?=$data['cogclass']?>">
    <div class="ordercogdetailsviewclose">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" version="1.1" style="shape-rendering:geometricPrecision;text-rendering:geometricPrecision;image-rendering:optimizeQuality;" viewBox="0 0 847 847" x="0px" y="0px" fill-rule="evenodd" clip-rule="evenodd"><g><path class="btn-closemodal-svg" d="M423 592l-196 196c-110,111 -279,-58 -169,-169l196 -196 -196 -196c-110,-110 59,-279 169,-169l196 196 196 -196c111,-110 280,59 169,169l-196 196 196 196c111,111 -58,280 -169,169l-196 -196z"></path></g></svg>
    </div>

    <div class="projcoglabel"><?=$data['itemtype']?></div>
    <div class="revenueareaproj">
        <div class="revenuetitle">Total Revenue:</div>
        <div class="revenueval"><?=MoneyOutput($data['revenue'])?></div>
    </div>
    <div class="datarow">
        <div class="costspercareaproj">
            <?php foreach ($data['costs'] as $cost) { ?>
                <div class="datarow"><?=number_format($cost['proc'],2)?>%</div>
            <?php } ?>
        </div>
        <div class="costsareaproj">
            <?php foreach ($data['costs'] as $cost) { ?>
                <div class="datarow">
                    <div class="costlabel"><?=$cost['label']?>:</div>
                    <div class="costvalue"><?=MoneyOutput($cost['value'])?></div>
                </div>
            <?php } ?>
        </div>
    </div>
    <div id="orderprojcogarea" style="max-height: 460px; overflow-y: auto;">
    <?php foreach ($data['list'] as $item) : ?>
        <div class="itemtitle">
            <div class="labelrow">TOTAL:</div>
            <div class="valuerow">
                <div class="datarow"><?=$item['item_qty']?> <?=$item['item_color']?> - <?=$item['item_description']?></div>
            </div>
        </div>
        <div class="tabledataarea">
            <div class="titletable">
                <div class="qty">QTY</div>
                <div class="price">Price</div>
                <div class="date">Date</div>
                <div class="amnttype">Type</div>
                <div class="vendor">Vendor</div>
                <div class="amntpaymetod">Pay Method</div>
                <div class="amountsum">Amount</div>
                <div class="inclship">
                    Incl Shipping
                    <i class="fa fa-question-circle" data-event="hover" data-css="coghelpballonbox" data-bgcolor="#FFFFFF" data-bordercolor="#000" data-position="up" data-textcolor="#000"
                                     data-balloon="When this is checked profit is calculated using the shipping from the order.  When this is is unchecked it means you should add a PO that covers the shipping cost.  The checkbox is PER order so unchecking will uncheck on all POs for this order"
                       data-timer="8000" data-delay="1000" aria-hidden="true">
                    </i>
                </div>
                <div class="actions">&nbsp;</div>
            </div>
            <div class="tabledetailsitem" data-order="<?=$item['order_itemcolor_id']?>"><?=$item['detailsview']?></div>
            <?php if ($item['totalamnt'] !== 0) : ?>
                <div class="tabledetailstotalamnt"><?=MoneyOutput($item['totalamnt'])?></div>
            <?php endif; ?>
            <?php if ($edit_mode==0) { ?>
                <div class="placepo active" data-order="<?=$item['order_itemcolor_id']?>">+ Place Outside PO</div>
            <?php } ?>
            <?php $projects = $item['projects'];?>
            <?php foreach ($projects as $project) : ?>
            <div class="tabledatasection">
                <div class="qty"><?=$project['qty']?></div>
                <div class="price">-</div>
                <div class="date"><?=date('M j', $data['print_date'])?></div>
                <div class="amnttype">-</div>
                <div class="vendor">PRINT SHOP</div>
                <div class="amntpaymetod">Projected:</div>
                <div class="amountsum"><?=MoneyOutput($project['amount'], 2)?></div>
                <div class="inclship">&nbsp;</div>
                <div class="profitdataperc"><?=$project['profit_perc']?>%</div>
                <div class="actions">To Print:</div>
            </div>
            <?php if ($data['completed']==1) : ?>
                <div class="amountcompleted">100% Complete</div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
    </div>

    <div class="totalcogprojarea">
        <div class="totalcoglabel">Total COG:</div>
        <div class="totalcogvalue"><?=MoneyOutput($data['cog_value'])?></div>
    </div>
    <div class="datarow">
        <div class="profitperc"><?=$data['profit_proc']?>%</div>
        <div class="profitprojarea">
            <div class="profitlabel">Profit:</div>
            <div class="profitvalue"><?=MoneyOutput($data['profit_value'])?></div>
        </div>
    </div>
</div>