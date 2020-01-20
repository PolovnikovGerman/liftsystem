<table  cellspacing="0" cellpadding="0" class="tablepricetypes">
    <tr align="center">
        <td class="td-text">Quantity:</td>
        <?php foreach ($price_types as $row) {?>
            <td class="td-text3"><b><?=$row['type']?></b></td>
        <?php } ?>
        <td class="pricing_empty" style="width:17px;">
            &nbsp;
        </td>
        <td class="td-text3" ><b>Prints</b></td>
        <td class="td-text3-1"><b>Setup</b></td>
    </tr>
</table>
<img style="padding-left:20px;" src="/img/itemdetails/line-pricing.png"/>
<div id="pricedataview">
    <?=$prices?>
</div>
<img style="padding-left:20px;" src="/img/itemdetails/line-pricing.png"/>
<!-- Compare -->
<div id="researchdataview">
    <?=$researchdata?>
</div>
<img style="padding-left:20px;" src="/img/itemdetails/line-pricing.png"/>
<div id="profitdataview">
    <?=$profit_dat?>
</div>
