<input type="hidden" id="item_price_id" name="item_price_id" value="<?= $prices['item_price_id'] ?>"/>
<table cellspacing="0" cellpadding="0" align="left" class="pricevalues">
    <tr align="center" >
        <td class="td-text">Price:</td>
        <?php foreach ($price_types as $pricerow) { ?>
            <td class="td-text2">
                <input type="hidden" id="old_item_price_<?=$pricerow['type']?>" value="<?=$prices['item_price_' . $pricerow['type']]?>"/>
                <input type="text" id="item_price_<?=$pricerow['type']?>" name="item_price_<?=$pricerow['type']?>" class="itempriceval" value="<?=$prices['item_price_' . $pricerow['type']]?>"/>
            </td>
        <?php } ?>
        <td class="pricing_empty">&nbsp;</td>
        <td class="td-text2">
            <input type="hidden" id="old_item_price_print" value="<?=$prices['item_price_print']?>"/>
            <input type="text" id="item_price_print" name="item_price_print" class="itempriceval" value="<?=$prices['item_price_print']?>"/>
        </td>
        <td class="td-text2">
            <input type="hidden" id="old_item_price_setup" value="<?=$prices['item_price_setup']?>"/>
            <input type="text" id="item_price_setup" name="item_price_setup" class="itempriceval" value="<?=$prices['item_price_setup']?>"/>
        </td>
    </tr>
    <tr align="center" >
        <td class="td-text">Sale:</td>
        <?php $i = 0; ?>
        <?php foreach ($price_types as $pricerow) { ?>
            <td class="<?= ($i == 0 ? 'td-text2-1' : ($i == $numprice ? 'td-text2-3' : 'td-text2-2')) ?>">
                <input type="hidden" id="old_item_sale_<?=$pricerow['type']?>" value="<?=$prices['item_sale_'.$pricerow['type']]?>"/>
                <input type="text" id="item_sale_<?=$pricerow['type']?>" name="item_sale_<?=$pricerow['type']?>" class="itemsaleval" value="<?=$prices['item_sale_' . $pricerow['type']]?>"/>
            </td>
            <?php $i++; ?>
        <?php } ?>
        <td class="pricing_empty">&nbsp;</td>
        <td class="td-text2-1">
            <input type="hidden" id="old_item_sale_print" value="<?=$prices['item_sale_print']?>"/>
            <input type="text" id="item_sale_print" name="item_sale_print" class="itemsaleval" value="<?=$prices['item_sale_print']?>"/>
        </td>
        <td class="td-text2-3">
            <input type="hidden" id="old_item_sale_setup" value="<?=$prices['item_sale_setup']?>"/>
            <input type="text" id="item_sale_setup" name="item_sale_setup" class="itemsaleval" value="<?=$prices['item_sale_setup']?>"/>
        </td>
    </tr>
</table>
