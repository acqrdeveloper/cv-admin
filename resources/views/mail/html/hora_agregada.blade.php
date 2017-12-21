@extends('mail.html.responsive')
@section('content')
<table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="max-width: 680px;" class="email-container">
    <tr>
        <td bgcolor="#ffffff">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td style="padding: 40px 40px 30px; text-align: center;">
                        <h1 style="margin: 0; font-family: sans-serif; font-size: 24px; line-height: 27px; color: #333333; font-weight: normal;">Sr(a). {{ $fullname }}</h1>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0 40px 40px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555; text-align: justify;">
                        <p style="margin: 0;">Súmate al Éxito confirma tu solicitud de horas extras</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0 40px 40px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555; text-align: center;">
                        <p style="margin: 0;"><b>Cantidad de Horas:</b> {{ $horas }}</p>
                        <p style="margin: 0;"><b>Tipo:</b> {{ $tipo }}</p>
                        <p style="margin: 0;"><b>Precio:</b> {{ $precio }} (*)</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0 40px 40px; font-family: sans-serif; font-size: 13px; line-height: 20px; color: #555555; text-align: justify;">
                        <p style="margin: 0;">* Este monto será agregado a tu próxima facturación</strong></p>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0 40px 40px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555; text-align: justify;">
                        <p style="margin: 0;">Saludos cordiales!<br><strong>Súmate al Éxito.</strong></p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
@endsection