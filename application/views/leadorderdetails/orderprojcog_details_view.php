<div class="ordercogdetailsviewarea project">
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
    <?php foreach ($data['list'] as $item) { ?>
        <div class="itemtitle">
            <div class="labelrow">TOTAL:</div>
            <div class="valuerow">
                <div class="datarow"><?=$item['item_qty']?> <?=$item['item_color']?> - <?=$item['item_description']?></div>
            </div>
        </div>
        <div class="tabledataarea">
            <div class="titletable">
                <div class="qty">QTY</div>
                <div class="date">Date</div>
                <div class="amnttype">Type</div>
                <div class="vendor">Vendor</div>
                <div class="amntpaymetod">Pay Method</div>
                <div class="amountsum">Amount</div>
            </div>
            <?php $details = $item['details'];?>
            <?php foreach ($details as $detail) : ?>
                <div class="tabledatasection <?=$detail['printshop']==1 ? 'printdetails' : 'details'?>">
                    <?php if ($detail['printshop']==1) : ?>
                        <div class="openprintamnt">Open</div>
                    <?php else : ?>
                        <div class="editamount <?=$edit_mode==1 ? 'actionhide' : ''?>" data-amount="<?=$detail['amount_id']?>"><i class="fa fa-pencil"></i></div>
                        <div class="delamount <?=$edit_mode==1 ? 'actionhide' : ''?>" data-amount="<?=$detail['amount_id']?>"><i class="fa fa-trash"></i></div>
                    <?php endif; ?>
                    <div class="qtyamnt"><?=$detail['qty']?></div>
                    <div class="dateamnt"><?=date('m/d/y', $detail['amount_date'])?></div>
                    <div class="typeamnt"><?=$detail['type']?></div>
                    <div class="vendoramnt"><?=$detail['vendor']?></div>
                    <div class="paymetodamnt"><?=$detail['payment_method']?></div>
                    <div class="amountsum"><?=MoneyOutput($detail['amount'], 2)?></div>
                    <div class="profitdataperc"><?=$detail['profit_perc']?>%</div>
                </div>
            <?php endforeach;?>
            <?php if ($edit_mode==0) { ?>
                <div class="placepo active" data-order="<?=$item['order_itemcolor_id']?>">+ Place Outside PO</div>
            <?php } ?>
            <?php $projects = $item['projects'];?>
            <?php foreach ($projects as $project) : ?>
            <div class="tabledatasection">
                <div class="qty"><?=$project['qty']?></div>
                <div class="datadevide">-</div>
                <div class="amnttype">TO PRINT</div>
                <div class="datadevide">-</div>
                <div class="vendor">PRINT SHOP</div>
                <div class="datadevide">-</div>
                <div class="date"><?=date('M j', $data['print_date'])?></div>
                <div class="amntpaymetod">Projected:</div>
                <div class="amountsum"><?=MoneyOutput($project['amount'], 2)?></div>
                <div class="profitdataperc"><?=$project['profit_perc']?>%</div>
            </div>
            <?php if ($data['completed']==1) : ?>
                <div class="amountcompleted">100% Complete</div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php } ?>
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