<html xmlns="http://www.w3.org/1999/xhtml"
      style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; padding: 0;">
<head>
    <meta name="viewport" content="width=device-width"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>New Template</title>
    <style type="text/css">
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            box-sizing: border-box; font-size: 14px;
            -webkit-font-smoothing: antialiased;
            -webkit-text-size-adjust: none;
            width: 100% !important;
            height: 100%;
            line-height: 1.6;
            background: #f6f6f6;
            margin: 0;
            padding: 0;
        }
        table.main {
            background: #f6f6f6;
            font-size: 14px;
        }
        img {
            max-width: 100%;
        }

        body {
            -webkit-font-smoothing: antialiased;
            -webkit-text-size-adjust: none;
            /* width: 100% !important; */
            height: 100%;
            line-height: 1.6;
        }

        body {
            background-color: #f6f6f6;
        }

        .mobile_contacts {
            /* display: none; */
        }
        .mobile_icon {
            width: 8%;
        }
        .desktop_contacts {
            display: block;
        }
        @media (max-width: 420px) {
            .mobile_contacts {
                /*clear: both; */
                /* display: block; */
                /* float: left;
                margin: 10px auto 5px 20px;
                width: 28%; */
            }
            .mobile_icon {
                width: 10%;
            }
            .desktop_contacts {
                display: none;
            }
        }

    </style>
</head>
<body>
<!-- header -->
<table class="main">
    <tr>
        <td width="10px">&nbsp;</td>
        <td width="700" style="padding-top: 15px">
            <table style="background: #757575; width: 100%;">
                <tr>
                    <td style="width: 15px" >&nbsp;</td>
                    <td style="width: 670px; background: #757575; text-align: center;">
                        <table style="background: #757575; width: 100%;">
                            <tr style="height: 2.5rem;">
                                <td style="width: 100%; text-align: center; vertical-align: baseline">
                                    <div style="width: 40%;margin: 17px auto;">
                                        <img src="https://www.tutoria.de/img/logo_jetztnachhilfe.png" alt="Logo"/>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 15px" >&nbsp;</td>
                </tr>
                <tr>
                    <td style="width: 15px" >&nbsp;</td>
                    <td style="width: 670px; background: #f6f6f6; padding: 0.9rem;">
                        <!-- Text -->
                        <table width="100%" cellpadding="0" cellspacing="0"
                               style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; padding: 0;">
                            <tr style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; padding: 0;">
                                <td class="content-block"
                                    style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 20px; font-weight:bold; vertical-align: top; margin: 0; padding: 0 0 20px;"
                                    valign="top">
                                    Hallo {{name}}!
                                </td>
                            </tr>
                            <tr style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; padding: 0;">
                                <td class="content-block"
                                    style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;"
                                    valign="top">
                                    Wir freuen uns, dich bei tutoria begrüßen zu dürfen. Bei tutoria verbinden wir
                                    dich mit Nachhilfelehrern ganz bei dir in der Nähe. Es liegt uns am Herzen den
                                    richtigen Nachhilfelehrer für dich zu finden. <br>
                                    <br>
                                    Wie kannst du den perfekten Nachhilfelehrer für dich finden? Ganz einfach! Gib
                                    einfach über das Feld „Nachhilfelehrer finden“ ein, in welcher Region du einen
                                    Nachhilfelehrer suchst und in welchem Fach. <br><br>
                                    Dann werden dir alle Nachhilfelehrer bei dir in der Nähe angezeigt und du kannst
                                    den passenden Nachhilfelehrer für dich auswählen. Wenn du auf den Button
                                    klickst, kannst du direkt mit der Suche starten!
                                </td>
                            </tr>
                            <tr style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; padding: 0;">
                                <td class="content-block"
                                    style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;"
                                    valign="top">
                                    <a href="{{ link }}" class="btn-primary"
                                       style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px;  background: #348eda; margin: 0; padding: 0; border-color: #348eda; border-style: solid; border-width: 10px 20px;">Nachhilfelehrer
                                        finden</a>
                                </td>
                            </tr>
                            <tr>
                                <td>{{ link }}
                                <td>
                            </tr>
                            <tr style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; padding: 0;">
                                <td class="content-block"
                                    style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;"
                                    valign="top">
                                    Wenn du Fragen oder Anmerkungen hast, kannst du uns jederzeit unter
                                    info@jetztnachhilfe.de oder unter 0800 111 12 36 kontaktieren.<br>
                                    <br>
                                    Wir wünschen dir viel Erfolg auf tutoria!<br>
                                    Mit freundlichen Grüßen,<br>
                                    dein jetztnachhilfe Team
                                </td>
                            </tr>
                        </table>
                        <!-- End Text -->
                    </td>
                    <td style="width: 15px">&nbsp;</td>
                </tr>
            </table>
            <table style="background: #757575; width: 100%; color: #ffffff">
                <tr>
                    <td style="padding-left:2%; text-align: center;">Hast du Fragen?</td>
                </tr>
                <tr class="mobile_contacts">
                    <td style="padding-left: 2%">
                        <div style="width: 60%;margin: 0 auto;">
                            <!-- width: 14%; -->
                            <div style="width: 10%;margin-left: 2%;float: left;"><img src="https://www.tutoria.de/img/phone-red.png" alt="Phone" class="img-fluid" style="max-width: 100%"></div>
                            <span style="margin-left: 2%;font-size: 110%; float: left;">0800 111 12 36</span>
                        </div>
                    </td>
                </tr>
                <tr class="mobile_contacts">
                    <td style="padding-left: 2%;border-bottom: 1px solid #bdbdbd;">
                        <div style="width: 60%;margin: 0 auto;">
                            <div style="width: 10%;margin-left: 2%;float: left;"><img src="https://www.tutoria.de/img/envelope-red.png" alt="Email" class="img-fluid" style="max-width: 100%"></div>
                            <span style="margin-left: 2%;font-size: 110%; float: left;">info@jetztnachhilfe.de</span>
                        </div>
                    </td>
                </tr>
                <!-- Desktop Contacts -->
                <tr>
                    <td style="padding-left: 2%">&copy; 2021 Studienkreis GmbH. Alle Rechte vorbehalten.</td>
                </tr>
                <tr style="height: 28px;line-height: 28px;">
                    <td style="padding-left: 2%">
                        <a style="color: #ffffff; text-decoration: none; font-weight: 600;" href="https://www.tutoria.de/allgemeine-gesch%C3%A4ftsbedingungen">AGB</a> |
                        <a style="color: #ffffff; text-decoration: none; font-weight: 600;" href="https://www.tutoria.de/datenschutz">Datenschutz</a>  |
                        <a style="color: #ffffff; text-decoration: none; font-weight: 600;" href="https://www.tutoria.de/impressum">Impressum</a>
                    </td>
                </tr>
                <tr>
                    <td style="padding-left: 2%">
                        jetztnachhilfe Teil der Studienkreis GmbH | Universitätsstraße 104 | 44799 Bochum
                    </td>
                </tr>
                <tr>
                    <td style="padding-left: 2%">
                        Geschäftsführer: Lorenz Haase
                    </td>
                </tr>
                <tr>
                    <td style="padding-left: 2%">
                        Registergericht: HRB 4581 | USt-IdNr.: DE124086608
                    </td>
                </tr>
            </table>
        </td>
        <td width="10px">&nbsp;</td>
    </tr>
</table>

</body>
</html>

