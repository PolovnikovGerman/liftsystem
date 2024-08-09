<div class="ordercogdetailsviewarea project">
    <div class="projcoglabel">INTERNAL</div>
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
    <div class="itemtitle">
        <div class="labelrow">TOTAL:</div>
        <div class="valuerow">
            <?php foreach ($data['list'] as $item) { ?>
                <div class="datarow"><?=$item['item_qty']?> <?=$item['item_color']?> - <?=$item['item_description']?></div>
            <?php } ?>
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
        <?php if ($edit_mode==0) { ?>
            <div class="placepo active" data-order="<?=$data['order_id']?>">+ Place Outside PO</div>
        <?php } ?>

        <div class="tabledatasection">
            <div class="qty"><?=$data['order_qty']?></div>
            <div class="datadevide">-</div>
            <div class="amnttype">TO PRINT</div>
            <div class="datadevide">-</div>
            <div class="vendor">PRINT SHOP</div>
            <div class="datadevide">-</div>
            <div class="date"><?=date('M j', $data['print_date'])?></div>
            <div class="amntpaymetod">Projected:</div>
            <div class="amountsum"><?=MoneyOutput($data['amount_sum'], 2)?></div>
        </div>
    </div>
    <div class="procentsarea">53.91%</div>
    <!--    <div class="dataarea">-->
    <!--        --><?php //$nrow=0;?>
    <!--        --><?php //foreach ($data['list'] as $row) { ?>
    <!--        <div class="datarow --><?php //=($nrow%2==0 ? 'whitedatarow' : 'greydatarow')?><!--">-->
    <!--            <div class="editamount --><?php //=$edit_mode==1 ? 'actionhide' : ''?><!--" data-amount="--><?php //=$row['amount_id']?><!--"><i class="fa fa-pencil"></i></div>-->
    <!--            <div class="delamount --><?php //=$edit_mode==1 ? 'actionhide' : ''?><!--" data-amount="--><?php //=$row['amount_id']?><!--"><i class="fa fa-trash"></i></div>-->
    <!--            <div class="date">--><?php //=date('m/d/y',$row['amount_date'])?><!--</div>-->
    <!--            <div class="amnttype">--><?php //=($row['printshop']==1 ? 'Print Shop' : 'PO' )?><!--</div>-->
    <!--            <div class="vendor">--><?php //=($row['printshop']==1 ? '&mdash;' : $row['vendor_name'])?><!--</div>-->
    <!--            <div class="amountsum">--><?php //=MoneyOutput($row['amount_sum'], 2)?><!--</div>-->
    <!--        </div>-->
    <!--        --><?php //$nrow++;?><!--        -->
    <!--        --><?php //} ?>
    <!--    </div>-->
    <!--    <div class="procentsarea">-->
    <!--        --><?php //foreach ($data['list'] as $row) { ?>
    <!--            <div class="datarow">--><?php //=number_format($row['proc'],2)?><!--%</div>-->
    <!--        --><?php //} ?>
    <!--    </div>-->
    <div class="totalcogprojarea">
        <div class="totalcoglabel">Total COG:</div>
        <div class="totalcogvalue">$63.30</div>
        <!--        --><?php //=MoneyOutput($data['cog_value'])?>
        <!--        <div class="totalcogperc">--><?php //=number_format($data['cog_proc'],2)?><!--%</div>-->
    </div>
    <div class="datarow">
        <div class="profitperc">31.26%</div>
        <div class="profitprojarea">
            <div class="profitlabel">Profit:</div>
            <div class="profitvalue">$36.53</div>
            <!--        --><?php //=MoneyOutput($data['profit_value'])?>
        </div>
    </div>
    <!--    --><?php //=number_format($data['profit_proc'],2)?><!--    -->
</div>