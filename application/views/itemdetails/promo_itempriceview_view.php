<table border="0" cellspacing="0" cellpadding="0" align="left" style=" position:relative; margin:8px 0 -3px 4px; float: left;" class="priceqtyvalues">
    <tr align="center">
        <td class="td-text">Quantity:</td>
        <?php $i=0;?>
        <?php foreach ($prices as $row) { ?>
            <td class="td-text3"><?=(intval($row['item_qty'])==0 ? '' : $row['item_qty'])?></td>
            <?php $i++;?>
        <?php } ?>
        <td class="pricing_empty" style="width:17px;">&nbsp;</td>
        <td class="td-text3" ><b>Prints</b></td>
        <td class="td-text3-1"><b>Setup</b></td>
    </tr>
</table>
<img style="padding-left:20px;" src="/img/itemdetails/line-pricing.png"/>
<table border="0" cellspacing="0" cellpadding="0" align="left" style=" position:relative; margin: 3px 3px -2px; float: left;" class="pricevalues">
    <tr align="center" >
        <td class="td-text">Price:</td>
        <?php $i=0?>
        <?php foreach ($prices as $row) {?>
            <td class="td-text2"><?=(floatval($row['price'])==0 ? '' : '$'.number_format($row['price'],2,'.',''))?></td>
            <?php $i++?>
        <?php } ?>
        <td class="pricing_empty">&nbsp;</td>
        <td class="td-text2"><?=(floatval($common_prices['item_price_print'])==0 ? '' : '$'.number_format($common_prices['item_price_print'],2,'.',''))?></td>
        <td class="td-text2"><?=(floatval($common_prices['item_price_setup'])==0 ? '' : '$'.number_format($common_prices['item_price_setup'],2,'.',''))?></td>
    </tr>
    <tr align="center" >
        <td class="td-text">Sale:</td>
        <?php $i=0;?>
        <?php foreach ($prices as $row) { ?>
            <td class="td-text2-<?=($i==0 ? '1' : ($i==$numprices ? '3' : '2'))?>"><?=(floatval($row['sale_price'])==0 ? '' : '$'.number_format($row['sale_price'],2,'.',''))?></td>
            <?php $i++?>
        <?php } ?>
        <td class="pricing_empty">&nbsp;</td>
        <td class="td-text2-1"><?=(floatval($common_prices['item_sale_print'])==0 ? '' : '$'.number_format($common_prices['item_sale_print'],2,'.',''))?></td>
        <td class="td-text2-3"><?=(floatval($common_prices['item_sale_setup'])==0 ? '' : '$'.number_format($common_prices['item_sale_setup'],2,'.',''))?></td>
    </tr>
</table>
