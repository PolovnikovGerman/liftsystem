<html>
<head>
    <style type="text/css">
body {
  font-family: Verdana, sans-serif;
  font-size: 0.8em;
  color:#484848;
}
h1, h2, h3 { font-family: "Trebuchet MS", Verdana, sans-serif; margin: 0px; }
h1 { font-size: 1.2em; }
h2, h3 { font-size: 1.1em; }
a, a:link, a:visited { color: #2A5685;}
a:hover, a:active { color: #c61a1a; }
a.wiki-anchor { display: none; }
p {font-size: 13px; color: #000000;}
hr {
  width: 100%;
  height: 1px;
  background: #ccc;
  border: 0;
}
.footer {
  font-size: 0.8em;
  font-style: italic;
}
.maintitle, .subtitle {
    clear: both;
    float: left;
    font-size: 14px;
    font-weight: bold;
    text-align: center;
    width: 100%;
}
.subtitle {
    font-size: 12px;
    font-weight: normal;
}
.span.bonusdetails {
    color: #ccc;
    font-size: 85%;
}
</style>
</head>
<body>
    <p>1-499 Piece Orders = <?=$price_500?> Point</p>
    <p>500-999 Piece Orders = <?=$price_1000?> Points</p>
    <p>1000+ Piece Orders = <?=$price_1200?> Points</p>    
    <p><b>Point Value: <?= MoneyOutput($bonus_price)?> / Point</b></p>
    <br/>
    <table style="width: 1100px; font-size: 13px; color: #000000; border: 1px solid #000;border-collapse: collapse;">
        <thead>
            <tr style="border: 1px solid #000000;">
                <th style="width: 12%; text-align: center;border: 1px solid #000000; background-color: #0000ff; color: #FFFFFF;">Dates</th>
                <th style="width: 10%; text-align: center;border: 1px solid #000000; background-color: #0000ff; color: #FFFFFF;">0-499 Piece Orders</th>
                <th style="width: 10%; text-align: center;border: 1px solid #000000; background-color: #0000ff; color: #FFFFFF;">500-999 Piece Orders</th>
                <th style="width: 10%; text-align: center;border: 1px solid #000000; background-color: #0000ff; color: #FFFFFF;">1000+ Piece Orders</th>
                <th style="width: 8%; text-align: center;border: 1px solid #000000; background-color: #0000ff; color: #FFFFFF;">Total Orders</th>
                <th style="width: 8%; text-align: center;border: 1px solid #000000; background-color: #0000ff; color: #FFFFFF;">Total Points</th>
                <th style="width: 8%; text-align: center;border: 1px solid #000000; background-color: #0000ff; color: #FFFFFF;">Total Bonus Payment</th>
                <th style="width: 8%; text-align: center;border: 1px solid #000000; background-color: #0000ff; color: #FFFFFF;">Weekly Base</th>
                <th style="width: 8%; text-align: center;border: 1px solid #000000; background-color: #0000ff; color: #FFFFFF;">Total Pay</th>
                <th style="width: 6%; text-align: center;border: 1px solid #000000; background-color: #000000; color: #FFFFFF;">Canceled Orders</th>
                <th style="width: 6%; text-align: center;border: 1px solid #000000; background-color: #000000; color: #FFFFFF;">Canceled Points</th>
                <th style="width: 6%; text-align: center;border: 1px solid #000000; background-color: #000000; color: #FFFFFF;">Canceled Value</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($admin) && $admin==1) { ?>
            <tr>
                <td style="text-align: center;border: 1px solid #000000;"><b>Total</b></td>
                <td style="text-align: center;border: 1px solid #000000;">
                    <?php if ($total['total_500']==0) { ?>
                        &mdash;
                    <?php } else { ?>
                        <b><?=$total['orders_500']?></b> <span class="bonusdetails" style="color: #696969; font-size: 12px;"> x <?=$price_500?> = <?=$total['total_500']?></span>
                    <?php } ?>                    
                </td>
                <td style="text-align: center;border: 1px solid #000000;">
                    <?php if ($total['total_1000']==0) { ?>
                        &mdash;
                    <?php } else { ?>
                        <b><?=$total['orders_1000']?></b> <span class="bonusdetails" style="color: #696969; font-size: 12px;"> x <?=$price_1000?> = <?=$total['total_1000']?></span>
                    <?php } ?>
                </td>
                <td style="text-align: center;border: 1px solid #000000;">
                    <?php if ($total['total_1200']==0) { ?>
                        &mdash;
                    <?php } else { ?>
                        <b><?=$total['orders_1200']?></b> <span class="bonusdetails" style="color: #696969; font-size: 12px;"> x <?=$price_1200?> = <?=$total['total_1200']?></span>
                    <?php } ?>                    
                </td>
                <td style="text-align: center;border: 1px solid #000000;"><?=$total['total_orders']?></td>
                <td style="text-align: center;border: 1px solid #000000;"><?=$total['bonuses']?></td>
                <td style="text-align: center;border: 1px solid #000000;"><b><?= MoneyOutput($total['prize'])?></b></td>
                <td style="text-align: center;border: 1px solid #000000;"><?= MoneyOutput($total['week_base'])?></td>
                <td style="text-align: center;border: 1px solid #000000;"><b><?= MoneyOutput($total['week_pay'])?></b></td>
                <td style="text-align: center;border: 1px solid #000000;">
                <?php if ($total['cancel_orders']==0) { ?>
                    &mdash;
                <?php } else { ?>
                    <?=$total['cancel_orders']?>
                <?php } ?>                            
                </td>                
                <td style="text-align: center;border: 1px solid #000000;">
                <?php if ($total['cancel_points']==0) { ?>
                    &mdash;
                <?php } else { ?>
                    <?=$total['cancel_points']?>
                <?php } ?>                
                </td>
                <td style="text-align: center;border: 1px solid #000000;"><b><?=$total['cancel_values']==0 ? '&mdash;' : MoneyOutput($total['cancel_values'])?></b></td>
            </tr>
            <?php } ?>
            <?php foreach ($data as $row) { ?>
            <?php $show_row=0; ?>
            <?php if ((isset($admin) && $admin==1) || $row['show_user']==1) { ?>
            <tr>
                <td style="text-align: center;border: 1px solid #000000;"><?=$row['dates']?></td>
                <td style="text-align: center;border: 1px solid #000000;">
                    <?php if ($row['total_500']==0) { ?>
                        &mdash;
                    <?php } else { ?>
                        <b><?=$row['orders_500']?></b> <span class="bonusdetails" style="color: #696969; font-size: 12px;"> x <?=$price_500?> = <?=$row['total_500']?></span>
                    <?php } ?>                    
                </td>
                <td style="text-align: center;border: 1px solid #000000;">
                    <?php if ($row['total_1000']==0) { ?>
                        &mdash;
                    <?php } else { ?>
                        <b><?=$row['orders_1000']?></b> <span class="bonusdetails" style="color: #696969; font-size: 12px;"> x <?=$price_1000?> = <?=$row['total_1000']?></span>
                    <?php } ?>
                </td>
                <td style="text-align: center;border: 1px solid #000000;">
                    <?php if ($row['total_1200']==0) { ?>
                        &mdash;
                    <?php } else { ?>
                        <b><?=$row['orders_1200']?></b> <span class="bonusdetails" style="color: #696969; font-size: 12px;"> x <?=$price_1200?> = <?=$row['total_1200']?></span>
                    <?php } ?>                    
                </td>
                <td style="text-align: center;border: 1px solid #000000;"><?=$row['total_orders']?></td>
                <td style="text-align: center;border: 1px solid #000000;"><?=$row['bonuses']?></td>
                <td style="text-align: center;border: 1px solid #000000;"><b><?= MoneyOutput($row['prize'])?></b></td>
                <td style="text-align: center;border: 1px solid #000000;"><?= MoneyOutput($row['week_base'])?></td>
                <td style="text-align: center;border: 1px solid #000000;"><b><?= MoneyOutput($row['week_pay'])?></b></td>
                <td style="text-align: center;border: 1px solid #000000;">
                <?php if ($row['cancel_orders']==0) { ?>
                    &mdash;
                <?php } else { ?>
                    <?=$row['cancel_orders']?>
                <?php } ?>
                </td>
                <td style="text-align: center;border: 1px solid #000000;">
                <?php if ($row['cancel_points']==0) { ?>
                    &mdash;
                <?php } else { ?>
                    <?=$row['cancel_points']?>
                <?php } ?>
                </td>
                <td style="text-align: center;border: 1px solid #000000;"><b><?=$row['cancel_values']==0 ? '&mdash;' : MoneyOutput($row['cancel_values'])?></b></td>
            </tr>
            <?php } ?>
            <?php if (isset($admin) && $admin==1 && $row['admin_break']) { ?>
            <tr><td colspan="12">&nbsp;</td></tr>
            <?php } ?>            
            <?php } ?>
        </tbody>
    </table>
</body>
</html>