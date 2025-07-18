<div class="inventorydetails_body_content">
    <input type="hidden" id="invenorynewhistoryadd" value="0"/>
    <div class="inventorydetails_table_title">Inventory Used:</div>
    <div class="inventorydetails_table_head">
        <div class="instock_date">
            <span class="outcomelistadd" data-item="<?=$item['inventory_color_id']?>"><i class="fa fa-plus-circle" aria-hidden="true"></i></span>
            Date
        </div>
        <div class="instock_recnum">Record #</div>
        <div class="instock_descript">Description</div>
        <div class="instock_amount">QTY</div>
        <div class="instock_balance">Balance</div>
    </div>
    <div class="inventorydetails_table_body" id="inventorydetails_table_body">
        <?php $numrow = 0; ?>
        <?php foreach ($lists as $list) { ?>
            <div class="inventorydetails_table_row <?= $numrow % 2 == 0 ? 'greydatarow' : 'whitedatarow' ?>">
                <div class="instock_date"><?= date('m/d/y', $list['date']) ?></div>
                <div class="instock_recnum" data-rectype="<?=$list['rectype']?>" data-order="<?=$list['order']?>">
                    <?=$list['record']?>
                </div>
                <div class="instock_descript"><?=$list['description'] ?></div>
                <div class="instock_amount <?=$list['type']=='O' ? 'negative' : ''?>">
                    <?=$list['type']=='O' ? '(' : ''?><?=QTYOutput($list['amount'])?><?=$list['type']=='O' ? ')' : '' ?>
                </div>
                <div class="instock_balance <?=$list['balance']<0 ? 'negative' : ''?>">
                    <?=$list['balance']<0 ? '(' : ''?><?=QTYOutput(abs($list['balance']))?><?=$list['balance']<0 ? ')' : ''?>
                </div>
            </div>
            <?php $numrow++; ?>
        <?php } ?>
    </div>
    <div class="inventory_oldestdetails">oldest</div>
    <div class="inventory_mostrecent">most recent</div>
    <div class="datarow">
        <div class="inventorydetails_total">In Stock:</div>
        <div class="inventorydetails_total_stock"><?=QTYOutput($balance)?></div>
    </div>
    <div class="datarow">
        <div class="inventorydetails_reseved_title">Reserved: <?=QTYOutput($reservtotal)?></div>
        <div class="inventorydetails_reserved_legend">
            <div class="datarow">
                <div class="reserved_legend_title">Investigate:</div>
            </div>
            <div class="datarow">
                <div class="reserved_legend_title">
                    <span class="legendicon"><img src="/img/masterinvent/red_square.png" alt="Late"/></span>
                    Over 30 Days Late
                </div>
            </div>
            <div class="datarow">
                <div class="reserved_legend_title">
                    <span class="legendicon"><img src="/img/masterinvent/violet_square.png" alt="Shipped"/></span>
                    % Fulfilled != Shipped
                </div>
            </div>
        </div>
    </div>
    <div class="datarow">
        <div class="inventorydetails_reseved_head">
            <div class="shipdate">Ship Date</div>
            <div class="ordernumber">Order #</div>
            <div class="customername">Customer</div>
            <div class="amntval">QTY</div>
            <div class="forecastbal">Forecasted Bal.</div>
            <div class="artapprov">Artwork Approved</div>
            <div class="fullfiledperc">% Fulfilled</div>
            <div class="shippedperc">% Shipped</div>
        </div>
    </div>
    <div class="datarow">
        <div class="inventorydetails_reseved_body" id="inventorydetails_reseved_body">
            <?php $nrow = 0; ?>
            <?php foreach ($reserved as $reserv) : ?>
                <div class="inventoryreseved_table_row <?= $nrow % 2 == 0 ? 'greydatarow' : 'whitedatarow' ?>">
                    <div class="shipdate <?=$reserv['shipdateclass']?>"><?=$reserv['shipdate']?></div>
                    <div class="ordernumber" data-order="<?=$reserv['order_id']?>"><?=$reserv['order']?></div>
                    <div class="customername"><?=$reserv['customer_name']?></div>
                    <div class="amntval"><?=QTYOutput($reserv['reserved'])?></div>
                    <div class="forecastbal"><?=QTYOutput($reserv['forecastbal'])?></div>
                    <div class="artapprov <?=$reserv['approvedclass']?>"><?=$reserv['approved']?></div>
                    <div class="fullfiledperc <?=$reserv['fullfillclass']?>"><?=$reserv['fullfill']?></div>
                    <div class="shippedperc <?=$reserv['shipclass']?>"><?=$reserv['ship']?></div>
                </div>
                <?php $nrow++; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="datarow">
        <div class="inventorydetails_reserved">Available:</div>
        <div class="inventorydetails_total_reserved"><?=QTYOutput($available)?></div>
    </div>

</div>