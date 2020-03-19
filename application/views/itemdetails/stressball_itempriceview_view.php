<table cellspacing="0" cellpadding="0" align="left" class="pricevalues">
    <tr align="center" >
        <td class="td-text">Price:</td>
        <?php foreach ($price_types as $pricerow) { ?>
            <td class="td-text2"><b><?= (floatval($prices['item_price_' . $pricerow['type']]) == 0 ? '' : '$' . number_format($prices['item_price_' . $pricerow['type']], 2, '.', '')) ?></b></td>
        <?php } ?>
        <td class="pricing_empty">&nbsp;</td>
        <td class="td-text2"><b><?= (floatval($prices['item_price_print']) != 0 ? '$' . number_format($prices['item_price_print'], 2, '.', '') : '') ?></b></td>
        <td class="td-text2"><b><?= (floatval($prices['item_price_setup']) != 0 ? '$' . number_format($prices['item_price_setup'], 2, '.', '') : '') ?></b></td>
    </tr>
    <tr align="center" >
        <td class="td-text">Sale:</td>
        <?php $i = 0; ?>
        <?php foreach ($price_types as $pricerow) { ?>
            <td class="<?= ($i == 0 ? 'td-text2-1' : ($i == $numprice ? 'td-text2-3' : 'td-text2-2')) ?>"><b><?= (floatval($prices['item_sale_' . $pricerow['type']]) == 0 ? '' : '$' . number_format($prices['item_sale_' . $pricerow['type']], 2, '.', '')) ?></b></td>
            <?php $i++; ?>
        <?php } ?>
        <td class="pricing_empty">&nbsp;</td>
        <td class="td-text2-1"><b><?= (floatval($prices['item_sale_print']) != 0 ? '$' . $prices['item_sale_print'] : '') ?></b></td>
        <td class="td-text2-3"><b><?= (floatval($prices['item_sale_setup']) != 0 ? '$' . $prices['item_sale_setup'] : '') ?></b></td>
    </tr>
</table>
