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
    </style>
</head>
<body>
    <table style="width: 580px; font-size: 14px; border-collapse: collapse;">
        <tr>
            <td colspan="4" style="text-align: center; font-size: 14px; font-weight: bold">Art Proofs Uploaded to Lift</td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: center; font-size: 13px; font-weight: bold"><?=$report_date?> Grand Total - <?=$total?></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center; font-size: 13px; font-weight: bold">SB</td>
            <td colspan="2" style="text-align: center; font-size: 13px; font-weight: bold">SR</td>
        </tr>
        <tr>
            <td>Orders:</td>
            <td><?=$sb_orders?></td>
            <td>Orders:</td>
            <td><?=$sr_orders?></td>
        </tr>
        <tr>
            <td>Leads:</td>
            <td><?=$sb_proofs?></td>
            <td>Leads:</td>
            <td><?=$sr_proofs?></td>
        </tr>
        <tr>
            <td style="font-weight: 600">Total</td>
            <td style="font-weight: 600"><?=$sb_total?></td>
            <td style="font-weight: 600">Total</td>
            <td style="font-weight: 600"><?=$sr_total?></td>
        </tr>
    </table>
</body>
<html>