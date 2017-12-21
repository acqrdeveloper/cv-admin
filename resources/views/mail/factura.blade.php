@extends('mail.base')
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
                        <p style="margin: 0;">
                            @if($vencido<0)
                            <strong>Súmate al Éxito</strong> le informa que su {{ $comprobante }} fue emitido
                            @elseif($vencido==0)
                            <strong>Súmate al Éxito</strong> le recuerda que HOY vence su {{ $comprobante }}. Cancela y evita que su servicio sea suspendido.
                            @else
                            <strong>Súmate al Éxito</strong> le recuerda que tiene una {{ $comprobante }} vencida. Cancele y evite la suspensión de su servicio y/o cargos por reconexión de su servicio.
                            @endif
                        </p>
                    </td>
                </tr>

                <!-- DETALLE DE LA FACTURA -->
                @if(!isset($nota))
                <tr>
                    <td style="padding: 0 40px 40px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555; text-align: center;">
                        <p style="margin: 0;"><b>Documento:</b> {{ $comprobante }} {{ $numero }}</p>
                        <p style="margin: 0;"><b>Fec. Vencimiento:</b> {{ $vencimiento }}</p>
                        @if(isset($monto))
                        <p style="margin: 0;"><b>Monto:</b> S/. {{ $monto }}</p>
                        @endif
                    </td>
                </tr>
                @if(isset($detalle) && count($detalle)>0)
                <tr>
                    <td style="padding: 0 40px 40px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555; text-align: center;">
                        <div style="border: 1px solid #f1f1f1;padding: 10px;"><b>Detalle del comprobante</b></div>
                        <table cellspacing="0" cellpadding="0" border="0" style="width:100%">                            
                            <tr style="border-bottom: 1px solid #f1f1f1;">
                                <th style="font-weight: bold;text-align: left;width:70%;">Descripción</th>
                                <th style="font-weight: bold;text-align: right;width:30%;">Precio Total (S/.)</th>
                            </tr>
                            @foreach($detalle as $det)
                            <tr style="border-bottom: 1px solid #f1f1f1;">
                                <td style="text-align: left;">{{ $det->descripcion }}</td>
                                <td style="text-align: right;">{{ number_format($det->precio,2) }}</td>
                            </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>
                @endif
                <!-- FIN DEL DETALLE DE LA FACTURA -->

                <tr>
                    <td style="padding: 0 40px 40px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555; text-align: justify;">
                        <p style="margin: 0;">Si desea descargar su factura o saber más detalle de su deuda, ingrese al <a href="{{ env('APP_URL') }}" target="_blank">sistema</a>.</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0 40px 40px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555; text-align: justify;">
                        <p style="margin: 0;">Cuando se acerquen al banco o realicen una transferencia le pedirá un código de cliente, ese código es su <strong>número RUC</strong> si es empresa o su <strong>DNI</strong> si es persona natural.</p>
                    </td>
                </tr>
                @endif
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