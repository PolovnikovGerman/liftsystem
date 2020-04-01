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
                        <td style="border: 1px solid #000000; background: #254c25 ; color :#FFFFFF; text-align: center;">
                            &nbsp;
                        </td>
                        <td style="border: 1px solid #000000; background: #254c25 ; color :#FFFFFF; text-align: center; border: 1px solid #000000;" colspan="6">
                            New Proofs
                        </td>
                        <td style="width: 3px;">&nbsp;</td>
                        <td style="border: 1px solid #000000; background: #254c25 ; color :#FFFFFF; text-align: center; border: 1px solid #000000;" colspan="6">
                            Changes
                        </td>
                        <td style="width: 3px;">&nbsp;</td>
                        <td style="border: 1px solid #000000; background: #254c25 ; color :#FFFFFF; text-align: center; border: 1px solid #000000;" colspan="2">
                            Total All
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000000; background: #254c25 ; color :#FFFFFF; text-align: center; border: 1px solid #000000;">User</td>
                        <td style="border: 1px solid #000000; background: #254c25 ; color :#FFFFFF; text-align: center; border: 1px solid #000000;">Orders</td>
                        <td style="border: 1px solid #000000; background: #254c25 ; color :#FFFFFF; text-align: center; border: 1px solid #000000;">Opt</td>
                        <td style="border: 1px solid #000000; background: #254c25 ; color :#FFFFFF; text-align: center; border: 1px solid #000000;">Leads</td>
                        <td style="border: 1px solid #000000; background: #254c25 ; color :#FFFFFF; text-align: center; border: 1px solid #000000;">Opt</td>
                        <td style="border: 1px solid #000000; background: #254c25 ; color :#FFFFFF; text-align: center; border: 1px solid #000000;">Total</td>
                        <td style="border: 1px solid #000000; background: #254c25 ; color :#FFFFFF; text-align: center; border: 1px solid #000000;">Total Opt</td>
                        <td style="width: 3px;">&nbsp;</td>
                        <td style="border: 1px solid #000000; background: #254c25 ; color :#FFFFFF; text-align: center; border: 1px solid #000000;">Orders</td>
                        <td style="border: 1px solid #000000; background: #254c25 ; color :#FFFFFF; text-align: center; border: 1px solid #000000;">Opt</td>
                        <td style="border: 1px solid #000000; background: #254c25 ; color :#FFFFFF; text-align: center; border: 1px solid #000000;">Leads</td>
                        <td style="border: 1px solid #000000; background: #254c25 ; color :#FFFFFF; text-align: center; border: 1px solid #000000;">Opt</td>
                        <td style="border: 1px solid #000000; background: #254c25 ; color :#FFFFFF; text-align: center; border: 1px solid #000000;">Total</td>
                        <td style="border: 1px solid #000000; background: #254c25 ; color :#FFFFFF; text-align: center; border: 1px solid #000000;">Total Opt</td>
                        <td style="width: 3px;">&nbsp;</td>
                        <td style="border: 1px solid #000000; background: #254c25 ; color :#FFFFFF; text-align: center; border: 1px solid #000000;">All</td>
                        <td style="border: 1px solid #000000; background: #254c25 ; color :#FFFFFF; text-align: center; border: 1px solid #000000;">Opt</td>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lists as $row) { ?>
                        <tr>
                            <td style="text-align: left;border: 1px solid #000000;"><?= $row['user'] ?></td>
                            <td style="text-align: center;border: 1px solid #000000;"><?= $row['orders_first'] ?></td>
                            <td style="background: #494948; color : #e5e5e5; text-align: center; border: 1px solid #000000;"><?= $row['orders_first_attach'] ?></td>
                            <td style="text-align: center;border: 1px solid #000000;"><?= $row['request_first'] ?></td>
                            <td style="background: #494948; color : #e5e5e5; text-align: center;border: 1px solid #000000;"><?= $row['request_first_attach'] ?></td>
                            <td style="text-align: center;border: 1px solid #000000;"><?= $row['all_first'] ?></td>
                            <td style="background: #494948; color : #e5e5e5; text-align: center;border: 1px solid #000000;"><?= $row['all_first_attach'] ?></td>
                            <td style="width: 3px;">&nbsp;</td>
                            <td style="text-align: center;border: 1px solid #000000;"><?= $row['orders_resend'] ?></td>
                            <td style="background: #494948; color : #e5e5e5; text-align: center;border: 1px solid #000000;"><?= $row['orders_resend_attach'] ?></td>
                            <td style="text-align: center;border: 1px solid #000000;"><?= $row['request_resend'] ?></td>
                            <td style="background: #494948; color : #e5e5e5; text-align: center;border: 1px solid #000000;"><?= $row['request_resend_attach'] ?></td>
                            <td style="text-align: center;border: 1px solid #000000;"><?= $row['all_resend'] ?></td>
                            <td style="background: #494948; color : #e5e5e5; text-align: center;border: 1px solid #000000;"><?= $row['all_resend_attach'] ?></td>
                            <td style="width: 3px;">&nbsp;</td>
                            <td style="text-align: center;border: 1px solid #000000;"><?= $row['all'] ?></td>
                            <td style="background: #494948; color : #e5e5e5; text-align: center;border: 1px solid #000000;"><?= $row['all_attach'] ?></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td style="text-align: left;border: 1px solid #000000;">Total:</td>
                        <td style="text-align: center;border: 1px solid #000000;"><?= $total['orders_first'] ?></td>
                        <td style="background: #494948; color : #e5e5e5; text-align: center;border: 1px solid #000000;"><?= $total['orders_first_attach'] ?></td>
                        <td style="text-align: center;border: 1px solid #000000;"><?= $total['request_first'] ?></td>
                        <td style="background: #494948; color : #e5e5e5; text-align: center;border: 1px solid #000000;"><?= $total['request_first_attach'] ?></td>
                        <td style="text-align: center;border: 1px solid #000000;"><?= $total['all_first'] ?></td>
                        <td style="background: #494948; color : #e5e5e5; text-align: center;border: 1px solid #000000;"><?= $total['all_first_attach'] ?></td>
                        <td style="width: 3px;">&nbsp;</td>
                        <td style="text-align: center;border: 1px solid #000000;"><?= $total['orders_resend'] ?></td>
                        <td style="background: #494948; color : #e5e5e5; text-align: center;border: 1px solid #000000;"><?= $total['orders_resend_attach'] ?></td>
                        <td style="text-align: center;border: 1px solid #000000;"><?= $total['request_resend'] ?></td>
                        <td style="background: #494948; color : #e5e5e5; text-align: center;border: 1px solid #000000;"><?= $total['request_resend_attach'] ?></td>
                        <td style="text-align: center;border: 1px solid #000000;"><?= $total['all_resend'] ?></td>
                        <td style="background: #494948; color : #e5e5e5; text-align: center;border: 1px solid #000000;"><?= $total['all_resend_attach'] ?></td>
                        <td style="width: 3px;">&nbsp;</td>
                        <td style="text-align: center;border: 1px solid #000000;"><?= $total['all'] ?></td>
                        <td style="background: #494948; color : #e5e5e5; text-align: center;border: 1px solid #000000;"><?= $total['all_attach'] ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <br/>
        <div style="clear: both; float: left; font-size: 14px; font-weight: bold; text-align: center; width: 100%;">Regular VS Custom</div>
        <div style="clear: both; float: left; font-size: 12px; font-weight: normal; text-align: center; width: 100%;">
            <!-- border: 1px solid #000; -->
            <table style="width: 100%; font-size: 14px; border-collapse: collapse;">
                <thead>
                    <tr>
                        <td style="border: 1px solid #000000; background: #254c25 ; color :#FFFFFF; text-align: center;">
                            &nbsp;
                        </td>
                        <td style="border: 1px solid #000000; background: #254c25 ; color :#FFFFFF; text-align: center; border: 1px solid #000000;" colspan="6">
                            Regular
                        </td>
                        <td style="width: 3px;">&nbsp;</td>
                        <td style="border: 1px solid #000000; background: #254c25 ; color :#FFFFFF; text-align: center; border: 1px solid #000000;" colspan="6">
                            Custom
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000000; background: #254c25 ; color :#FFFFFF; text-align: center; border: 1px solid #000000;">
                            User
                        </td>
                        <td style="border: 1px solid #000000; background: #254c25 ; color :#FFFFFF; text-align: center; border: 1px solid #000000;">
                            Orders
                        </td>
                        <td style="border: 1px solid #000000; background: #254c25 ; color :#FFFFFF; text-align: center; border: 1px solid #000000;">
                            Opt
                        </td>
                        <td style="border: 1px solid #000000; background: #254c25 ; color :#FFFFFF; text-align: center; border: 1px solid #000000;">
                            Leads 
                        </td>
                        <td style="border: 1px solid #000000; background: #254c25 ; color :#FFFFFF; text-align: center; border: 1px solid #000000;">
                            Opt
                        </td>
                        <td style="border: 1px solid #000000; background: #254c25 ; color :#FFFFFF; text-align: center; border: 1px solid #000000;">
                            All 
                        </td>
                        <td style="border: 1px solid #000000; background: #254c25 ; color :#FFFFFF; text-align: center; border: 1px solid #000000;">
                            All Opt
                        </td>
                        <td style="width: 3px;">&nbsp;</td>                        
                        <td style="border: 1px solid #000000; background: #254c25 ; color :#FFFFFF; text-align: center; border: 1px solid #000000;">
                            Orders
                        </td>
                        <td style="border: 1px solid #000000; background: #254c25 ; color :#FFFFFF; text-align: center; border: 1px solid #000000;">
                            Opt
                        </td>
                        <td style="border: 1px solid #000000; background: #254c25 ; color :#FFFFFF; text-align: center; border: 1px solid #000000;">
                            Leads 
                        </td>
                        <td style="border: 1px solid #000000; background: #254c25 ; color :#FFFFFF; text-align: center; border: 1px solid #000000;">
                            Opt
                        </td>
                        <td style="border: 1px solid #000000; background: #254c25 ; color :#FFFFFF; text-align: center; border: 1px solid #000000;">
                            All 
                        </td>
                        <td style="border: 1px solid #000000; background: #254c25 ; color :#FFFFFF; text-align: center; border: 1px solid #000000;">
                            All Opt
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($listtype as $row) { ?>
                        <tr>
                            <td style="text-align: left;border: 1px solid #000000;"><?= $row['user'] ?></td>
                            <td style="text-align: center;border: 1px solid #000000;">
                                <?=($row['orders_reg_first']+$row['orders_reg_resend'])  ?>
                            </td>
                            <td style="background: #494948; color : #e5e5e5; text-align: center;border: 1px solid #000000;">
                                <?= ($row['orders_reg_first_attach']+$row['orders_reg_resend_attach'])?>
                            </td>
                            <td style="text-align: center;border: 1px solid #000000;">
                                <?=($row['request_reg_first']+$row['request_reg_resend']) ?>
                            </td>
                            <td style="background: #494948; color : #e5e5e5; text-align: center;border: 1px solid #000000;">
                                <?=($row['request_reg_first_attach']+$row['request_reg_resend_attach'])?>
                            </td>
                            <td style="text-align: center;border: 1px solid #000000;">
                                <?=($row['all_reg_first']+$row['all_reg_resend']) ?>
                            </td>
                            <td style="background: #494948; color : #e5e5e5; text-align: center;border: 1px solid #000000;">
                                <?=($row['all_reg_first_attach']+$row['all_reg_resend_attach']) ?>
                            </td>
                            <td style="width: 3px;">&nbsp;</td>
                            <td style="text-align: center;border: 1px solid #000000;">
                                <?=($row['orders_cust_first']+$row['orders_cust_resend'])  ?>
                            </td>
                            <td style="background: #494948; color : #e5e5e5; text-align: center;border: 1px solid #000000;">
                                <?= ($row['orders_cust_first_attach']+$row['orders_cust_resend_attach'])?>
                            </td>
                            <td style="text-align: center;border: 1px solid #000000;">
                                <?=($row['request_cust_first']+$row['request_cust_resend']) ?>
                            </td>
                            <td style="background: #494948; color : #e5e5e5; text-align: center;border: 1px solid #000000;">
                                <?=($row['request_cust_first_attach']+$row['request_cust_resend_attach'])?>
                            </td>
                            <td style="text-align: center;border: 1px solid #000000;">
                                <?=($row['all_cust_first']+$row['all_cust_resend']) ?>
                            </td>
                            <td style="background: #494948; color : #e5e5e5; text-align: center;border: 1px solid #000000;">
                                <?=($row['all_cust_first_attach']+$row['all_cust_resend_attach']) ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td style="text-align: left;border: 1px solid #000000;">Total:</td>
                        <td style="text-align: center;border: 1px solid #000000;">
                            <?=($totaltype['orders_reg_first']+$totaltype['orders_reg_resend'])  ?>
                        </td>
                        <td style="background: #494948; color : #e5e5e5; text-align: center;border: 1px solid #000000;">
                            <?= ($totaltype['orders_reg_first_attach']+$totaltype['orders_reg_resend_attach'])?>
                        </td>
                        <td style="text-align: center;border: 1px solid #000000;">
                            <?=($totaltype['request_reg_first']+$totaltype['request_reg_resend']) ?>
                        </td>
                        <td style="background: #494948; color : #e5e5e5; text-align: center;border: 1px solid #000000;">
                            <?=($totaltype['request_reg_first_attach']+$totaltype['request_reg_resend_attach'])?>
                        </td>
                        <td style="text-align: center;border: 1px solid #000000;">
                            <?=($totaltype['all_reg_first']+$totaltype['all_reg_resend']) ?>
                        </td>
                        <td style="background: #494948; color : #e5e5e5; text-align: center;border: 1px solid #000000;">
                            <?=($totaltype['all_reg_first_attach']+$totaltype['all_reg_resend_attach']) ?>
                        </td>
                        <td style="width: 3px;">&nbsp;</td>
                        <td style="text-align: center;border: 1px solid #000000;">
                            <?=($totaltype['orders_cust_first']+$totaltype['orders_cust_resend'])  ?>
                        </td>
                        <td style="background: #494948; color : #e5e5e5; text-align: center;border: 1px solid #000000;">
                            <?= ($totaltype['orders_cust_first_attach']+$totaltype['orders_cust_resend_attach'])?>
                        </td>
                        <td style="text-align: center;border: 1px solid #000000;">
                            <?=($totaltype['request_cust_first']+$totaltype['request_cust_resend']) ?>
                        </td>
                        <td style="background: #494948; color : #e5e5e5; text-align: center;border: 1px solid #000000;">
                            <?=($totaltype['request_cust_first_attach']+$totaltype['request_cust_resend_attach'])?>
                        </td>
                        <td style="text-align: center;border: 1px solid #000000;">
                            <?=($totaltype['all_cust_first']+$totaltype['all_cust_resend']) ?>
                        </td>
                        <td style="background: #494948; color : #e5e5e5; text-align: center;border: 1px solid #000000;">
                            <?=($totaltype['all_cust_first_attach']+$totaltype['all_cust_resend_attach']) ?>
                        </td>
                    </tr>
                </tbody>
            </table>            
        </div>
    </body>
</html>