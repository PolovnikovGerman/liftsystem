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
            /* font-weight: normal; */
        }
        tr {
            height: 24px;
            line-height: 24px;
        }
        table.historyupl td {
            border-collapse: collapse;
            border: 1px solid #000000;
        }
    </style>
</head>
<body>
<table style="width: 580px; font-size: 14px; border-collapse: collapse;">
    <tr>
        <td colspan="4" style="text-align: center; font-size: 14px; font-weight: bold"><?=date('m/d/Y')?> - Tracking # Added</td>
    </tr>
    <tr>
        <td>Order #</td>
        <td>Customer</td>
        <td>Item #</td>
        <td>Item Name</td>
        <td>QTY</td>
        <td>Service</td>
        <td>Track #</td>
    </tr>
    <?php foreach ($tracks as $track) : ?>
    <tr>
        <td><?=$track['order_num']?></td>
        <td><?=$track['customer_name']?></td>
        <td><?=$track['item_number']?></td>
        <td><?=$track['item_name']?></td>
        <td><?=$track['qty']?></td>
        <td><?=$track['trackservice']?></td>
        <td><?=$track['trackcode']?></td>
    </tr>
    <?php endforeach; ?>
</table>
</body>
</html>