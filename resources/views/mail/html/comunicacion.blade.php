@extends('mail.html.responsive')
@section('content')
<table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="max-width: 680px;" class="email-container">
    <!-- 1 Column Text + Button : BEGIN -->
    <tr>
        <td bgcolor="#ffffff">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td style="padding: 20px 40px 30px; text-align: center;">
                        <h1 style="margin: 0; font-family: sans-serif; font-size: 24px; line-height: 27px; color: #333333; font-weight: normal;">Sr(a). {{ $fullname }}</h1>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0 40px 40px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555; text-align: justify;">
                        <p style="margin-bottom: 10px;">Por medio del presente, Centros Virtuales del Perú EIRL les  comunica  que, con el fin de  mejorar el servicio de nuestra plataforma y aplicación, a partir del día Lunes 06 de Noviembre, se lanzará el nuevo sistema que le permitirá de  una forma simple y dinámica realizar sus reservas tanto en oficinas, auditorios y aulas de capacitación, haciendo más simple y fácil de administrar sus servicios.</p>
                        <p style="margin-bottom: 10px;">Por ejemplo, si ingresa a nuestra app, podrá observar que solo hay  6 OPCIONES, que de forma sencilla  guía a la tarea que desea ejecutar.</p>
						<p style="margin-bottom: 10px;"><b>Reserva</b> Permite reservar salas de reuniones, oficinas privadas y auditorios.</p>
						<p style="margin-bottom: 10px;"><b>Bandeja</b> Le permite enviar y recibir todo tipo de mensajes a diferentes áreas ya sea administrativa o atención al cliente. (Queja, sugerencia, solicitud de horas extras etc.)</p>
						<p style="margin-bottom: 10px;"><b>Reporte</b> Permite monitorear llamadas, ver facturas, correspondencia y recado.</p>
						<p style="margin-bottom: 10px;">Le recordamos que puede cambiar sus datos personales desde el panel de control, así mismo puedes acceder a nuestros manuales  de ayuda que te guiarán paso a paso a conseguir tus objetivos.</p><br>
						<p style="margin-bottom: 10px;">Para que puedan ingresar al nuevo panel lo deben hacer mediante la siguiente dirección:</p>
						<p style="margin-bottom: 10px;text-align: center;"><b><a href="https://app.sumatealexito.com" target="_blank">https://app.sumatealexito.com</a></b></p><br>
						<p style="margin-bottom: 10px;">Y desde su móvil  ENCUENTRANOS DESDE TU ANDROID (PLAY STORE) O IPHONE (APP STORE) COMO <b>SÚMATE AL ÉXITO</b></p>
						<p style="margin-bottom: 10px;"><b>Importante. Estas nuevas direcciones  anulan a las páginas anteriores.</b></p><br>
						<p style="margin-bottom: 10px;">Si tiene dudas o inconvenientes puede comunicar con los siguientes números que gustosamente le ayudarán.</p><br>
						<p style="margin-bottom: 10px;">Central Telefónica - 707-3500. (Celulares. 958801152, 982973059, 975453176, 975453170)</p><br>
						<p style="margin-bottom: 10px;">Esperamos que estos cambios  sean de mucha utilidad.</p>
						<p style="margin-bottom: 10px;">Atentamente</p><br>
						<p style="margin-bottom: 10px;"><b>Administración</b></p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <!-- 1 Column Text + Button : END -->

</table>
@endsection