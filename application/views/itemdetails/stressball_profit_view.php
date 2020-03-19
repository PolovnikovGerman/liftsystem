<table border="0" cellspacing="0" cellpadding="0" align="left" style=" position:relative; margin: 0 7px 7px; float: left;">
    <tr align="center" id="profitrow">
        <td class="td-text">Profit:</td>
        <?php foreach ($price_types as $pricerow) { ?>
            <td class="td-text2" id="profit_<?= $pricerow['type'] ?>"><b><?= (floatval($prices['profit_' . $pricerow['type']]) != 0 ? '$' . number_format($prices['profit_' . $pricerow['type']], 0, '.', '') : '') ?></b></td>
        <?php } ?>
        <td class="pricing_empty" style="padding-left:6px;">&nbsp;</td>
        <td class="td-text2" id="profit_print"><b><?= (floatval($prices['profit_print']) != 0 ? '$' . number_format($prices['profit_print'], 2, '.', '') : '') ?></b></td>
        <td class="td-text2" id="profit_setup"><b><?= (floatval($prices['profit_setup']) != 0 ? '$' . number_format($prices['profit_setup'], 2, '.', '') : '') ?></b></td>
    </tr>
    <tr align="center" id="profitperc" >
        <td class="td-text">Percent:</td>
        <?php foreach ($price_types as $pricerow) { ?>
            <td id="profit_<?= $pricerow['type'] ?>_perc" class="td-text2 <?= $prices['profit_' . $pricerow['type'] . '_class'] ?>"><?= ($prices['profit_' . $pricerow['type'] . '_class'] != 'empty' ? $prices['profit_' . $pricerow['type'] . '_perc'] . '%' : '') ?></td>
        <?php } ?>
        <td class="pricing_empty" style="padding-left:6px;">&nbsp;</td>
        <td id="profit_print_perc" class="td-text2 <?= $prices['profit_print_class'] ?>"><?= ($prices['profit_print_class'] != 'empty' ? $prices['profit_print_perc'] . '%' : '') ?></td>
        <td id="profit_setup_perc" class="td-text2 <?= $prices['profit_setup_class'] ?>"><?= ($prices['profit_setup_class'] != 'empty' ? $prices['profit_setup_perc'] . '%' : '') ?></td>
    </tr>
</table>
<div class="pricekeyinfo">Key</div>