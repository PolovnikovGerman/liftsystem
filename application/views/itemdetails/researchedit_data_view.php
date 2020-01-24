<table border="0" cellspacing="0" cellpadding="0" align="left" class="researchprices">
    <?php $counter = 1; ?>
    <?php foreach ($research_price as $row) { ?>
        <tr align="center">
            <input type="hidden" id="othprice<?= $counter ?>" name="othprice<?= $counter ?>" value="<?= $row['other_vendorprice_id'] ?>"/>
            <input type="hidden" id="vendorid<?= $counter ?>" name="vendorid<?= $counter ?>" value="<?= $row['other_vendor_id'] ?>" />
            <input type="hidden" id="vendorname<?=$counter?>" value="<?= $row['vendor_name'] ?>"/>
            <td class="td-text competitorname" title="Last Updated Date<br/><?=date('m/d/y H:i:s', $row['other_vendorprice_updated'])?>">
                <?= $row['vendor_name'] ?>
            </td>
            <?php foreach ($price_types as $pricerow) { ?>
                <td class="td-text2 <?= $row['price_' . $pricerow['type'] . '_class'] ?>" id="priceval<?= $pricerow['type'] ?><?= $counter ?>">
                    <input type="hidden" id="price<?= $pricerow['type'] ?><?= $counter ?>" value="<?= $row['other_vendorprice_price_' . $pricerow['type']] ?>"/>
                    <?= (floatval($row['other_vendorprice_price_' . $pricerow['type']]) != 0 ? '$' . number_format($row['other_vendorprice_price_' . $pricerow['type']], 2, '.', '') : 'n/a') ?>
                </td>
            <?php } ?>
            <td class="pricing_empty" style="padding-left:6px;">&nbsp;</td>
            <td class="td-text2 <?= $row['price_print_class'] ?>" id="pricevalprint<?= $counter ?>">
                <input type="hidden" id="priceprint<?= $counter ?>" value="<?= $row['other_vendorprice_price_print'] ?>"/>
                <?= (floatval($row['other_vendorprice_price_print']) != 0 ? '$' . number_format($row['other_vendorprice_price_print'], 2, '.', '') : 'n/a') ?>
            </td>
            <td class="td-text2 <?= $row['price_setup_class'] ?>" id="pricevalsetup<?= $counter ?>">
                <input type="hidden" id="pricesetup<?= $counter ?>" value="<?= $row['other_vendorprice_price_setup'] ?>"/>
                <?= (floatval($row['other_vendorprice_price_setup']) != 0 ? '$' . number_format($row['other_vendorprice_price_setup'], 2, '.', '') : 'n/a') ?>
            </td>
            <td colspan="3" class="td-text2">
                <input type="hidden" id="price_url<?= $counter ?>" name="price_url<?= $counter ?>" value="<?= $row['other_vendor_price_url'] ?>"/>
                <a href="javascript:void(0)" onclick="openpriceurl(<?= $counter ?>);">
                    <img src="/img/itemdetails/openurl.png" alt="Open URL" />
                </a>
                <div class="otherpriceurl" id="otherpriceurl<?=$counter?>"><b>URL</b></div>
            </td>
        </tr>
        <?php $counter++; ?>
    <?php } ?>
</table>
