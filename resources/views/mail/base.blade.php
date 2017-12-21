<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title></title> <!-- The title tag shows in email notifications, like Android 4.4. -->
    <!-- Web Font / @font-face : BEGIN -->
    <!-- NOTE: If web fonts are not required, lines 10 - 27 can be safely removed. -->

    <!-- Desktop Outlook chokes on web font references and defaults to Times New Roman, so we force a safe fallback font. -->
    <!--[if mso]>
        <style>
            * {
                font-family: sans-serif !important;
            }
        </style>
    <![endif]-->

    <!-- All other clients get the webfont reference; some will render the font and others will silently fail to the fallbacks. More on that here: http://stylecampaign.com/blog/2015/02/webfont-support-in-email/ -->
    <!--[if !mso]><!-->
    <!-- insert web font reference, eg: <link href='https://fonts.googleapis.com/css?family=Roboto:400,700' rel='stylesheet' type='text/css'> -->
    <!--<![endif]-->

    <!-- Web Font / @font-face : END -->
    <!-- CSS Reset : BEGIN -->
    <style>
        /* What it does: Remove spaces around the email design added by some email clients. */
        /* Beware: It can remove the padding / margin and add a background color to the compose a reply window. */
        html,
        body {
            margin: 0 auto !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
        }

        /* What it does: Stops email clients resizing small text. */
        * {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }

        /* What it does: Centers email on Android 4.4 */
        div[style*="margin: 16px 0"] {
            margin: 0 !important;
        }

        /* What it does: Stops Outlook from adding extra spacing to tables. */
        table,
        td {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
        }

        /* What it does: Fixes webkit padding issue. Fix for Yahoo mail table alignment bug. Applies table-layout to the first 2 tables then removes for anything nested deeper. */
        table {
            border-spacing: 0 !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            margin: 0 auto !important;
        }
        table table table {
            table-layout: auto;
        }

        /* What it does: Uses a better rendering method when resizing images in IE. */
        img {
            -ms-interpolation-mode:bicubic;
        }

        /* What it does: A work-around for email clients meddling in triggered links. */
        *[x-apple-data-detectors],  /* iOS */
        .x-gmail-data-detectors,    /* Gmail */
        .x-gmail-data-detectors *,
        .aBn {
            border-bottom: 0 !important;
            cursor: default !important;
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        /* What it does: Prevents Gmail from displaying an download button on large, non-linked images. */
        .a6S {
            display: none !important;
            opacity: 0.01 !important;
        }
        /* If the above doesn't work, add a .g-img class to any image in question. */
        img.g-img + div {
            display: none !important;
        }

        /* What it does: Prevents underlining the button text in Windows 10 */
        .button-link {
            text-decoration: none !important;
        }

        /* What it does: Removes right gutter in Gmail iOS app: https://github.com/TedGoas/Cerberus/issues/89  */
        /* Create one of these media queries for each additional viewport size you'd like to fix */
        /* Thanks to Eric Lepetit @ericlepetitsf) for help troubleshooting */
        @media only screen and (min-device-width: 375px) and (max-device-width: 413px) { /* iPhone 6 and 6+ */
            .email-container {
                min-width: 375px !important;
            }
        }

    </style>
    <!-- CSS Reset : END -->

    <!-- Progressive Enhancements : BEGIN -->
    <style>

    /* What it does: Hover styles for buttons */
    .button-td,
    .button-a {
        transition: all 100ms ease-in;
    }
    .button-td:hover,
    .button-a:hover {
        background: #555555 !important;
        border-color: #555555 !important;
    }

    /* Media Queries */
    @media screen and (max-width: 480px) {

        /* What it does: Forces elements to resize to the full width of their container. Useful for resizing images beyond their max-width. */
        .fluid {
            width: 100% !important;
            max-width: 100% !important;
            height: auto !important;
            margin-left: auto !important;
            margin-right: auto !important;
        }

        /* What it does: Forces table cells into full-width rows. */
        .stack-column,
        .stack-column-center {
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            direction: ltr !important;
        }
        /* And center justify these ones. */
        .stack-column-center {
            text-align: center !important;
        }

        /* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */
        .center-on-narrow {
            text-align: center !important;
            display: block !important;
            margin-left: auto !important;
            margin-right: auto !important;
            float: none !important;
        }
        table.center-on-narrow {
            display: inline-block !important;
        }

        /* What it does: Adjust typography on small screens to improve readability */
        .email-container p {
            font-size: 17px !important;
            line-height: 22px !important;
        }
    }

    </style>
    <!-- Progressive Enhancements : END -->

    <!-- What it does: Makes background images in 72ppi Outlook render at correct size. -->
    <!--[if gte mso 9]>
    <xml>
        <o:OfficeDocumentSettings>
            <o:AllowPNG/>
            <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml>
    <![endif]-->

</head>
<body width="100%" style="margin: 0; mso-line-height-rule: exactly;">
    <center style="width: 100%; text-align: left;">

        <!-- Visually Hidden Preheader Text : BEGIN -->
        <!--<div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">
            (Optional) This text will appear in the inbox preview, but not the email body. It can be used to supplement the email subject line or even summarize the email's contents. Extended text preheaders (~490 characters) seems like a better UX for anyone using a screenreader or voice-command apps like Siri to dictate the contents of an email. If this text is not included, email clients will automatically populate it using the text (including image alt text) at the start of the email's body.
        </div>-->
        <!-- Visually Hidden Preheader Text : END -->

        <!--
            Set the email width. Defined in two places:
            1. max-width for all clients except Desktop Windows Outlook, allowing the email to squish on narrow but never go wider than 680px.
            2. MSO tags for Desktop Windows Outlook enforce a 680px width.
            Note: The Fluid and Responsive templates have a different width (600px). The hybrid grid is more "fragile", and I've found that 680px is a good width. Change with caution.
        -->
        <div style="max-width: 680px; margin: auto;" class="email-container">
            <!--[if mso]>
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="680" align="center">
            <tr>
            <td>
            <![endif]-->

            <!-- Email Header : BEGIN -->
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="max-width: 680px;">
                <tr>
                    <td style="padding: 20px 0; text-align: center">
                        @if(isset($message))
                            <img src="{{ $message->embed(public_path() . '/images/sae-mail.png') }}" width="200" height="50" alt="alt_text" border="0" style="height: auto; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;"/>
                        @else
                            <img src="{{ asset('/images/sae-mail.png') }}" width="200" height="50" alt="alt_text" border="0" style="height: auto; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;"/>
                        @endif
                    </td>
                </tr>
            </table>
            <!-- Email Header : END -->

            <!-- Email Body : BEGIN -->
            @yield('content')
            <!-- Email Body : END -->
            <!--[if mso]>
            </td>
            </tr>
            </table>
            <![endif]-->
        </div>

        <!-- Full Bleed Background Section : BEGIN -->
        <table role="presentation" bgcolor="#222222" cellspacing="0" cellpadding="0" border="0" align="center" width="100%">
            <tr>
                <td valign="top" align="center">
                    <div style="max-width: 680px; margin: auto;" class="email-container">
                        <!--[if mso]>
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="680" align="center">
                        <tr>
                        <td>
                        <![endif]-->
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                            <tr>
                                <td style="padding: 40px; text-align: left; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #ffffff;">
                                    <p style="margin: 0;">Para mayor información se puede comunicar con las siguientes áreas, que gustosamente le ayudarán.</p>
                                    <div style="width: 100%; height: 100%">
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" align="center" width="100%" style="max-width:660px;">
                                            <tr>
                                                <td align="center" valign="top" style="font-size:0; padding: 10px 0;">
                                                    <!--[if mso]>
                                                    <table role="presentation" border="0" cellspacing="0" cellpadding="0" align="center" width="660">
                                                    <tr>
                                                    <td align="left" valign="top" width="330">
                                                    <![endif]-->
                                                    <div style="display:inline-block; margin: 0 -2px; width:100%; min-width:300px; max-width:300px; vertical-align:top;" class="stack-column">
                                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                                            <tr>
                                                                <td style="padding: 10px 10px 10px 0;">
                                                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="font-size: 14px;text-align: left;">
                                                                        <tr>
                                                                            <td>
                                                                                <p><b>POSTVENTA</b></p>
                                                                                <div><b>Asesor</b>: Rosa Sernaque</div>
                                                                                <div style="text-decoration: none !important;"><b>Correo</b>: postventa@centrosvirtuales.com</div>
                                                                                <div><b>Central</b>: 707-3500 <b>Anexo</b>: 301-302</div>
                                                                                <div><b>Celular</b>: 975453170 - 966019104</div>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <!--[if mso]>
                                                    </td>
                                                    <td align="left" valign="top" width="330">
                                                    <![endif]-->
                                                    <div style="display:inline-block; margin: 0 -2px; width:100%; min-width:300px; max-width:300px; vertical-align:top;" class="stack-column">
                                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                                            <tr>
                                                                <td style="padding: 10px 10px 10px 0px;">
                                                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="font-size: 14px;text-align: left;">
                                                                        <tr>
                                                                            <td>
                                                                                <p><b>ATENCIÓN AL CLIENTE - SURCO</b></p>
                                                                                <div><b>Asesor</b>: Vanesa Chavez</div> 
                                                                                <div><b>Correo</b>: pagos@centrosvirtuales.com</div> 
                                                                                <div><b>Central</b>: 707-3500 <b>Anexo</b>: 305-306</div>
                                                                                <div><b>Celular</b>: 975453170 - 966019104</div> 
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <!--[if mso]>
                                                    </td>
                                                    </tr>
                                                    </table>
                                                    <![endif]-->
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center" valign="top" style="font-size:0; padding: 10px 0;">
                                                    <!--[if mso]>
                                                    <table role="presentation" border="0" cellspacing="0" cellpadding="0" align="center" width="660">
                                                    <tr>
                                                    <td align="left" valign="top" width="330">
                                                    <![endif]-->
                                                    <div style="display:inline-block; margin: 0 -2px; width:100%; min-width:300px; max-width:300px; vertical-align:top;" class="stack-column">
                                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                                            <tr>
                                                                <td style="padding: 10px 10px 10px 0;">
                                                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="font-size: 14px;text-align: left;">
                                                                        <tr>
                                                                            <td>
                                                                                <p><b>ATENCIÓN AL CLIENTE - MIRAFLORES</b></p>
                                                                                <div><b>Asesor</b>: Mariluz Rondinel</div>
                                                                                <div><b>Correo</b>: marirondinel@centrosvirtuales.com</div>
                                                                                <div><b>Central</b>: 707-3500 <b>Anexo</b>: 307-308</div>
                                                                                <div><b>Celular</b>: 993812902 - 966019104</div>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <!--[if mso]>
                                                    </td>
                                                    <td align="left" valign="top" width="330">
                                                    <![endif]-->
                                                    <div style="display:inline-block; margin: 0 -2px; width:100%; min-width:300px; max-width:300px; vertical-align:top;" class="stack-column">
                                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                                            <tr>
                                                                <td style="padding: 10px 10px 10px 0px;">
                                                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="text-align: left;">
                                                                        <tr>
                                                                            <td style="font-size: 14px;">
                                                                                <p><b>ATENCIÓN AL CLIENTE - SAN ISIDRO</b></p>
                                                                                <div><b>Asesor</b>: Marisol Chavez</div> 
                                                                                <div><b>Correo</b>: atencionalcliente@centrosvirtuales.com</div> 
                                                                                <div><b>Central</b>: 707-3500 <b>Anexo</b>: 310</div>
                                                                                <div><b>Celular</b>: 982903593 - 966019104</div> 
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <!--[if mso]>
                                                    </td>
                                                    </tr>
                                                    </table>
                                                    <![endif]-->
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <!--[if mso]>
                        </td>
                        </tr>
                        </table>
                        <![endif]-->
                    </div>
                </td>
            </tr>
        </table>
        <!-- Full Bleed Background Section : END -->
    </center>
</body>
</html>