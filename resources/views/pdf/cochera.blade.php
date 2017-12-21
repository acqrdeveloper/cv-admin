<?php
	if(strlen($empresa->empresa_ruc) == 11 ){
		$block = $empresa->empresa_nombre . " con RUC N° " . $empresa->empresa_ruc. " debidamente representado por " . strtoupper($representante->nombre. " " .$representante->apellido) . " identificado con DNI. N° " . $representante->dni. " ubicado en " . $representante->domicilio;
	} else {
		$block = strtoupper($representante->nombre. " " .$representante->apellido) . " identificado con DNI. N° " . $representante->dni. " ubicado en " . $representante->domicilio;
	}
?>
<div class="body-page">
	<div style="font-weight: bold;text-align:center;margin-bottom:30px;text-decoration: underline;">ANEXO {{$anexo}}</div><br>
	<div style="text-align: center"><b>CONTRATO DE LOCACION DE SERVICIOS USO DE COCHERA</b></div><br>
	<div class="clausule">
		<div class="paragraph">Conste por el presente documento el CONTRATO CON CENTROS VIRTUALES que celebran de una parte, CENTROS VIRTUALES, con RUC: 20546951015, debidamente representada por su titular Gerente, el Sr. Oscar Gonzalo Salvador Ruiz, con DNI. Nro. 48632827 con domicilio en Calle Elias Aguirre 180, distrito de Miraflores y de otra parte {{ $block }}, el mismo que se suscribe en los términos y condiciones siguientes:</div>
	</div>
	<div class="clausule">
		<div class="subTitle">PRIMERO: ANTECEDENTES</div>
		<div class="paragraph">Suscribieron un Contrato, cuyo objeto era que EL COMITENTE gozará del uso temporal de un espacio físico asignado de acuerdo a la disponibilidad de CENTROS VIRTUALES así como servicios complementarios, de recepción de llamadas telefónicas, correspondencia, uso de áreas de directorio, oficinas privadas y otros servicios que se brindarán de conformidad con el plan contratado y que se encuentran precisados en el anexo 1.</div>
	</div>
	<div class="clausule">
		<div class="subTitle">SEGUNDO: VIGENCIA DEL CONTRATO</div>
		<div class="paragraph">El contrato se encuentra vigente desde el 31/10/2017</div>
	</div>
	<div class="clausule">
		<div class="subTitle">TERCERO: CONDICIONES PARA EL USO DE COCHERA</div>
		<div class="paragraph">
			<div>Centros Virtuales ofrece al Comitente el uso de cochera ubicado en el sótano 2. En cuanto al uso de la cochera se deberá tomar en cuenta lo siguiente:</div><br>
			<div style="padding-left: 0.5cm">
				<div>En cuanto al personal encargado:</div>
				<ul>
					<li>El personal de seguridad es el responsable del ingreso de los vehículos, deberá tener mucha diligencia al orientar al CLIENTE de Centros Virtuales para que pueda estacionarse correctamente.</li>
					<li>El personal de seguridad es el responsable de abrir y cerrar la cochera, para ello cuenta con un control</li>
					<li>El personal de seguridad es responsable del cuidado de la cochera, es responsable que ingresen a la cochera solo los autos autorizados y de la forma correcta.</li>
				</ul>
			</div>
			<div style="padding-left: 0.5cm">
				<div>En cuanto al COMITENTE:</div>
				<ul>
					<li>El Comitente se hace responsable de su propiedad vehicular y de las pertenencias que puedan estar en el vehículo.</li>
					<li>El Comitente deberá ser diligente con el cuidado del montavehículos, de lo contrario deberá responder económicamente por los daños ocasionados.</li>
					<li>El comitente deberá estacionarse correctamente, toda vez que los sensores, seguridades de puertos y el equipo no presenta problemas de estacionamiento.</li>
					<li>Si a pesar de las indicaciones que le brinda el personal de seguridad EL COMITENTE hace caso omiso y producto de ello se generan daños a su vehículo, el COMITENTE será el único responsable y deberá cubrir tanto los daños ocasionados a su vehículo como al ascensor eléctrico.</li>
					<li>Centros Virtuales se exime de toda responsabilidad en cuanto a la propiedad del Comitente.</li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="page_break"></div>
<div class="body-page">
	<div class="clausule">
		<div class="subTitle">CUARTO: ALQUILER DE COCHERA</div>
		<div class="paragraph">
			<ul class="line-style-guion">
				<li>El Comitente que requiera el servicio de alquiler de cochera deberá realizar un pago de S/5.00 soles, este pago es por cada hora que alquile la cochera, este monto se podrá recargar en su facturación.</li>
				<li>Los autos autorizados para el ingreso de la cochera deberán ser de peso liviano, no se permite el ingreso de CAMIONETAS u otro tipo de automóvil que perjudique el correcto funcionamiento del ascensor eléctrico.</li>
				<li>Centros Virtuales se exime de toda responsabilidad de reserva de cochera a terceros.</li>
			</ul>
		</div>
	</div>
	<div class="clausule">
		<div class="subTitle">QUINTO: DECLARACION DE LAS PARTES</div>
		<div class="paragraph">Tanto la Empresa CENTROS VIRTUALES como el COMITENTE declaran que reconocen y reiteran todos los compromisos asumidos en virtud de la adenda Ambas partes, suscriben este documento en señal de conformidad de todo lo estipulado cuyos textos son igualmente idénticos, a la fecha que se suscribe el contrato.</div>
	</div>
	@component('pdf.sign')
	@endcomponent
</div>