@section('css')
    <style>
        body {
            font-size: 12px !important;
            font-family: "Helvetica", serif !important;
        }

        td.sangria {
            vertical-align: baseline;
            padding-right: 1em;
        }

        td.space {
            vertical-align: baseline;
            padding-right: 0.10em;
        }

        .add-page-before {
            page-break-before: always;
        }

        .add-page-after {
            page-break-after: always;
        }

        .footer, .push {
            height: 4em;
        }

        .table > tbody > tr > td, .table > tbody > tr > th, .table > tfoot > tr > td, .table > tfoot > tr > th, .table > thead > tr > td, .table > thead > tr > th {
            border-top: 1px solid transparent;
            padding: 0 0 0 0;
        }
    </style>
@endsection
@section('firma')
    <div class="col-md-12">
        <table class="table">
            <tr>
                <td>
                    <br>
                </td>
            </tr>
            <tr>
                <td width="50%"></td>
                <td width="50%" class="text-right"><?=DATE_FIRMA ?></td>
            </tr>
            <tr>
                <td>
                    <br>
                    <br>
                    <br>
                </td>
            </tr>
            <tr>
                <td width="50%" class="text-center">
                    <p><b>............................................................................</b></p>
                    <span><b>Centros Virtuales del Perú E.I.R.L.</b></span><br>
                    <span>RUC N° 20546951015</span>
                </td>
                <td width="50%" class="text-center">
                    <p><b>............................................................................</b></p>
                    <span><b>El Comitente</b></span>
                </td>
            </tr>
        </table>
    </div>
@endsection
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    @yield('css')
    <title>CV | contrato</title>
</head>
<body>
<p class="text-center"><u><b>CONTRATO DE LOCACION DE SERVICIOS<?=$setTitle?></b></u></p>
<p class="text-justify">
    Conste por el presente documento el Contrato de Prestación de Servicios que celebran de una
    parte, Centros Virtuales del Perú E.I.R.L. con RUC Nro. 20546951015, debidamente representada por su titular
    Gerente, el Sr. Oscar Gonzalo Salvador Ruiz, con DNI. Nro. 48632827 con domicilio en Av. Dos de Mayo Nro. 516,
    interior Nro. 201, distrito de Miraflores (en adelante CENTROS VIRTUALES); y de otra parte EL COMITENTE, cuyos datos
    de identificación, representación y domicilio aparecen en el <b>ANEXO #1</b> que forma parte integrante del presente
    contrato, el
    mismo que se suscribe en los términos y condiciones siguientes:</p>
<br>
<p><u><b>PRIMERO: Antecedentes</b></u></p>
@if($isCoworking)
    <?php
    $anexo = "<b>ANEXO #2</b>";
    $txt = '"OFICINAS EN COWORKING"';
    ?>
@else
    <?php
    $anexo = "<b>ANEXO #1.1</b>";
    $txt = '"OFICINAS VIRTUALES"';
    ?>
@endif
<p class="text-justify">
    CENTROS VIRTUALES, es una persona jurídica que dentro del ámbito de su objeto social, otorga a
    terceros la cesión en uso temporal de oficinas y espacios de trabajo, brindando servicios complementarios bajo la
    modalidad de <?=$txt?></p>
<p class="text-justify">
    Conforme a lo indicado en el párrafo anterior, EL COMITENTE gozará del uso temporal de un
    espacio físico asignado de acuerdo a la disponibilidad de CENTROS VIRTUALES así como servicios complementarios, de
    recepción de llamadas telefónicas, correspondencia, uso de áreas de directorio, oficinas privadas y otros servicios
    que se brindarán de conformidad con el plan contratado y que se encuentran precisados en el <?=$anexo?></p>
<p class="text-justify">Para el cumplimiento de la prestación a su cargo, CENTROS VIRTUALES en la actualidad ofrece los
    servicios anteriormente indicados en las siguientes oficinas:</p>
@if($isCoworking)
    <b>OFICINA PARA USO DE COWORKING</b>
    <ul>
        <li>Av. 02 de Mayo Nro. 516, Oficina Nro. 201 - Miraflores.</li>
    </ul>
    <b>OFICINA PARA USO DE SALAS DE REUNIONES Y DIRECTORIOS</b>
    <ul>
        <li>Calle Elías Aguirre 180, oficina 401 Miraflores.</li>
        <li>Av. 02 de Mayo Nro. 516, Oficina Nro. 201 Miraflores.</li>
        <li>Av. Manuel Olguín 335, Oficina 1205 - Surco.</li>
    </ul>
@else
    <ul>
        <li>Av. 02 de Mayo Nro. 516, Oficina Nro. 201 - Miraflores.</li>
        <li>Calle Elias Aguirre180 - Miraflores.</li>
        <li>Av. Manuel Olguín 335, Oficina 1205 - Surco.</li>
    </ul>
@endif
<p class="text-justify">CENTROS VIRTUALES podrá ampliar en el futuro sus servicios a otras oficinas de su propiedad o
    bajo su conducción, para lo cual bastará que este hecho sea comunicado a EL COMITENTE en su oportunidad.</p>
<br>
<p><u><b>SEGUNDO: Contraprestación y Garantía</b></u></p>
<p class="text-justify">
    En contraprestación por el servicio prestado, EL COMITENTE deberá abonar a favor de CENTROS
    VIRTUALES una contraprestación de periodicidad mensual cuyo importe se establece conforme al plan contratado y cuya
    fecha de pago se precisa en el ' . $anexoTxt . ', conforme al ciclo de facturación convenido por las partes
    contratantes.</p>
<p class="text-justify">
    Así mismo, se deja constancia que EL COMITENTE deberá abonar el monto correspondiente a un mes de servicio en
    calidad de garantía, suma de dinero que deberá ser devuelta a EL COMITENTE al finalizar el plazo del presente
    contrato sin devengue de intereses y previo descuentos de cualquier deuda, concepto u obligación pendiente de pago
    por parte de EL COMITENTE que se derive del presente contrato.
</p>
<p class="text-justify">
    El importe entregado en garantía será retenido en su totalidad por CENTROS VIRTUALES en calidad de compensación por
    lucro cesante y se procederá el ingreso de la central de riesgo por el monto de todos los pagos adeudados en el caso
    que EL COMITENTE decidiera dar por terminado el contrato antes del plazo convenido por ambas partes o cuando el
    presente contrato sea resuelto por cualquiera de las causales establecidas en la cláusula sexta.
</p>
<p class="text-justify">
    Así mismo, EL COMITENTE, no podra cambiarse de un plan mayor a un plan menor en su primer contrato, deberá respetar
    el plan por el que ha contratado, de lo contrario se procederá a retener la totalidad de la garantía.CENTROS
    VIRTUALES podrá exigir un importe de garantía mayor al indicado en caso así lo considere conveniente.
</p>
<p class="text-justify">CENTROS VIRTUALES podrá exigir un importe de garantía mayor al indicado en caso así lo considere
    conveniente.</p>
<br>
<p><b><u>TERCERO: Características Generales del Servicio</u></b></p>
<table>
    <tr>
        <td class="text-justify sangria"><b>A)</b></td>
        <td class="text-justify">
            CENTROS VIRTUALES se limita a brindar espacios de oficinas temporales y servicios complementarios conforme a
            lo indicado en la cláusula primera, no siendo partícipe de la actividad comercial y/o profesional que
            desarrolla EL COMITENTE, en tal sentido, no asume responsabilidad ante terceros por los daños, perjuicios o
            actos dolosos desarrollados por éste, incluyendo las obligaciones asumidas con los trabajadores o personal
            contratado por EL COMITENTE.
        </td>
    </tr>
    <tr>
        <td><br></td>
    </tr>
    <tr>
        <td class="text-justify sangria"><b>B)</b></td>
        <td class="text-justify">
            EL COMITENTE en el ejercicio de su actividad comercial y/o profesional deberá dar estricto cumplimiento a
            las leyes vigentes, en consecuencia, se encuentra expresamente prohibido hacer uso de los servicios que
            brinda CENTROS VIRTUALES para propósitos ilícitos, que configuren delito o atentatorios de la moral y buenas
            costumbres, en caso contrario, CENTROS VIRTUALES procederá a la inmediata suspensión del servicio y la
            resolución del contrato conforme a lo establecido en la cláusula sexta del presente documento, sin perjuicio
            de iniciar las acciones legales correspondientes.
        </td>
    </tr>
    <tr>
        <td><br></td>
    </tr>
@if($isCoworking) <!-- si es coworking -->
    <tr>
        <td class="text-justify sangria"><b>C)</b></td>
        <td class="text-justify">
            EL COMITENTE utilizará las oficinas y gozará de los demás servicios complementarios de acuerdo al plan
            contratado indicado en el ANEXO2, para tal efecto, podrá introducir en las oficinas de CENTROS VIRTUALES,
            únicamente computadoras portátiles de uso personal, afiches, documentos, material publicitario y/o material
            de trabajo que pueda llevar consigo, quedando expresamente establecido, que la custodia de dichos bienes son
            de exclusiva responsabilidad de EL COMITENTE, en tal sentido, CENTROS VIRTUALES no se responsabilizará por
            el olvido o extravío de dichos bienes.
        </td>
    </tr>
    <tr>
        <td><br></td>
    </tr>
    <tr>
        <td class="text-justify sangria"><b>D)</b></td>
        <td class="text-justify">
            El COMITENTE deberá respetar el espacio contratado, por tanto no está permitido que el cliente después de
            elegir su espacio, realice su cambio sin autorización.
        </td>
    </tr>
    <tr>
        <td><br></td>
    </tr>
    <tr>
        <td class="text-justify sangria"><b>E)</b></td>
        <td class="text-justify">
            EL COMITENTE es responsable de los daños o pérdidas que pudieran ocasionarse a las instalaciones y bienes de
            propiedad de CENTROS VIRTUALES por actos propios o de terceros que ingresen a las oficinas autorizados por
            EL COMITENTE, debiendo éste reponer el valor de los bienes afectados dentro de un plazo de 24 horas de
            verificado el hecho, esta misma disposición es aplicable en caso los daños se produzcan en las áreas comunes
            del edificio. e) EL COMITENTE deberá tratar con respeto y acatar las disposiciones y/o directivas que sean
            comunicadas por el personal de CENTROS VIRTUALES, para el uso de los servicios, bienes e instalaciones de
            las oficinas.
        </td>
    </tr>
    <tr>
        <td><br></td>
    </tr>
    <tr>
        <td class="text-justify sangria"><b>F)</b></td>
        <td class="text-justify">
            El horario establecido para el uso de los servicios es de lunes a viernes de 8.00 a.m. hasta las 6.30 p.m. y
            los días sábados de 8.00 a.m. a 2.00 p.m.
        </td>
    </tr>
    <tr>
        <td><br></td>
    </tr>
    <tr>
        <td class="text-justify sangria"><b>G)</b></td>
        <td class="text-justify">
            Las horas que brindan cada plan contratado, deberán respetarse, no son transferibles ni acumulables.
        </td>
    </tr>
    <tr>
        <td><br></td>
    </tr>
    <tr>
        <td class="text-justify sangria"><b>H)</b></td>
        <td class="text-justify">
            Las políticas de uso de los servicios están descritas en el ANEXO2.
        </td>
    </tr>
    <tr>
        <td><br></td>
    </tr>
    <tr>
        <td class="text-justify sangria"><b>I)</b></td>
        <td class="text-justify">
            El uso de la terraza y salas comunes es exclusivamente para los clientes de Centros virtuales y está SUJETO
            A DISPONIBILIDAD, por tanto se permitirá el acceso de dos socios por empresa.
        </td>
    </tr>
@else <!-- si es normal -->
    <tr>
        <td><br></td>
    </tr>
    <tr>
        <td class="text-justify sangria"><b>C)</b></td>
        <td class="text-justify">
            EL COMITENTE utilizará las oficinas en forma temporal y gozará de los demás servicios complementarios de
            acuerdo al plan contratado indicado en el ANEXO1, para tal efecto, podrá introducir en las oficinas de
            CENTROS VIRTUALES, únicamente computadoras portátiles de uso personal, afiches, documentos, material
            publicitario y/o material de trabajo que pueda llevar consigo luego de utilizar las oficinas y/o salas de
            reuniones, quedando expresamente establecido, que la custodia de dichos bienes son de exclusiva
            responsabilidad de EL COMITENTE, en tal sentido, CENTROS VIRTUALES no se responsabilizará por el olvido o
            extravío de dichos bienes.
        </td>
    </tr>
    <tr>
        <td><br></td>
    </tr>
    <tr>
        <td class="text-justify sangria"><b>D)</b></td>
        <td class="text-justify">
            EL COMITENTE es responsable de los daños o pérdidas que pudieran ocasionarse a las instalaciones y bienes de
            propiedad de CENTROS VIRTUALES por actos propios o de terceros que ingresen a las oficinas autorizados por
            EL COMITENTE, debiendo éste reponer el valor de los bienes afectados dentro de un plazo de 24 horas de
            verificado el hecho, esta misma disposición es aplicable en caso los daños se produzcan en las áreas comunes
            del edificio.
        </td>
    </tr>
    <tr>
        <td><br></td>
    </tr>
    <tr>
        <td class="text-justify sangria"><b>E)</b></td>
        <td class="text-justify">
            EL COMITENTE deberá tratar con respeto y acatar las disposiciones y/o directivas que sean comunicadas por el
            personal de CENTROS VIRTUALES, para el uso de los servicios, bienes e instalaciones de las oficinas.
        </td>
    </tr>
    <tr>
        <td><br></td>
    </tr>
    <tr>
        <td class="text-justify sangria"><b>F)</b></td>
        <td class="text-justify">
            Durante la vigencia del presente contrato y con una posterioridad de hasta 06 meses de su culminación EL
            COMITENTE ni CENTROS VIRTUALES podrán contratar personal que haya formado parte del staff laboral de la otra
            empresa, en caso contrario la empresa infractora de esta disposición deberá pagar a favor de la otra una
            penalidad ascendente a la suma de S/. 1500.00
        </td>
    </tr>
    <tr>
        <td><br></td>
    </tr>
    <tr>
        <td class="text-justify sangria"><b>G)</b></td>
        <td class="text-justify">
            El horario establecido para el uso de los servicios es de lunes a viernes de 8.30 a.m. hasta las 10.00 p.m.
            y los días sábados de 8.30 a.m. a 7.00 p.m.
        </td>
    </tr>
    <tr>
        <td><br></td>
    </tr>
    <tr>
        <td class="text-justify sangria"><b>H)</b></td>
        <td class="text-justify">
            Las horas que brindan cada plan contratado, deberán respetarse, no son transferibles ni acumulables.
        </td>
    </tr>
    <tr>
        <td><br></td>
    </tr>
    <tr>
        <td class="text-justify sangria"><b>I)</b></td>
        <td class="text-justify">
            EL COMITENTE deberá realizar sus reservas mediante el panel de cliente, si desea reservar horas adicionales,
            deberá pagar el monto de 30 nuevos soles, y la solicitud tendrá que hacerla por su sesión del panel de
            cliente o enviando un correo al área de atención al cliente. La eliminación de reservas podrá generar desde
            su panel de cliente con 24 horas de anticipación a lo agendado.
        </td>
    </tr>
    <tr>
        <td><br></td>
    </tr>
    <tr>
        <td class="text-justify sangria"><b>J)</b></td>
        <td class="text-justify">
            El uso de cochera y/o proyector EL COMITENTE solo podrá utilizar durante su reserva y podrá contar con un
            estacionamiento y/o un equipo proyector. Si no incluye en su plan se le adicionará 5 soles por hora y cada
            uno de los servicios en su próximo pago.
        </td>
    </tr>
    <tr>
        <td><br></td>
    </tr>
    <tr>
        <td class="text-justify sangria"><b>K)</b></td>
        <td class="text-justify">
            Si EL COMITENTE desea realizar una modificación en su mensaje de voz de la central telefónica, el pago ha
            realizar es de 45 soles.
        </td>
    </tr>
    <tr>
        <td><br></td>
    </tr>
    <tr>
        <td class="text-justify sangria"><b>L)</b></td>
        <td class="text-justify">
            La correspondencia será de peso liviano, identificado por el nombre de la empresa y/o representante. El
            comitente Solo tendrá un plazo máximo de 30 días (iniciando la fecha de recepción) para su entrega, de lo
            contrario se procederá al reciclaje de la misma.
        </td>
    </tr>
    <tr>
        <td><br></td>
    </tr>
    <tr>
        <td class="text-justify sangria"><b>M)</b></td>
        <td class="text-justify">
            El horario de atención está establecido en el literal G de la clausula tercera del presente contrato. Si EL
            COMITENTE es un estudio de abogados deberá ingresar todos sus procesos legales en el rubro de gestión en el
            panel de cliente vía web, de lo contrario no será recepciónada las notificaciones como correspondencia.
        </td>
    </tr>
    <tr>
        <td><br></td>
    </tr>
    <tr>
        <td class="text-justify sangria"><b>N)</b></td>
        <td class="text-justify">
            El uso de la terraza y salas comunes es exclusivamente para los clientes de Centros virtuales y está SUJETO
            A DISPONIBILIDAD, siempre y cuando exista una reserva de por medio durante el día que desea utilizar dicho
            espacio. El acceso permitido a la terraza es solo para dos socios por empresa con el horario establecido en
            el presente contrato.
        </td>
    </tr>
    @endif
</table>
<br>
@if($dataEmpresa['preferencia_fiscal'] == 'SI')
    @if($isCoworking)
        <?php
        $anexo = "<b>ANEXO #3</b>";
        ?>
    @else
        <?php
        $anexo = "<b>ANEXO #2</b>";
        ?>
    @endif
@endif
<p><u><b>CUARTO: Alquiler de Auditorio, Terraza y Sala de Capacitacion</b></u></p>
<table>
    <tr>
        <td class="sangria">-</td>
        <td class="text-justify">
            En cuanto al alquiler de auditorio, terraza y salas de capacitación el comitente no podrá anular las
            reservas cuando ya estén realizadas. Solo se aceptara que sea cancelada la reserva siempre y cuando sean
            anuladas con quince días de anticipación.
        </td>
    </tr>
    <tr>
        <td><br></td>
    </tr>
    <tr>
        <td class="sangria">-</td>
        <td class="text-justify">
            El comitente a fin de confirmar la reserva del auditorio, deberá realizar previo a ello el pago
            correspondiente al alquiler, a fin que de esa forma confirme su reserva.
        </td>
    </tr>
    <tr>
        <td><br></td>
    </tr>
    <tr>
        <td class="sangria">-</td>
        <td class="text-justify">
            El comitente deberá respetar el horario de reserva, por tanto deberá ser puntual con el horario de ingreso y
            salida.
        </td>
    </tr>
    <tr>
        <td><br></td>
    </tr>
    <tr>
        <td class="sangria">-</td>
        <td class="text-justify">
            Debe respetar el aforo del salón que es un máximo de 70 personas.
        </td>
    </tr>
    <tr>
        <td><br></td>
    </tr>
    <tr>
        <td class="sangria">-</td>
        <td class="text-justify">
            Si el comitente trae su publicidad, sea banners o afiches se deben exhibir en el espacio alquilado del
            auditorio, no se permite hacerlo en el área de recepción revise el <?=$anexo?>.
        </td>
    </tr>
</table>
<br>
<p><u><b>QUINTO: Respetar el Aforo</b></u></p>
<p class="text-justify">
    El comitente deberá respetar el aforo de las oficinas de Centros Virtuales (ubicadas en las dos Sedes), que el panel
    de control indique cuando realice su reserva, correspondiente a salas de reuniones, salas de capacitaciones y
    auditorio.
</p>
<br>
<p><u><b>SEXTO: Domicilio Comercial</b></u></p>
<p class="text-justify">
    Como parte de los servicios que brinda CENTROS VIRTUALES, se permite a EL COMITENTE señalar domicilio comercial en
    las oficinas materia del presente contrato, en tal sentido, se deja constancia que éste domicilio servirá para
    efectos de recibir correspondencia de carácter comercial y/o profesional por los servicios y la actividad que
    desarrolla EL COMITENTE, pactándose expresamente que el domicilio indicado no podrá ser utilizado en procesos
    legales, judiciales, administrativos, investigaciones policiales y/o fiscales seguidas contra el propio COMITENTE,
    en cuyo caso, CENTROS VIRTUALES queda autorizado a suspender este servicio en forma unilateral e informar a la
    entidad y/o autoridad competente que el domicilio indicado no corresponde a EL COMITENTE, así como a iniciar las
    acciones legales a que hubiere lugar.
</p>
<br>
@if($isCoworking)
    <?php
    $anexo = "<b>ANEXO #3</b>";
    ?>
@else
    <?php
    $anexo = "<b>ANEXO #2</b>";
    ?>
@endif
<p><u><b>SEPTIMO: Domicilio Fiscal</b></u></p>
<p class="text-justify">
    Para efectos de la correspondencia y/o notificaciones de carácter tributario que sean remitidas a EL COMITENTE,
    CENTROS VIRTUALES previa evaluación, podrá otorgar el servicio de domicilio fiscal, no siendo responsable del
    cumplimiento de las obligaciones tributarias de EL COMITENTE, en tal sentido, la contratación de este servicio está
    sujeta a las condiciones y términos precisados en el <?=$anexo?> que una vez suscrito por los contratantes,
    formará parte integrante del presente contrato.
</p>
<p class="text-justify">
    Centros Virtuales no permite que el comitente utilice la dirección de la empresa como dirección fiscal para
    constituir su empresa, si el Comitente hace caso omiso a lo indicado, deberá pagar una penalidad a favor de la
    empresa y a consecuencia de tal accionar se resolverá el contrato.
</p>
<br>
<p><u><b>OCTAVO: Cláusula resolutoria expresa</b></u></p>
<p class="text-justify">
    CENTROS VIRTUALES podrá, en forma unilateral proceder a la resolución extrajudicial del presente contrato cuando se
    verifiquen cualquiera de las circunstancias que se indican a continuación:
</p>
<table>
    <tr>
        <td class="sangria"><b>A)</b></td>
        <td class="text-left">Por incumplimiento en el pago de la contraprestación indicada en la cláusula segunda
            del presente contrato dentro del plazo convenido por las partes.
        </td>
    </tr>
    <tr>
        <td><br></td>
    </tr>
    @if($isCoworking)
        <tr>
            <td class="sangria"><b>B)</b></td>
            <td class="text-justify">
                Por incumplimiento de las obligaciones y/o restricciones establecidas en los literales "b", "c" y "d" de
                la cláusula tercera del presente contrato.
            </td>
        </tr>
    @else
        <tr>
            <td class="sangria"><b>B)</b></td>
            <td class="text-justify">
                Por incumplimiento de las obligaciones y/o restricciones establecidos en los literales "b", "c", "d" y
                "e" de la cláusula tercera del presente contrato.
            </td>
        </tr>
    @endif
    <tr>
        <td><br></td>
    </tr>
    <tr>
        <td class="sangria"><b>C)</b></td>
        <td class="text-justify">
            Por incumplimiento de la obligación establecida en la cláusula cuarta del presente contrato.
        </td>
    </tr>
    <tr>
        <td><br></td>
    </tr>
    <tr>
        <td class="sangria"><b>D)</b></td>
        <td class="text-justify">
            Por ofender o ultrajar con palabras, gestos o vías de hecho e imputar cargos sin fundamento alguno a
            miembros del personal, funcionario o trabajador de CENTROS VIRTUALES.
        </td>
    </tr>
    <tr>
        <td><br></td>
    </tr>
    <tr>
        <td class="sangria"><b>E)</b></td>
        <td>
            Por utilizar la dirección de CENTROS VIRTUALES como dirección fiscal para constituir su empresa.
        </td>
    </tr>
    <tr>
        <td><br></td>
    </tr>
    <tr>
        <td class="sangria"><b>F)</b></td>
        <td>
            Por los reiterados memorándum que reciba el Comitente por no respetar las normas de convivencia.
        </td>
    </tr>
</table>
<br>
<p class="text-justify">
    Así mismo, CENTROS VIRTUALES queda en libertad de proceder a la resolución unilateral y extrajudicial del presente
    contrato, suspendiendo todo servicio a favor de EL COMITENTE, si éste realiza prácticas empresariales y/o
    profesionales fuera del marco legal o que sean moralmente cuestionables, lo cual quedará a entera discrecionalidad
    de CENTROS VIRTUALES.
</p>
<p class="text-justify">
    Para proceder a la resolución del contrato por cualquiera de las causales anteriormente señaladas, bastará que
    CENTROS VIRTUALES comunique por escrito su decisión de valerse de esta cláusula conforme a lo establecido en el
    artículo 1430 del vigente Código Civil, pudiendo cursar la comunicación escrita a los domicilios indicados en el <b>ANEXO
        #1</b>. La resolución contractual operará de pleno derecho al día siguiente de recibida la comunicación
    escrita por parte de EL COMITENTE.
</p>
<br>
<p><u><b>NOVENO: Libre Competencia en el Mercado</b></u></p>
<p class="text-justify">
    CENTROS VIRTUALES no asumirá responsabilidad de ningún tipo respecto al desempeño de las actividades empresariales
    de sus clientes en el mercado, asimismo, CENTROS VIRTUALES cuenta con total libertad para contratar con empresas y
    clientes que realicen cualquier actividad comercial, siempre y cuando esta se encuentre dentro de lo previsto por la
    ley y las buenas costumbres.
</p>
<p class="text-justify">
    EL COMITENTE al momento de suscribir el presente contrato acepta y es de pleno conocimiento que en CENTROS VIRTUALES
    puede existir una persona natural o jurídica que realice la misma actividad económica en que desarrolla sus
    actividades empresariales, sin que esto constituya un acto de mala fe o contrario a libre competencia existente en
    el mercado y sobre el cual CENTROS VIRTUALES no tiene ningún tipo de injerencia ni preferencia o exclusividad,
    quedando bajo responsabilidad absoluta del COMITENTE su desenvolvimiento en el mercado dentro de los márgenes de
    libre competencia permitidos por nuestra legislación.
</p>
<br>
<p><u><b>DECIMO: Integridad del mobiliario</b></u></p>
<p class="text-justify">
    La integridad del mobiliario, equipos de cómputo, instalaciones, software y demás bienes al interior de las oficinas
    de CENTROS VIRTUALES son de exclusiva propiedad de ésta, y son cedidos en uso temporalmente a favor de EL COMITENTE
    para permitir que éste desarrolle su actividad comercial y/o profesional, no pudiendo estos bienes ser afectados o
    gravados por reclamos, procesos, cobranzas y en general cualquier acción legal, judicial, extrajudicial y/o
    administrativa que se interponga contra EL COMITENTE.
</p>
<br>
<p><u><b>ONCEAVO: Firma del Contrato y sus Anexos</b></u></p>
@if($isCoworking)
    <p class="text-justify">
        El cliente se compromete a firmar cada página del contrato, así como también cada página de sus anexos adjunto
        al presente, como muestra de conformidad de cada clausula y anexo señalado.En cuanto al <b>ANEXO #4</b> que se
        indica como carta de compromiso, el comitente deberá observar cada norma de convivencia, deberá darle fiel y
        riguroso cumplimiento a punto señalado por la empresa.
    </p>
@else
    <p class="text-justify">
        El cliente se compromete a firmar cada página del contrato, así como también cada página de sus anexos adjunto
        al presente, como muestra de conformidad de cada clausula y anexo señalado.
    </p>
@endif
<br>
<p><u><b>DOCEAVO: Confidencialidad</b></u></p>
<p class="text-justify">
    Los términos de este Contrato son confidenciales. Ni CENTROS VIRTUALES ni EL COMITENTE deben divulgarlos sin la
    autorización de la otra parte a menos que la ley o una autoridad competente lo requieran. Esta obligación continuará
    después de la finalización de este Contrato.
</p>
<br>
@if($isCoworking) <!-- si es coworking -->
<?php $subtitle = ' Aplicación supletoria'?>
@else
    <?php $subtitle = ' Acuerdos Generales con el Cliente'?>
@endif
<p><u><b>TRECEAVO:<?=$subtitle?></b></u></p>
@if($isCoworking)
    <p class="text-justify">
        En todo lo no previsto en el presente documento, será de aplicación las normas pertinentes del vigente Código
        Civil,
        normas concordantes y conexas.
    </p>
@endif
<table>
    <tr>
        <td class="sangria"><b>-</b></td>
        <td class="text-justify">
            Centros Virtuales es libre de abrir sucursales en lugares empresariales, como también tiene la facultad de
            cerrar sus oficinas cuando lo crea conveniente. El cliente será informado de estos cambios con 1 mes de
            anticipación, así mismo Centros Virtuales no se hace responsable por publicidad u otro servicio que
            considere el Cliente que ha sido afectado por el cambio.
        </td>
    </tr>
    <tr>
        <td><br></td>
    </tr>
    <tr>
        <td class="sangria"><b>-</b></td>
        <td class="text-justify">
            Centros Virtuales no otorga Licencia de funcionamiento a sus clientes, la Licencia de funcionamiento que
            brinda la Municipalidad de Miraflores es para el único uso de la empresa.
        </td>
    </tr>
    <tr>
        <td><br></td>
    </tr>
    <tr>
        <td class="sangria"><b>-</b></td>
        <td class="text-justify">
            El cliente acepta el cambio de dirección de la empresa en sus tres Sedes (2 Sedes en Miraflores y una Sede
            en Surco), para el respectivo cambio se le informara con un mes de anticipación.
        </td>
    </tr>
    <tr>
        <td><br></td>
    </tr>
    <tr>
        <td class="sangria"><b>-</b></td>
        <td class="text-justify">
            El cliente acepta que la empresa cambie de personal de trabajo (Secretaria), cada tiempo que la empresa lo
            requiera, con el fin de brindarle un mejor servicio al cliente.
        </td>
    </tr>
    <tr>
        <td><br></td>
    </tr>
    <tr>
        <td class="sangria"><b>-</b></td>
        <td class="text-justify">
            El cliente no podrá brindar sus servicios profesionales al personal de trabajo de la empresa Centros
            Virtuales, durante la vigencia de su contrato deberá mantener con el personal una relación contractual con
            mucha ética y respeto mutuo.
        </td>
    </tr>
</table>
@if(!$isCoworking) <!-- si es normal -->
{{--add page--}}
<div class="add-page-before">
    <p><u><b>CATORCEAVO: Aplicación Supletoria</b></u></p>
    <p class="text-justify">
        En todo lo no previsto en el presente documento, será de aplicación las normas pertinentes del vigente Código
        Civil, normas concordantes y conexas.
    </p>
    <br>
    <?php $title = 'QUINCEAVO: '?>
    <p><u><b><?= $title?>Competencia territorial</b></u></p>
    <p class="text-justify">
        Para cualquier controversia derivada del contenido, interpretación y/o aplicación del presente contrato, las
        partes
        se someten a la jurisdicción y competencia de los Jueces y Tribunales del distrito Judicial de Lima, haciendo
        expresa renuncia al fuero de sus domicilios.
    </p>
    @yield('firma')
</div>

@else

    <br>
    <br>
    <?php $title = 'CATORCEAVO: '?>
    <p><u><b><?= $title?>Competencia territorial</b></u></p>
    <p class="text-justify">
        Para cualquier controversia derivada del contenido, interpretación y/o aplicación del presente contrato, las
        partes
        se someten a la jurisdicción y competencia de los Jueces y Tribunales del distrito Judicial de Lima, haciendo
        expresa renuncia al fuero de sus domicilios.
    </p>
    @yield('firma')

@endif
{{--add page--}}
<div class="add-page-before">
    <p class="text-center"><b><u>ANEXO #1</u></b></p>
    <p></p>
@if(!$isCoworking) <!-- si es normal -->
    <p class="text-center"><b>VIRTUAL (<?='S/.' . $dataEmpresa['precio']?> MENSUALES)</b></p>
    <p></p>
    @endif
    <table class="table">
        <tr>
            <td class="text-left" colspan="3">
                <p>
                    <b><u>1.- DATOS DEL COMITENTE</u></b>
                </p>
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-left">NOMBRE Y/O RAZON SOCIAL</td>
            <td width="5%" class="text-center">:</td>
            <td width="60%">
                <div><?=$COMITENTE['razon_social']?></div>
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-left">RUC</td>
            <td width="5%" class="text-center">:</td>
            <td width="60%"><?=$COMITENTE['ruc']?></td>
        </tr>
        <tr>
            <td width="35%" class="text-left">ACTIVIDAD O GIRO DEL NEGOCIO</td>
            <td width="5%" class="text-center">:</td>
            <td width="60%"><?=$COMITENTE['actividad']?></td>
        </tr>
        <tr>
            <td width="35%" class="text-left">DOMICILIO</td>
            <td width="5%" class="text-center">:</td>
            <td width="60%"><?=$COMITENTE['domicilio']?></td>
        </tr>
        <tr>
            <td width="35%" class="text-left">
                <small>PODERES INSCRITOS</small>
            </td>
            <td width="5%" class="text-center">:</td>
            <td width="60%"><?=$COMITENTE['poderes']?></td>
        </tr>
    </table>
    <table class="table">
        <tr>
            <td class="text-left" colspan="3">
                <p>
                    <b><u>2.- DATOS DEL REPRESENTANTE LEGAL</u></b>
                </p>
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-left">NOMBRES Y APELLIDOS</td>
            <td width="5%" class="text-center">:</td>
            <td width="60%">
                <div><?=$REPRESENTANTE['nombre_apellido']?></div>
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-left">DOCUMENTO DE IDENTIDAD</td>
            <td width="5%" class="text-center">:</td>
            <td width="60%"><?=$REPRESENTANTE['dni']?></td>
        </tr>
        <tr>
            <td width="35%" class="text-left">DOMICILIO</td>
            <td width="5%" class="text-center">:</td>
            <td width="60%"><?=$REPRESENTANTE['domicilio']?></td>
        </tr>
        <tr>
            <td width="35%" class="text-left">EMAIL</td>
            <td width="5%" class="text-center">:</td>
            <td width="60%"><?=$REPRESENTANTE['email']?></td>
        </tr>
        <tr>
            <td width="35%" class="text-left">TELEFONO</td>
            <td width="5%" class="text-center">:</td>
            <td width="60%"><?=$REPRESENTANTE['telefono']?></td>
        </tr>
    </table>
    <table class="table">
        <tr>
            <td class="text-left" colspan="3">
                <p>
                    <b><u>3.- DATOS DEL CLIENTE ENCARGADO DE LOS PAGOS</u></b>
                </p>
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-left">NOMBRES Y APELLIDOS</td>
            <td width="5%" class="text-center">:</td>
            <td width="60%">
                <div><?=$CLIENTE_ENCARGADO['nombre_apellido']?></div>
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-left">DOCUMENTO DE IDENTIDAD</td>
            <td width="5%" class="text-center">:</td>
            <td width="60%"><?=$CLIENTE_ENCARGADO['dni']?></td>
        </tr>
        <tr>
            <td width="35%" class="text-left">DOMICILIO</td>
            <td width="5%" class="text-center">:</td>
            <td width="60%"><?=$CLIENTE_ENCARGADO['domicilio']?></td>
        </tr>
        <tr>
            <td width="35%" class="text-left">EMAIL</td>
            <td width="5%" class="text-center">:</td>
            <td width="60%"><?=$CLIENTE_ENCARGADO['email']?></td>
        </tr>
        <tr>
            <td width="35%" class="text-left">TELEFONO</td>
            <td width="5%" class="text-center">:</td>
            <td width="60%"><?=$CLIENTE_ENCARGADO['telefono']?></td>
        </tr>
    </table>
    <table class="table">
        <tr>
            <td class="text-left" colspan="3">
                <p>
                    <b><u>4.- BENEFICIOS DEL PLAN ( VER ANEXO #1 )</u></b>
                </p>
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-left">PLAZO DEL CONTRATO</td>
            <td width="5%" class="text-center">:</td>
            <td width="60%">
                <div><?=$BENEFICIOS_PLAZO['plazo']?></div>
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-left">PAGO POR EL SERVICIO CONTRATADO</td>
            <td width="5%" class="text-center">:</td>
            <td width="60%"><?=$BENEFICIOS_PLAZO['pago_servicio'] . ' ' . $BENEFICIOS_PLAZO['letra_pago_servicio']?></td>
        </tr>
        @if($isCoworking)
            <tr>
                <td width="35%" class="text-left">A PARTIR DEL 4to MES</td>
                <td width="5%" class="text-center">:</td>
                <td width="60%"><?=$BENEFICIOS_PLAZO['cuarto_mes'] . ' ' . $BENEFICIOS_PLAZO['letra_cuarto_mes']?></td>
            </tr>
            <tr>
                <td width="35%" class="text-left">DIRECCION DEL SERVICIO CONTRATADO</td>
                <td width="5%" class="text-center">:</td>
                <td width="60%"><?=$BENEFICIOS_PLAZO['direccion_servicio']?></td>
            </tr>
            <tr>
                <td width="35%" class="text-left">N° MODULO ASIGNADO</td>
                <td width="5%" class="text-center">:</td>
                <td width="60%"><?=$BENEFICIOS_PLAZO['modulos_asignado']?></td>
            </tr>
        @endif
        <tr>
            <td width="35%" class="text-left">GARANTIA</td>
            <td width="5%" class="text-center">:</td>
            <td width="60%"><?=$BENEFICIOS_PLAZO['garantia'] . ' ' . $BENEFICIOS_PLAZO['letra_garantia']?></td>
        </tr>
        <tr>
            <td width="35%" class="text-left">FECHA DE PAGO</td>
            <td width="5%" class="text-center">:</td>
            <td width="60%"><?=$BENEFICIOS_PLAZO['fecha_pago']?></td>
        </tr>
    </table>
    <p class="text-justify">
        El plazo del contrato se computará desde el ciclo de facturación que el cliente elija ya sea (Ciclo quincenal o
        mensual) validando con la firma del presente documento, debiendo efectuarse los pagos en forma adelantada como
        máximo en la fecha establecida en el presente anexo. Si existiera retraso en el pago pactado, CENTROS VIRTUALES
        podrá proceder a suspender en forma parcial y/o total los servicios, debiendo EL COMITENTE pagar la suma de S/.
        15.00 para la reactivación correspondiente. Si el retraso en el pago fuera por un período mayor a 30 días
        calendarios, el contrato quedará resuelto de pleno derecho sin necesidad de declaración judicial, en
        consecuencia, EL COMITENTE perderá en forma definitiva su suscripción*.
    </p>
    <p class="text-justify">
        Si el COMITENTE no desea renovar el contrato, deberá comunicar mediante correo electrónico o carta indicando los
        motivos por el cual no desea proceder a la renovación, debiendo ser realizado con anticipación no menor a 30
        días calendarios. Si el COMITENTE no llega a comunicarse con CENTROS VIRTUALES, se procederá a la renovación
        automática por el periodo y plan vigente de su último contrato.
    </p>
</div>
<div class="add-page-before">
    <p class="text-justify">
        EL COMITENTE declara que todos los datos consignados en el presente documento son ciertos, asumiendo la
        responsabilidad legal que corresponda en caso de falsedad.
    </p>
    @yield('firma')
</div>
@if(!$isCoworking)
    <div class="add-page-before">
        <p class="text-center"><b><u>ANEXO #1.1</u></b></p>
        <div class="text-center" style="padding-top: 2em">
            <img src="{{ asset('images/planes_cv.jpg') }}" alt="" width="700px" height="910px">
        </div>
    </div>
@endif
@if($isCoworking)
    <div class="add-page-before">
        <p class="text-center"><b><u>ANEXO #2</u></b></p>
        <p></p>
        <table class="table">
            <tr>
                <td width="100%" class="text-left" colspan="3">
                    <p>
                        <b><u>1.- CARACTERISTICAS DEL PLAN</u></b>
                    </p>
                </td>
            </tr>
            <tr>
                <td width="100%" class="text-left">
                    <ul>
                        <li>Espacio de coworking</li>
                        <li>5 horas al mes en salas de reuniones</li>
                        <li>Acceso a internet mediante Wi-fi</li>
                        <li>Servicio de bebida (Café, manzanilla, anís, te, agua)</li>
                        <li>Evento semanal de networking gratuito</li>
                        <li>Recepción de correspondencia</li>
                        <li>Número fijo empresarial (Central telefónica con contestadora personalizada)</li>
                        <li>Aplicación Android/IOS para gestión de servicios</li>
                        <li>Servicio de lokers exclusivo</li>
                        <li>Domicilio comercial</li>
                        <li>Costo del Plan 550 + IGV</li>
                    </ul>
                </td>
            </tr>
        </table>
        <p class="text-justify">
            El espacio de coworking es un espacio único de trabajo que puede ser utilizado por una única persona de L-V
            8:00 am - 6:30 pm y Sab. De 8:00 am - 2:00 pm. En este espacio está prohibido recibir y/o atender cualquier
            tipo de visita o cliente.
            En cuando al uso de las oficinas de directorio y demás servicios EL COMITENTE deberá realizar sus reservas
            mediante el panel de cliente, utilizando su usuario y password. Si desea horas extras, tendrá un costo de 30
            nuevos soles. Si desea la eliminación de reservas deberá ser solicitado como mínimo 3 horas de anticipación
            a lo agendado.
        </p>
        <p class="text-justify">
            Si EL COMITENTE desea realizar una modificación en su mensaje de voz de la central telefónica, el pago a
            realizar es de 45 soles.
            La correspondencia será de peso liviano, identificado por el nombre de la empresa y/o representante. El
            COMITENTE tendrá un plazo máximo de 30 días para recoger su correspondencia (iniciando a partir de la fecha
            de recepción). Después de esta fecha Centros Virtuales no se responsabiliza de la custodia de dicha
            documentación.
            Respecto al servicio de lokers es gratuito, sin embargo Centros Virtuales no se responsabiliza de las
            pertenencias que pueda dejar el comitente por lo que se recomienda no dejar cosas de valor.
        </p>
        <p class="text-justify">
            El plazo del contrato se computará desde el día siguiente de la firma del presente documento, debiendo
            efectuarse los pagos en forma adelantada como máximo en la fecha establecida en el presente anexo. Si
            existiera retraso en el pago pactado, CENTROS VIRTUALES podrá proceder a suspender en forma parcial y/o
            total los servicios, debiendo EL COMITENTE pagar la suma de S/. 15.00 para la reactivación correspondiente.
            Si el retraso en el pago fuera por un período mayor a 30 días calendarios, el contrato quedará resuelto de
            pleno derecho sin necesidad de declaración judicial, en consecuencia, EL COMITENTE perderá en forma
            definitiva su suscripción*.
        </p>
        <p class="text-justify">
            Si el COMITENTE no desea renovar el contrato, deberá comunicar mediante correo electrónico o carta indicando
            los motivos por el cual no desea proceder a la renovación, debiendo ser realizado con anticipación no menor
            a 30 días calendarios. Si el COMITENTE no llega ha comunicarse con CENTROS VIRTUALES, se procederá a la
            renovación automática por el periodo y plan vigente de su último contrato.
        </p>
        <p class="text-justify">
            EL COMITENTE declara que todos los datos consignados en el presente documento son ciertos, asumiendo la
            responsabilidad legal que corresponda en caso de falsedad.
        </p>
        @yield('firma')
    </div>
@endif
@if($dataEmpresa['preferencia_fiscal'] == 'SI')
    @if($isCoworking)
        <?php $anexo = '3'?>
    @else
        <?php $anexo = '2'?>
    @endif
    <div class="add-page-before">
        <p class="text-center"><b><u>ANEXO #<?=$anexo?></u></b></p>
        <p></p>
        <p class="text-center"><b>(solo para clientes con domicilio fiscal)</b></p>
        <p></p>
        <table class="table">
            <tr>
                <td class="text-left" colspan="3">
                    <p>
                        <b><u>1.- DATOS DEL COMITENTE</u></b>
                    </p>
                </td>
            </tr>
            <tr>
                <td width="35%" class="text-left">NOMBRE Y/O RAZON SOCIAL</td>
                <td width="5%" class="text-center">:</td>
                <td width="60%">
                    <div><?=$COMITENTE['razon_social']?></div>
                </td>
            </tr>
            <tr>
                <td width="35%" class="text-left">RUC</td>
                <td width="5%" class="text-center">:</td>
                <td width="60%"><?=$COMITENTE['ruc']?></td>
            </tr>
            <tr>
                <td width="35%" class="text-left">ACTIVIDAD O GIRO DEL NEGOCIO</td>
                <td width="5%" class="text-center">:</td>
                <td width="60%"><?=$COMITENTE['actividad']?></td>
            </tr>
            <tr>
                <td width="35%" class="text-left">DOMICILIO</td>
                <td width="5%" class="text-center">:</td>
                <td width="60%"><?=$COMITENTE['domicilio']?></td>
            </tr>
            <tr>
                <td width="35%" class="text-left">PODERES INSCRITOS</td>
                <td width="5%" class="text-center">:</td>
                <td width="60%"><?=$COMITENTE['poderes']?></td>
            </tr>
        </table>
        <table class="table">
            <tr>
                <td class="text-left" colspan="3">
                    <p>
                        <b><u>2.- DATOS DEL REPRESENTANTE LEGAL</u></b>
                    </p>
                </td>
            </tr>
            <tr>
                <td width="35%" class="text-left">NOMBRE Y/O RAZON SOCIAL</td>
                <td width="5%" class="text-center">:</td>
                <td width="60%">
                    <div><?=$REPRESENTANTE['nombre_apellido']?></div>
                </td>
            </tr>
            <tr>
                <td width="35%" class="text-left">DOCUMENTO DE IDENTIDAD</td>
                <td width="5%" class="text-center">:</td>
                <td width="60%"><?=$REPRESENTANTE['dni']?></td>
            </tr>
            <tr>
                <td width="35%" class="text-left">DOMICILIO</td>
                <td width="5%" class="text-center">:</td>
                <td width="60%"><?=$REPRESENTANTE['domicilio']?></td>
            </tr>
            <tr>
                <td width="35%" class="text-left">EMAIL</td>
                <td width="5%" class="text-center">:</td>
                <td width="60%"><?=$REPRESENTANTE['email']?></td>
            </tr>
            <tr>
                <td width="35%" class="text-left">TELEFONO</td>
                <td width="5%" class="text-center">:</td>
                <td width="60%"><?=$REPRESENTANTE['telefono']?></td>
            </tr>
        </table>
        <br>
        <p>
            <b><u>3.- DOMICILIO FISCAL</u></b>
        </p>
        <p class="text-justify">
            CENTROS VIRTUALES otorga el servicio de domicilio fiscal a favor de EL COMITENTE bajos las siguientes
            condiciones:
        </p>
        <table>
            <tr>
                <td class="text-justify sangria"><b>A)</b></td>
                <td class="text-justify">
                    El domicilio fiscal declarado por EL COMITENTE tiene como finalidad facilitar el pleno y cabal
                    cumplimiento de sus obligaciones tributarias, y facilitar la labor de comunicación y fiscalización
                    de
                    las entidades competentes del Estado Peruano, en tal sentido, está terminantemente prohibido su uso
                    con
                    propósitos de evasión, elusión y/o cualquier otro acto sancionado por ley.
                </td>
            </tr>
            <tr>
                <td><br></td>
            </tr>
            <tr>
                <td class="text-justify sangria"><b>B)</b></td>
                <td class="text-justify">
                    CENTROS VIRTUALES, no asume responsabilidad alguna respecto a las obligaciones de carácter
                    tributario de
                    EL COMITENTE, en consecuencia, no podrá ser afectado por medidas cautelares o procesos de
                    fiscalización
                    que se lleven a cabo contra EL COMITENTE.
                </td>
            </tr>
            <tr>
                <td><br></td>
            </tr>
            <tr>
                <td class="text-justify sangria"><b>C)</b></td>
                <td class="text-justify">
                    EL COMITENTE declara que, ha contratado los servicios de CENTROS VIRTUALES para el uso temporal (por
                    horas) de oficinas y algunos servicios complementarios detallados en el Anexo 1, dejando en claro
                    que la
                    integridad de bienes e instalaciones al interior de las oficinas indicadas en la cláusula primera
                    del
                    contrato, son de exclusiva propiedad de CENTROS VIRTUALES.
                </td>
            </tr>
            <tr>
                <td><br></td>
            </tr>
            <tr>
                <td class="text-justify sangria"><b>D)</b></td>
                <td class="text-justify">
                    EL COMITENTE está obligado a dar de baja o variar el domicilio fiscal facilitado por CENTROS
                    VIRTUALES,
                    dentro del plazo de 72 horas de haber vencido el plazo del presente contrato, en caso contrario,
                    CENTROS
                    VIRTUALES podrá proceder conforme a lo indicado en el numeral siguiente.
                </td>
            </tr>
        </table>
        <br>
        <p>
            <b><u>4.- RESOLUCION CONTRACTUAL</u></b>
        </p>
        <p class="text-justify">
            Por medio del presente documento EL COMITENTE autoriza a CENTROS VIRTUALES la resolución unilateral y
            extrajudicial del presente contrato, así como la inmediata suspensión del servicio indicado en el presente
            anexo, pudiendo comunicar a la entidad competente que el domicilio fiscal declarado por EL COMITENTE ha
            variado
            o debe ser dado de baja, cuando se den cualquiera de las siguientes circunstancias:
        </p>
        <table>
            <tr>
                <td class="text-justify sangria"><b>A)</b></td>
                <td class="text-justify">
                    Que, habiendo culminado el plazo del contrato, EL COMITENTE no haya cumplido con proceder a declarar
                    la
                    baja o variación del domicilio fiscal dentro del plazo de 72 horas, conforme a lo indicado en el
                    literal
                    "d" de la cláusula precedente.
                </td>
            </tr>
            <tr>
                <td><br></td>
            </tr>
            <tr>
                <td class="text-justify sangria"><b>B)</b></td>
                <td class="text-justify">
                    Que, la autoridad competente haya iniciado un proceso de revisión y/o fiscalización contra EL
                    COMITENTE
                    por supuestos incumplimientos de obligaciones tributarias.
                </td>
            </tr>
            <tr>
                <td><br></td>
            </tr>
            <tr>
                <td class="text-justify sangria"><b>C)</b></td>
                <td class="text-justify">
                    Que, EL COMITENTE se encuentre inmerso en un proceso de Reclamación Tributaria o cobranza coactiva
                    por
                    supuesto incumplimiento de obligaciones tributarias.
                </td>
            </tr>
            <tr>
                <td><br></td>
            </tr>
            <tr>
                <td class="text-justify sangria"><b>D)</b></td>
                <td class="text-justify">
                    Que, existan resolución emitidas por la autoridad competente que sancionen el incumplimiento de
                    obligaciones tributarias.
                </td>
            </tr>
            <tr>
                <td><br></td>
            </tr>
            <tr>
                <td class="text-justify sangria"><b>E)</b></td>
                <td class="text-justify">
                    Que, EL COMITENTE haya sido requerido por la entidad competente para sustentar ingresos
                    supuestamente no
                    declarados (cartas inductivas u otros requerimientos)
                </td>
            </tr>
            <tr>
                <td><br></td>
            </tr>
            <tr>
                <td class="text-justify sangria"><b>F)</b></td>
                <td class="text-justify">
                    Que, EL COMITENTE utilice la dirección de la empresa como dirección fiscal para constituir su
                    empresa.
                </td>
            </tr>
            <tr>
                <td><br></td>
            </tr>
            <tr>
                <td class="text-justify sangria"><b>G)</b></td>
                <td class="text-justify">
                    Que, EL COMITENTE reciba retirados Memorándum por el incumplimiento de las normas de convivencia.
                </td>
            </tr>
        </table>
        <br>
        <p class="text-justify">
            Así mismo, CENTROS VIRTUALES podrá valerse de esta cláusula ante cualquier irregularidad o situación que
            comprometa a EL COMITENTE con el pago o cumplimiento de obligaciones frente a cualquier entidad del Estado
            Peruano.
        </p>
        @yield('firma')
    </div>
@endif
@if($isCoworking)
    @if($dataEmpresa['preferencia_fiscal'] == 'SI')
        <?php $anexo = '4'?>
    @else
        <?php $anexo = '3'?>
    @endif
    <div class="add-page-before">
        <p class="text-center"><b><u>ANEXO #<?=$anexo?></u></b></p>
        <p></p>
        <p class="text-center"><b>CARTA DE COMPROMISO</b></p>
        <p></p>
        <p class="text-justify">
            Por medio del presente el comitente se compromete a dar fiel cumplimiento a las normas de convivencia de
            coworking determinadas por la empresa Centros Virtuales del Peru EIRL.
        </p>
        <table>
            <tr>
                <td class="text-justify sangria"><b>1)</b></td>
                <td class="text-justify">
                    No se permitirá la entrada a ningún usuario fuera del horario establecido.
                </td>
            </tr>
            <tr>
                <td><br></td>
            </tr>
            <tr>
                <td class="text-justify sangria"><b>3)</b></td>
                <td class="text-justify">
                    Se amable y respetuoso con tus compañeros, no toleramos comportamientos poco profesionales o
                    maliciosos.
                </td>
            </tr>
            <tr>
                <td><br></td>
            </tr>
            <tr>
                <td class="text-justify sangria"><b>4)</b></td>
                <td class="text-justify">
                    Respeta el espacio y déjalo tal y como lo encuentras. Deja las cosas cómo estaban y básicamente, el
                    que
                    rompe, se hace responsable y paga.
                </td>
            </tr>
            <tr>
                <td><br></td>
            </tr>
            <tr>
                <td class="text-justify sangria"><b>5)</b></td>
                <td class="text-justify">
                    Al tratarse de un espacio de coworking no se permitirá el acceso a ningún usuario que no respete las
                    normas convencionales de higiene personal.
                </td>
            </tr>
            <tr>
                <td><br></td>
            </tr>
            <tr>
                <td class="text-justify sangria"><b>6)</b></td>
                <td class="text-justify">
                    Las visitas externas deberán identificarse en recepción y no podrán ser recibidas en las áreas de
                    coworking. Para recibir a visitas externas deberán separar las salas de reuniones.
                </td>
            </tr>
            <tr>
                <td><br></td>
            </tr>
            <tr>
                <td class="text-justify sangria"><b>7)</b></td>
                <td class="text-justify">
                    Está terminantemente prohibido abrir las ventanas o las puertas cuando el equipo de aire
                    acondicionado
                    este prendido.
                </td>
            </tr>
            <tr>
                <td><br></td>
            </tr>
            <tr>
                <td class="text-justify sangria"><b>8)</b></td>
                <td class="text-justify">
                    No dejes objetos personales desperdigados o desatendidos en las zonas comunes. La administración no
                    se
                    responsabiliza de las pérdidas de objetos personales.
                </td>
            </tr>
            <tr>
                <td><br></td>
            </tr>
            <tr>
                <td class="text-justify sangria"><b>9)</b></td>
                <td class="text-justify">
                    Solo está permitido el ingreso de computadoras portátiles, por tanto no está autorizado el ingreso
                    de
                    computadoras de mesa.
                </td>
            </tr>
            <tr>
                <td><br></td>
            </tr>
            <tr>
                <td class="text-justify sangria"><b>10)</b></td>
                <td class="text-justify">
                    El uso de los utensilios de cocina (vasos, tazas, platos, microondas, etc) conlleva la limpieza y
                    colocación de los mismos una vez usados mantenerlo en condiciones higiénicas óptimas.
                </td>
            </tr>
            <tr>
                <td><br></td>
            </tr>
            <tr>
                <td class="text-justify sangria"><b>11)</b></td>
                <td class="text-justify">
                    Se realizará un uso respetuoso de los servicios higiénicos.
                </td>
            </tr>
            <tr>
                <td><br></td>
            </tr>
            <tr>
                <td class="text-justify sangria"><b>12)</b></td>
                <td class="text-justify">
                    El medio aceptado únicamente para escuchar música u otro sonido dentro del área de coworking serán
                    audífonos, con el fin de no molestar a los demás usuarios.
                </td>
            </tr>
            <tr>
                <td><br></td>
            </tr>
            <tr>
                <td class="text-justify sangria"><b>13)</b></td>
                <td class="text-justify">
                    No está permitido fumar, ni ingresar alimentos o bebidas que generen un olor que pudiera incomodar a
                    los
                    compañeros.
                </td>
            </tr>
            <tr>
                <td><br></td>
            </tr>
            <tr>
                <td class="text-justify sangria"><b>14)</b></td>
                <td class="text-justify">
                    Con el propósito de respetar el área de trabajo de los demás usuarios queda prohibido gritar o
                    hablar en
                    tono fuerte dentro de las sala de coworking.
                </td>
            </tr>
            <tr>
                <td><br></td>
            </tr>
            <tr>
                <td class="text-justify sangria"><b>15)</b></td>
                <td class="text-justify">
                    Únicamente podrás utilizar tu teléfono en modo de vibración, y en caso de recibir alguna llamada
                    tendrás
                    que salir a recibirla a fin de no incomodar a los demás.
                </td>
            </tr>
            <tr>
                <td><br></td>
            </tr>
            <tr>
                <td class="text-justify sangria"><b>16)</b></td>
                <td class="text-justify">
                    En caso de encontrar algún objeto extraviado de otra persona, entregarlo al personal administrativo.
                </td>
            </tr>
            <tr>
                <td><br></td>
            </tr>
            <tr>
                <td class="text-justify sangria"><b>17)</b></td>
                <td class="text-justify">
                    Está prohibido el ingreso de mascotas.
                </td>
            </tr>
            <tr>
                <td><br></td>
            </tr>
            <tr>
                <td class="text-justify sangria"><b>18)</b></td>
                <td class="text-justify">
                    La administración se reserva el derecho de admisión si se presentan signos de embriaguez, actitud
                    violenta, indumentaria indecorosa u ofensiva.
                </td>
            </tr>
            <tr>
                <td><br></td>
            </tr>
            <tr>
                <td class="text-justify sangria"><b>19)</b></td>
                <td class="text-justify">
                    En caso de disputa o diferencia entre dos o más coworkers, un responsable de administración ejercerá
                    de
                    arbitro y mediador en la disputa, y todos los implicados se comprometen a aceptar su decisión.
                </td>
            </tr>
        </table>
    </div>
    <div class="add-page-before">
        <p class="text-justify">
            Te recordamos brevemente:
        </p>
        <table>
            <tr>
                <td>
                    <ul>
                        <li>No descargar contenido inapropiado.</li>
                        <li>No descargar material con copyright o con licencia privativa o de uso a menos que estés en
                            posesión de una licencia.
                        </li>
                        <li>No transferir (subir/bajar) archivos de gran tamaño, al menos que sea necesario para la
                            realización de su trabajo.
                        </li>
                        <li>No dejar su equipo descargando contenido de la red sin su supervisión personal.</li>
                    </ul>
                </td>
            </tr>
        </table>
        <br>
        @yield('firma')
    </div>
@endif
</body>
</html>