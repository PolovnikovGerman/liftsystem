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
            <td colspan="4" style="text-align: center; font-size: 14px; font-weight: bold">Art Proofs Uploaded to Lift</td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: center; font-size: 13px; font-weight: bold"><?=$data['report_date']?> Grand Total - <?=$data['total']?></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center; font-size: 13px; font-weight: bold">SB</td>
            <td colspan="2" style="text-align: center; font-size: 13px; font-weight: bold">SR</td>
        </tr>
        <tr>
            <td>Orders:</td>
            <td><?=$data['sb_orders']?></td>
            <td>Orders:</td>
            <td><?=$data['sr_orders']?></td>
        </tr>
        <tr>
            <td>Incl Custom:</td>
            <td><?=$data['sb_order_custom']?></td>
            <td>Incl Custom:</td>
            <td><?=$data['sr_order_custom']?></td>
        </tr>
        <tr>
            <td>Leads:</td>
            <td><?=$data['sb_proofs']?></td>
            <td>Leads:</td>
            <td><?=$data['sr_proofs']?></td>
        </tr>
        <tr>
            <td>Incl Custom:</td>
            <td><?=$data['sb_proofs_custom']?></td>
            <td>Incl Custom:</td>
            <td><?=$data['sr_proofs_custom']?></td>
        </tr>
        <tr>
            <td style="font-weight: 600">Total</td>
            <td style="font-weight: 600"><?=$data['sb_total']?></td>
            <td style="font-weight: 600">Total</td>
            <td style="font-weight: 600"><?=$data['sr_total']?></td>
        </tr>
    </table>
    <br>
    <?=$weekview?>
    <br>
    <?=$yearview?>
</body>
<html>