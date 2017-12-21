<div class="body-page">
	<div style="font-weight: bold;text-align:center;margin-bottom:30px;text-decoration: underline;">ANEXO 1</div><br>
	<div class="clausule">
		<div class="subTitle">1 .- DATOS DEL COMITENTE</div>
		<div class="paragraph">
			<table>
				<tr>
					<td style="width:5cm;">Nombre y/o Razon Social</td><td width="1%">:</td><td>{{ $empresa->empresa_nombre }}</td>
				</tr>
				@if(!is_null($empresa->nombre_comercial))
				<tr>
					<td>Nombre Comercial</td><td>:</td><td>{{ $empresa->nombre_comercial }}</td>
				</tr>
				@endif
				<tr>
					<td>RUC</td><td>:</td><td>{{ $empresa->empresa_ruc }}</td>
				</tr>
				<tr>
					<td>Actividad o giro del negocio</td><td>:</td><td>{{ $empresa->empresa_rubro }}</td>
				</tr>
				<tr>
					<td>Domicilio</td><td>:</td><td>{{ $empresa->empresa_direccion }}</td>
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
					<td style="width:5cm;">Nombres y Apellidos</td><td width="1%">:</td><td>{{ $empresa->fac_nombre }} {{ $representante->apellido }}</td>
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
					<td style="text-align: left; font-size: 12px;"><b>GARANTÍA</b></td><td>:</td><td style="text-align: left; font-size: 12px;">S/.{{ $garantia }}</td>
				</tr>
				<tr>
					<td style="text-align: left; font-size: 12px;"><b>FECHA DE PAGO **</b></td><td style="text-align: left; font-size: 12px;">:</td><td>{{ ($empresa->preferencia_facturacion=='MENSUAL')?'Fin de mes':'Quincena del mes' }}</td>
				</tr>
				@if(isset($cw_modulos))
				<tr>
					<td style="text-align: left; font-size: 12px;"><b>DIRECCIÓN DEL SERVICIO CONTRATADO</b></td><td style="text-align: left; font-size: 12px;">:</td><td>Calle Elías Aguirre 180 of. 301 - Miraflores</td>
				</tr>
				<tr>
					<td style="text-align: left; font-size: 12px;"><b>MÓDULOS ASIGNADOS</b></td><td style="text-align: left; font-size: 12px;">:</td><td>{{$cw_modulos[0]->modulos}}</td>
				</tr>
				@endif
			</table>
		</div>
	</div>
	@component('pdf.sign')
	@endcomponent
</div>