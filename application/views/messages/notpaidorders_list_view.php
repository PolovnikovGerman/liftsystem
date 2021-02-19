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

<table style="width: 770px; font-size: 13px; color: #000000; border: 1px solid #000;border-collapse: collapse; margin-bottom: 15px;">
    <thead style="color: #FFFFFF; background-color: #000000">
    <tr>
        <th style="width: 10%;">Date</th>
        <th style="width: 13%;">Order #</th>
        <th style="width: 13%;">Confirm #</th>
        <th style="width: 34%;">Customer</th>
        <th style="width: 10%;">Revenue</th>
        <th style="width: 10%;">Pay Sum</th>
        <th style="width: 10%;">Different</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($data as $row) { ?>
        <tr>
            <td><?=$row['user']?></td>
            <td style="border-left: 1px solid #000000"><?=$row['order']?></td>
            <td style="border-left: 1px solid #000000"><?=$row['parameter']?></td>
            <td style="border-left: 1px solid #000000"><?=$row['old_value']?></td>
            <td style="border-left: 1px solid #000000"><?=$row['new_value']?></td>
            <td style="border-left: 1px solid #000000"><?=$row['description']?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
</body>
</html>

