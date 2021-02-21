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
<?php if (count($totals)>0) { ?>
    <table style="width: 280px; font-size: 13px; color: #000000; border: 1px solid #000;border-collapse: collapse; margin-bottom: 15px;">
        <thead style="color: #FFFFFF; background-color: #000000">
        <tr>
            <td style="width: 40%;text-align: center;">Year</td>
            <td style="width: 60%;text-align: center;">Total</td>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($totals as $trow) { ?>
            <tr>
                <td style="text-align: center;"><?=$trow['year']?></td>
                <td style="border-left: 1px solid #000000; text-align: right;"><?=MoneyOutput($trow['debt'])?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
<?php } ?>
<table style="width: 960px; font-size: 13px; color: #000000; border: 1px solid #000;border-collapse: collapse; margin-bottom: 15px;">
    <thead style="color: #FFFFFF; background-color: #000000">
    <tr>
        <th style="width: 6%;text-align: center">Date</th>
        <th style="width: 6%; text-align: center">Order #</th>
        <th style="width: 10%; text-align: center">Confirm #</th>
        <th style="width: 23%; text-align: center;">Customer</th>
        <th style="width: 14%; text-align: center">Phone</th>
        <th style="width: 14%; text-align: center;">Email</th>
        <td style="width: 6%; text-align: center">Last message</td>
        <th style="width: 7%; text-align: center;">Revenue</th>
        <th style="width: 7%; text-align: center;">Pay Sum</th>
        <th style="width: 7%; text-align: center">Different</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($data as $row) { ?>
        <tr>
            <td><?=$row['order_date']?></td>
            <td style="border-left: 1px solid #000000;text-align: center;"><?=$row['order_num']?></td>
            <td style="border-left: 1px solid #000000;text-align: center;"><?=$row['order_confirmation']?></td>
            <td style="border-left: 1px solid #000000"><?=$row['customer_name']?></td>
            <td style="border-left: 1px solid #000000"><?=$row['phone']?></td>
            <td style="border-left: 1px solid #000000"><?=$row['email']?></td>
            <td style="border-left: 1px solid #000000;text-align: center;"><?=$row['last_update']?></td>
            <td style="border-left: 1px solid #000000;text-align: right"><?=$row['revenue']?></td>
            <td style="border-left: 1px solid #000000;text-align: right"><?=$row['paysum']?></td>
            <td style="border-left: 1px solid #000000;text-align: right"><?=$row['notpaid']?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
</body>
</html>

