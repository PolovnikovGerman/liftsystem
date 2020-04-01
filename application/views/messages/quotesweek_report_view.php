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
                font-weight: normal;
            }
            tr.proj {
                background-color: #001072;
                color: #FFFFFF;
            }
            tr.black {
                background-color: #000000;
                color: #FFFFFF;
            }
            tr.maroon {
                background-color: #6D0303;
                color: #FFFFFF;
            }
            tr.red {
                background-color: #FF0000;
                color: #FFFFFF;
            }
            tr.orange {
                background-color: #EA8A0E;
                color: #000000;
            }
            tr.white {
                background-color: #FFFFFF;
                color: #000000;
            }
            tr.green {
                background-color: #00E947;
                color: #000000;
            }
        </style>
    </head>
    <body>
        <div style="clear: both; float: left; font-size: 14px; font-weight: bold; text-align: center; width: 100%;"><?= $title; ?></div>
        <div style="clear: both; float: left; font-size: 12px; font-weight: normal; text-align: center; width: 100%;">
            <!-- border: 1px solid #000; -->
            <table style="width: 100%; font-size: 14px; border-collapse: collapse;">
                <thead>
                    <tr>
                        <!-- border: 1px solid #000000; -->
                        <td style="border: 1px solid #000000; text-align: center;">
                            Date
                        </td>
                        <td style="border: 1px solid #000000; text-align: center; border: 1px solid #000000;">
                            Quotes
                        </td>                        
                        <td style="border: 1px solid #000000; text-align: center; border: 1px solid #000000;">
                            Proof Requests
                        </td>                        
                        <td style="border: 1px solid #000000; text-align: center; border: 1px solid #000000;">
                            Orders
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lists as $row) { ?>
                        <tr>
                            <td style="text-align: left;border: 1px solid #000000;"><?= $row['date'] ?></td>
                            <td style="text-align: center;border: 1px solid #000000;"><?= $row['quotes'] ?></td>
                            <td style="text-align: center; border: 1px solid #000000;"><?= $row['proofreq'] ?></td>
                            <td style="text-align: center;border: 1px solid #000000;"><?= $row['orders'] ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </body>
</html>