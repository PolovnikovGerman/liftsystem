<table border="0" cellspacing="0" cellpadding="0" align="left" style=" position:relative; margin:8px 0 -3px 4px; float: left;" class="priceqtyvalues">
    <tr align="center">
        <td class="td-text">Quantity:</td>
        <?php $i=0;?>
        <?php foreach ($prices as $row) { ?>
            <td class="td-text3">
                <input type="text" data-idx="<?=$row['promo_price_id']?>" data-fld="item_qty" class="edit_promoqty itempriceval" value="<?=$row['item_qty']?>"/>
            </td>
            <?php $i++;?>
        <?php } ?>
        <td class="pricing_empty" style="width:17px;">&nbsp;</td>
        <td class="td-text3" ><b>Prints</b></td>
        <td class="td-text3-1"><b>Setup</b></td>
    </tr>
</table>

<input type="hidden" id="item_price_id" name="item_price_id" value="<?=$common_prices['item_price_id']?>"/>
<table border="0" cellspacing="0" cellpadding="0" align="left" style=" position:relative; margin: 3px 3px -2px; float: left;" class="pricevalues">
    <tr align="center" >
        <td class="td-text">Price:</td>
        <?php $i=0?>
        <?php foreach ($prices as $row) {?>
            <td class="td-text2">
                <input type="text" data-idx="<?=$row['promo_price_id']?>" data-fld="price" value="<?=$row['price']?>" class="itempriceval"/>
            </td>
            <?php $i++?>
        <?php } ?>
        <td class="pricing_empty">&nbsp;</td>
        <td class="td-text2">
            <input type="text" data-idx="<?=$common_prices['item_price_id']?>" data-fld="item_price_print" value="<?=$common_prices['item_price_print']?>" class="itempriceval"/>
        </td>
        <td class="td-text2">
            <input type="text" data-idx="<?=$common_prices['item_price_id']?>" data-fld="item_price_setup" value="<?=$common_prices['item_price_setup']?>" class="itempriceval"/>
        </td>
    </tr>
    <tr align="center" >
        <td class="td-text">Sale:</td>
        <?php $i=0;?>
        <?php foreach ($prices as $row) { ?>
            <td class="td-text2-<?=($i==0 ? '1' : ($i==$numprices ? '3' : '2'))?>">
                <input type="text" data-idx="<?=$row['promo_price_id']?>" data-fld="sale_price" value="<?=$row['sale_price']?>" class="itempriceval"/>
                <?php $i++?>
            </td>
        <?php } ?>
        <td class="pricing_empty">&nbsp;</td>
        <td class="td-text2-1">
            <input type="text" data-idx="<?=$common_prices['item_price_id']?>" data-fld="item_sale_print" value="<?=$common_prices['item_sale_print']?>" class="itempriceval"/>
        </td>
        <td class="td-text2-3">
            <input type="text" data-idx="<?=$common_prices['item_price_id']?>" data-fld="item_sale_setup" value="<?=$common_prices['item_sale_setup']?>" class="itempriceval"/>
        </td>
    </tr>
</table>
