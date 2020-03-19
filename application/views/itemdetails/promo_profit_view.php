<table border="0" cellspacing="0" cellpadding="0" align="left" style=" position:relative; margin: 0 7px 7px; float: left;">
    <tr align="center" id="profitrow">
        <td class="td-text">Profit:</td>
        <?php $i=0;?>
        <?php foreach ($prices as $row) {?>
            <td class="td-text2" id="promoprofit_<?=$i?>"><b><?=($row['profit']!='' ? '$'.number_format($row['profit'],0,'.','') : '')?></b></td>
            <?php $i++;?>
        <?php } ?>
        <td class="pricing_empty" style="padding-left:6px;">&nbsp;</td>
        <td class="td-text2" id="profit_print"><b><?=($common_prices['profit_print']!='' ? '$'.number_format($common_prices['profit_print'],2,'.','') : '')?></b></td>
        <td class="td-text2" id="profit_setup"><b><?=($common_prices['profit_setup']!='' ? '$'.number_format($common_prices['profit_setup'],2,'.','') : '')?></b></td>
    </tr>
    <tr align="center" id="profitperc" >
        <td class="td-text">Percent:</td>
        <?php $i=0;?>
        <?php foreach($prices as $row) {?>
            <td class="td-text2 <?=$row['profit_class']?>" id="promoprofitperc_<?=$i?>">
                <b><?=($row['profit_perc']!='' ? $row['profit_perc'].'%' : '')?></b>
            </td>
            <?php $i++;?>
        <?php } ?>
        <td class="pricing_empty" style="padding-left:6px;">&nbsp;</td>
        <td id="profit_print_perc" class="td-text2 <?=$common_prices['profit_print_class']?>">
            <?=($common_prices['profit_print_perc']!='' ? $common_prices['profit_print_perc'].'%' : '')?>
        </td>
        <td id="profit_setup_perc" class="td-text2 <?=$common_prices['profit_setup_class']?>">
            <?=($common_prices['profit_setup_perc']!='' ? $common_prices['profit_setup_perc'].'%' : '')?>
        </td>
    </tr>
</table>
