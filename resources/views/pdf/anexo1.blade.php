<div class="body-page">
	<div style="font-weight: bold;text-align:center;margin-bottom:30px;text-decoration: underline;">ANEXO 1</div><br>
	<div style="text-align: center"><b>{{ $plan->nombre }} (S/. {{ $plan->precio }} MENSUALES)</b></div><br>
	<div class="clausule">
		<div class="subTitle">1 .- DATOS DEL COMITENTE</div>
		<div class="paragraph">
			<table>
				<tr>
					<td style="width:5cm;">Nombre y/o Razon Social</td><td width="1%">:</td><td>{{ $empresa->empresa_nombre }}</td>
				</tr>
				<tr>
					<td>RUC o DNI</td><td>:</td><td>{{ $empresa->empresa_ruc }}</td>
				</tr>
				<tr>
					<td>Actividad o giro del negocio</td><td>:</td><td>{{ $empresa->empresa_rubro }}</td>
				</tr>
				<tr>
					<td>Domicilio</td><td>:</td><td>{{ $empresa->empresa_direccion }}</td>
				</tr>
				<tr>
					<td>Poderes Inscritos - Partida N°</td><td>:</td><td>{{ $empresa->preferencia_fiscal_nro_partida }}</td>
				</tr>
			</table>
		</div>
	</div><br>
	<div class="clausule">
		<div class="subTitle">2 .- DATOS DEL REPRESENTANTE LEGAL</div>
		<div class="paragraph">
			<table>
				<tr>
					<td style="width:5cm;">Nombres y Apellidos</td><td width="1%">:</td><td>{{ $representante->nombre }} {{ $representante->apellido }}</td>
				</tr>
				<tr>
					<td>Documento de Identidad</td><td>:</td><td>{{ $representante->dni }}</td>
				</tr>
				<tr>
					<td>Domicilio</td><td>:</td><td>{{ $representante->domicilio }}</td>
				</tr>
				<tr>
					<td>Correo electrónico</td><td>:</td><td>{{ $representante->correo }}</td>
				</tr>
				<tr>
					<td>Contacto telefónico</td><td>:</td><td>{{ $representante->telefonos }}</td>
				</tr>
			</table>
		</div>
	</div><br>
	<div class="clausule">
		<div class="subTitle">3 .- DATOS DEL CLIENTE ENCARGADO DE LOS PAGOS</div>
		<div class="paragraph">
			<table>
				<tr>
					<td style="width:5cm;">Nombres y Apellidos</td><td width="1%">:</td><td>{{ $empresa->fac_nombre }} {{ $empresa->fac_apellido }}</td>
				</tr>
				<tr>
					<td>Documento de Identidad</td><td>:</td><td>{{ $empresa->fac_dni }}</td>
				</tr>
				<tr>
					<td>Domicilio</td><td>:</td><td>{{ $empresa->fac_domicilio }}</td>
				</tr>
				<tr>
					<td>Correo electrónico</td><td>:</td><td>{{ $empresa->fac_email }}</td>
				</tr>
				<tr>
					<td>Contacto telefónico</td><td>:</td><td>{{ $empresa->fac_telefono }} / {{ $empresa->fac_celular }}</td>
				</tr>
			</table>
		</div>
	</div><br>
	<div class="clausule">
		<div class="subTitle">4 .- BENEFICIOS DEL PLAN (VER ANEXO Nº 1)</div>
		<div class="paragraph">
			<table>
				<tr>
					<td style="width:7cm; font-size: 12px;"><b>PLAZO DEL CONTRATO</b></td><td width="1%">:</td><td style="text-align: left; font-size: 12px;">{{ \Carbon\Carbon::parse($contrato->fecha_inicio)->format('d/m/Y') }} hasta {{ \Carbon\Carbon::parse($contrato->fecha_fin)->format('d/m/Y') }}</td>
				</tr>
				<tr>
					<td style="text-align: left; font-size: 12px;"><b>PAGO POR EL SERVICIO CONTRATADO</b></td><td>:</td>
					<td style="text-align: left; font-size: 12px;">{!! $servicio_contratado !!}</td>
				</tr>
				<tr>
					<td style="text-align: left; font-size: 12px;"><b>GARANTÍA</b></td><td>:</td><td style="text-align: left; font-size: 12px;">S/. {{ $garantia }}</td>
				</tr>
				<tr>
					<td style="text-align: left; font-size: 12px;"><b>FECHA DE PAGO **</b></td><td style="text-align: left; font-size: 12px;">:</td><td>{{ ($empresa->preferencia_facturacion=='MENSUAL')?'Fin de mes':'Quincena del mes' }}</td>
				</tr>
			</table>
		</div>
	</div>
</div>
<div class="page_break"></div>
<div class="body-page">
	<div class="clausule">
		<div class="paragraph">El plazo del contrato se computará desde el ciclo de facturación que el cliente elija ya sea (Ciclo quincenal o mensual) validando con la firma del presente documento, debiendo efectuarse los pagos en forma adelantada como máximo en la fecha establecida en el presente anexo. Si existiera retraso en el pago pactado, CENTROS VIRTUALES podrá proceder a suspender en forma parcial y/o total los servicios, debiendo EL COMITENTE pagar la suma de S/. 15.00 para la reactivación correspondiente.</div>
	</div>
	<div class="clausule">
		<div class="paragraph">Si el COMITENTE no desea renovar el contrato, deberá comunicar mediante correo electrónico o carta indicando los motivos por el cual no desea proceder a la renovación, debiendo ser realizado con anticipación no menor a 30 días calendarios. Si el COMITENTE no llega a comunicarse con CENTROS VIRTUALES, se procederá a la renovación automática por el periodo y plan vigente de su último contrato.</div>
	</div>
	<div class="clausule">
		<div class="paragraph">EL COMITENTE declara que todos los datos consignados en el presente documento son ciertos, asumiendo la responsabilidad legal que corresponda en caso de falsedad.</div>
	</div>
	@component('pdf.sign')
	@endcomponent
</div>
<div class="page_break"></div>
<div class="body-page">
	<div style="font-weight: bold;text-align:center;margin-bottom:30px;text-decoration: underline;">ANEXO 1.1</div>
	<div style="text-align: center;">
		<img src="{{ public_path('images/planes_cv.jpg') }}" width="90%">
	</div>
</div>