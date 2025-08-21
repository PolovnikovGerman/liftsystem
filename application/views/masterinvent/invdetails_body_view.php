<div class="inventorydetails_body_content">
    <input type="hidden" id="invenorynewhistoryadd" value="0"/>
    <input type="hidden" id="hidelate" value="<?=$hidelate?>"/>
    <input type="hidden" id="hideproof" value="<?=$hideproof?>"/>
    <input type="hidden" id="hidefullfil" value="<?=$hidefullfil?>"/>
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
        <div class="inventorydetails_reseved_title">Reserved: <span><?=QTYOutput($reservtotal)?></span></div>
        <div class="inventorydetails_reserved_legend">
            <div class="datarow">
                <div class="inventory_reserv_view latetime">
                    <span class="inventory_reserv_check" data-item="<?=$item['inventory_color_id']?>">
                        <?php if ($hidelate==0) : ?>
                            <i class="fa fa-square"></i>
                        <?php else: ?>
                            <i class="fa fa-check-square"></i>
                        <?php endif; ?>
                    </span>
                    Hide
                </div>
                <div class="inventory_reserv_view artproof">
                    <span class="inventory_reserv_check" data-item="<?=$item['inventory_color_id']?>">
                        <?php if ($hideproof==0) : ?>
                            <i class="fa fa-square"></i>
                        <?php else: ?>
                            <i class="fa fa-check-square"></i>
                        <?php endif; ?>
                    </span>
                    Hide
                </div>
                <div class="inventory_reserv_view fulfillmnt">
                    <span class="inventory_reserv_check" data-item="<?=$item['inventory_color_id']?>">
                        <?php if ($hidefullfil==0) : ?>
                            <i class="fa fa-square"></i>
                        <?php else: ?>
                            <i class="fa fa-check-square"></i>
                        <?php endif; ?>
                    </span>
                    Hide
                </div>
            </div>
            <div class="datarow">
                <div class="reserved_legend_title">
                    <span class="legendicon"><img src="/img/masterinvent/red_square.png" alt="Late"/></span>
                    Over 60 Days Late
                </div>
                <div class="reserved_legend_title">
                    <span class="legendicon"><img src="/img/masterinvent/yellow_square.png" alt="Proof"/></span>
                    Proof Not Approved
                </div>
                <div class="reserved_legend_title" data-event="hover" data-css="itemdetailsballonarea" data-bgcolor="#FFFFFF"
                     data-bordercolor="#000" data-position="left" data-textcolor="#000" data-balloon="Hides the % Fulfilled minus % Shipped" data-timer="6000" data-delay="1000">
                    <span class="legendicon"><img src="/img/masterinvent/violet_square.png" alt="Fulfillment"/></span>
                    % Fulfilled ≠ Shipped
                </div>
            </div>
        </div>
    </div>
    <div class="datarow">
        <div class="inventorydetails_reseved_head">
            <div class="shipdate">Ship Date</div>
            <div class="ordernumber">Order #</div>
            <div class="customername">Customer</div>
            <div class="amntval">Qty to Fulfill</div>
            <div class="forecastbal">Forecasted Bal.</div>
            <div class="artapprov">Art Approved</div>
            <div class="fullfiledperc">% Fulfilled</div>
            <div class="shippedperc">% Shipped</div>
        </div>
    </div>
    <div class="datarow">
        <div class="inventorydetails_reseved_body" id="inventorydetails_reseved_body"><?=$reservview?></div>
    </div>
    <div class="datarow">
        <div class="inventorydetails_reserved">Available:</div>
        <div class="inventorydetails_total_reserved"><?=QTYOutput($available)?></div>
    </div>

</div>