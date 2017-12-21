@extends('mail.base')
@section('content')
<table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="max-width: 680px;" class="email-container">
    <!-- 1 Column Text + Button : BEGIN -->
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
                        <p style="margin-bottom: 10px;">Centros Virtuales del Perú les comunica que a partir del día 01 de Diciembre, nuestra nueva sede (<a href="https://www.google.com/maps/place/Av.+Javier+Prado+Este+204,+Cercado+de+Lima+15023/@-12.0810744,-76.9688092,17z/data=!3m1!4b1!4m5!3m4!1s0x9105c655228210d3:0xc67efd411d0793d0!8m2!3d-12.0810797!4d-76.9666205">Av. Circunvalación del Golf 206-208 Interior 602 - Torre 3, Santiago de Surco</a>) abre sus puertas para que utilice los servicios contratos.</p>
                        <p style="margin-bottom: 10px;">Así mismo les comunicamos que nuestra sede de Manuel Olguin 335 of. 1205, seguirá atendiendo hasta el 31 de Diciembre.</p>
                        <p style"=margin-bottom: 10px;">En ambas oficinas recepcionarán las correspondencias que lleguen, informado a través de nuestro sistema el detalle de recepción de dichos documentos.</p>
                        <p style="margin-bottom: 40px;">Agradecemos su comprensión.</p>
                        <p style="margin-bottom: 10px;"><b>Administración de Centros Virtuales del Perú</b></p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <!-- 1 Column Text + Button : END -->

</table>
@endsection