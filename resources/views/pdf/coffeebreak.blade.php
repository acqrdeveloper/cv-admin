<?php
	if(strlen($empresa->empresa_ruc) == 11 ){
		$block = $empresa->empresa_nombre . " con RUC N° " . $empresa->empresa_ruc. " debidamente representado por " . strtoupper($representante->nombre. " " .$representante->apellido) . " identificado con DNI. N° " . $representante->dni. " ubicado en " . $representante->domicilio;
	} else {
		$block = strtoupper($representante->nombre. " " .$representante->apellido) . " identificado con DNI. N° " . $representante->dni. " ubicado en " . $representante->domicilio;
	}

	$anexo = $anexo
?>
<!--<div class="body-page">
	<div style="font-weight: bold;text-align:center;margin-bottom:30px;text-decoration: underline;">ANEXO {{$anexo}}</div><br>
	<div style="text-align: center"><b>CONTRATO DE ALQUILER DE SERVICIOS Y AREAS GENERALES</b></div><br>
	<div class="clausule">
		<div class="paragraph"><div style="padding-bottom: 10px;">Ambas partes, suscriben este documento conste por el presente documento el contrato con CENTROS VIRTUALES quecelebran de una parte:</div><div style="padding-bottom: 10px;">CENTROS VIRTUALES, con RUC N° 20546951015, debidamente representada por su titular Gerente, el Sr. Oscar Gonzalo Salvador Ruiz, con DNI. N° 48632827 con domicilio en Calle Elias Aguirre 180, Distrito de Miraflores, aquien en adelante denominaremos EL ARRENDADOR; y de otra parte {{ $block }}, a quien en adelante denominaremos EL CLIENTE, el mismo que suscribe el presente documento en los términos y condiciones siguientes:</div><div>EL ARRENDADOR declara que todos los datos consignados en el presente documento son ciertos, asumiendo la responsabilidad legal que corresponda en caso de falsedad.</div></div>
	</div>
	<div class="clausule">
		<div class="subTitle">PRIMERO.- ANTECEDENTES</div>
		<div class="paragraph">
			EL ARRENDADOR es propietario de los auditorios y aulas de capacitación ubicados:<br>
			<div style="padding-left: 0.5cm">
				<b>A)</b> Calle Elias Aguirre N 180 2do Piso (Aula de Capacitación para 20 personas)<br>
				<b>B)</b> Calle Elias Aguirre N 180 5to Piso (Capacidad para 60 personas)<br>
				<b>C)</b> Calle Elias Aguirre N 180 6to Piso (Capacidad para 60 personas)<br>
				<b>D)</b> Calle Elias Aguirre 141 Messanine (Capacidad para 30 personas)<br>
				<b>E)</b> Calle Elias Aguirre N 180 8vo Piso (Terraza capacidad para 16 personas)<br>
			</div>
		</div>
	</div>
	<div class="clausule">
		<div class="subTitle">SEGUNDO.- DURACION DEL CONTRATO</div>
		<div class="paragraph">
			El plazo de duración del presente contrato es por horas determinadas, fecha en que EL ARRENDATARIO utilizará:<br>
			( Marcar con una x la sala que se utilizará )<br>
			<div style="padding-left: 0.5cm">
				<b>A)</b> Calle Elias Aguirre N 180 2do Piso (Aula de Capacitación para 20 personas)<br>
				<b>B)</b> Calle Elias Aguirre N 180 5to Piso (Capacidad para 60 personas)<br>
				<b>C)</b> Calle Elias Aguirre N 180 6to Piso (Capacidad para 60 personas)<br>
				<b>D)</b> Calle Elias Aguire 141 Messanine (Capacidad para 30 personas)<br>
				<b>E)</b> Calle Elias Aguire 180 8vo Piso (Terraza Capacidad para 16 personas)<br>
			</div>
		</div>
	</div>
	<div class="clausule">
		<div class="subTitle">TERCERO.- PAGO</div>
		<div class="paragraph">El pago pactado de común acuerdo, es por horas determinadas, las cuales se pagarán en forma adelantada con el objetivo de realizar y confirmar la reserva en el sistema, de lo contrario la empresa no se responsabiliza por la solicitud de reserva.</div>
	</div>
	<div class="clausule">
		<div class="subTitle">CUARTO.- RESERVA DE AUDITORIO</div>
		<div class="paragraph">
			<ul>
				<li>En cuanto al alquiler del auditorio, aulas de capacitación o terraza EL CLIENTE no podrá anular las reservas cuando ya estén realizadas, ni solicitar el reembolso del dinero.</li>
				<li>EL CLIENTE deberá realizar su reserva con un mínimo de 5 horas de anticipación a la fecha de reserva.</li>
				<li>EL CLIENTE por medio de la plataforma de Centros Virtuales, deberá solicitar su reserva, posterior a la solicitud, se otorga al cliente cinco horas para que realice el pago, el área encargada procederá a verificar el pago y de acuerdo a ello se aceptará su reserva, de lo contraria la solicitud será eliminada.</li>
				<li>EL CLIENTE deberá respetar el horario de reserva, por tanto deberá ser puntual con el horario de ingreso y salida, de lo contrario se tomará parte del pago de la garantía.</li>
				<li>
					Debe respetar los aforos de cada área:
					<div style="padding-left: 0.5cm">
						<ul class="line-style-guion">
							<li>Auditorio: Aforo 60 personas</li>
							<li>Sala de capacitación Elias Aguirre: Aforo 20 personas.</li>
							<li>Sala de capacitación Messanine : Aforo 30 personas.</li>
							<li>Terraza: Aforo 16 personas.</li>
						</ul>
					</div>
				</li>
				<li>Si EL CLIENTE trae su publicidad, sea banners o afiches se deben exhibir en el espacio alquilado del auditorio, no está permitido hacerlo en el área de recepción.</li>
			</ul>
		</div>
	</div>
	<div class="clausule">
		<div class="subTitle">QUINTO.- INMUEBLE</div>
		<div class="paragraph">
			EL CLIENTE declara recibir el espacio de alquiler, materia de contrato, en perfectas condiciones;
			comprometiéndose a devolverlo en el mismo estado, salvo el desgaste causado por el uso normal. De lo contrario, por cualquier daño que se ocasione en el inmueble, se descontará de la garantía otorgada, que refiere la cláusula posterior.<br>
			EL CLIENTE es responsable de los daños o pérdidas que pudieran ocasionarse a las instalaciones y bienes de propiedad de CENTROS VIRTUALES por actos propios o de terceros que ingresen a las oficinas autorizados por EL CLIENTE, debiendo éste reponer el valor de los bienes afectados dentro de un plazo de 24 horas de verificado el hecho, esta misma disposición es aplicable en caso los daños se produzcan en las áreas comunes del edificio.
		</div>
	</div>
	<div class="clausule">
		<div class="subTitle">SEXTO.- DISPOSICIONES DE CENTROS VIRTUALES</div>
		<div class="paragraph">EL CLIENTE deberá tratar con respeto y acatar las disposiciones y/o directivas comunicadas por el personal de CENTROS VIRTUALES, para el uso de los servicios, bienes e instalaciones del auditorio, salas de capacitaciones y terraza.</div>
	</div>
	<div class="clausule">
		<div class="subTitle">SEPTIMO.- REGISTRO DE ASISTENTES PARA EVENTO O CAPACITACION</div>
		<div class="paragraph">En cuanto al registro de asistentes tanto para auditorio, salas de capacitaciones y terraza, se deberá dar fiel cumplimiento a lo siguiente:
			<table>
				<tr><th>1.-</th><td>EL CLIENTE deberá subir su lista de asistentes mediante la plataforma de Centros Virtuales, para el control y seguridad de la empresa, así mismo EL CLIENTE podrá actualizar su lista hasta la hora que inicie su evento.</td></tr>
				<tr><th>2.-</th><td>Para el ingreso al auditorio, salas de capacitaciones y terraza, los asistentes deberán portar su DNI, de lo contrario no se permitirá el ingreso.</td></tr>
				<tr><th>3.-</th><td>EL CLIENTE deberá subir su lista de asistentes mediante la plataforma de Centros Virtuales, para el control y seguridad de la empresa, así mismo EL CLIENTE podrá actualizar su lista hasta la hora que inicie su evento.</td></tr>
				<tr><th>4.-</th><td>Los asistentes que se apersonen a las instalaciones y no estén registrados en la lista de asistentes no podrán ingresar a las instalaciones del nuevo Centro de Innovación Empresarial.</td></tr>
				<tr><th>5.-</th><td>Los asistentes podrán ingresar al evento o capacitación diez minutos antes de su hora de inicio del evento.</td></tr>
				<tr><th>6.-</th><td>Se suspenderá el evento si EL CLIENTE no sube a la plataforma de Centros Virtuales su lista de asistentes y por tanto no se reembolsará el dinero por el pago de la reserva.</td></tr>
			</table>
		</div>
	</div>
</div>
<div class="page_break"></div>
<div class="body-page">
	<div class="clausule">
		<div class="subTitle">OCTAVO.- NORMAS DE CONDUCTA</div>
		<div class="paragraph">
			EL CLIENTE guardará las normas de conducta, respeto y consideración con los clientes de Centros Virtuales, evitando las molestias de ruidos que perjudique o ponga en peligro la tranquilidad de los clientes de Centros Virtuales.
			<ul>
				<li>El cliente no podrá ingresar al auditorio, salas de capacitaciones o terraza bebidas alcohólicas ni estupefacientes.</li>
				<li>Centros virtuales brinda a sus clientes las áreas generales para que puedan desempeñarse en el rubro empresarial, no está permitido otro tipo de actividades incompatibles con la empresa Centros Virtuales.</li>
			</ul>
		</div>
	</div>
	<div class="clausule">
		<div class="subTitle">NOVENO.- HORARIO DE INGRESO Y SALIDA</div>
		<div class="paragraph">
			En cuanto al alquiler, EL CLIENTE podrá ingresar con 15 minutos de anticipación a fin que realice todas las coordinaciones correspondientes a su evento.<br>
			En cuanto al horario de salida EL CLIENTE deberá ser puntual, si en caso inculmple su hora de reserva se cobrará como una hora adicional de su reserva y se recargara en su facturación.
		</div>
	</div>
	<div class="clausule">
		<div class="subTitle">DECIMO.- COFFEE BREAK</div>
		<div class="paragraph">
			<ul>
				<li>Centros Virtuales prohíbe el ingreso de Catering del CLIENTE que alquile auditorio, salas de capacitaciones, o terraza.</li>
				<li>Si el cliente desea contar con el servicio de Coffe Break podrá contactar a Centros Virtuales a fin de contratar el Servicio de Coffe Break ver ANEXO {{$anexo}}.1.</li>
				<li>El servicio de Coffe Break que brinda Centros Virtuales se llevará a cabo en la Terraza por un periodo de 15 minutos.</li>
				<li>Centros Virtuales solo se responsabiliza por el servicio contratado. El cliente deberá asumir costos adicionales por algún pedido que no contrató en su momento.</li>
			</ul>
		</div>
	</div>
	<div class="clausule">
		<div class="paragraph">Tanto la Empresa CENTROS VIRTUALES como EL CLIENTE declaran que reconocen y reiteran todos los compromisos asumidos en virtud del Contrato. En señal de conformidad de todo lo estipulado cuyos textos son igualmente idénticos.</div>
	</div>
	@component('pdf.sign')
	@endcomponent
</div>
<div class="page_break"></div>-->
<div class="body-page">
	<div style="font-weight: bold;text-align:center;margin-bottom:30px;text-decoration: underline;">ANEXO {{$anexo}}.1</div>
	<div style="text-align: center;">
		<img src="{{ public_path('images/sae_auditorio.png') }}" style="width: 90%">
	</div>
</div>
<div class="page_break"></div>
<div class="body-page">
	<div style="font-weight: bold;text-align:center;margin-bottom:30px;text-decoration: underline;">ANEXO {{$anexo}}.2</div>
	<div style="text-align: center;">
		<img src="{{ public_path('images/coffeebreak_planes.png') }}" style="width: 90%">
	</div>
</div>