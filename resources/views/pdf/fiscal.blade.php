<div class="body-page">
	<div style="font-weight: bold;text-align:center;margin-bottom:30px;text-decoration: underline;">ANEXO {{$anexo}} (solo para clientes con domicilio fiscal)</div><br>
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
	</div>
	<div class="clausule">
		<div class="subTitle">3 .- DOMICILIO FISCAL</div>
		<div class="paragraph">
			<div>CENTROS VIRTUALES otorga el servicio de domicilio fiscal a favor de EL COMITENTE bajos las siguientes condiciones:</div>
			<table>
				<tr>
					<th>a)</th>
					<td>El domicilio fiscal declarado por EL COMITENTE tiene como finalidad facilitar el pleno y cabal cumplimiento de sus obligaciones tributarias, y facilitar la labor de comunicación y fiscalización de las entidades competentes del Estado Peruano, en tal sentido, está terminantemente prohibido su uso con propósitos de evasión, elusión y/o cualquier otro acto sancionado por ley.</td>
				</tr>
				<tr>
					<th>b)</th>
					<td>CENTROS VIRTUALES, no asume responsabilidad alguna respecto a las obligaciones de carácter tributario de EL COMITENTE, en consecuencia, no podrá ser afectado por medidas cautelares o procesos de fiscalización que se lleven a cabo contra EL COMITENTE.</td>
				</tr>
				<tr>
					<th>c)</th>
					<td>EL COMITENTE declara que, ha contratado los servicios de CENTROS VIRTUALES para el uso temporal (por horas) de oficinas y algunos servicios complementarios detallados en el Anexo 1, dejando en claro que la integridad de bienes e instalaciones al interior de las oficinas indicadas en la cláusula primera del contrato, son de exclusiva propiedad de CENTROS VIRTUALES.</td>
				</tr>
				<tr>
					<th>d)</th>
					<td>EL COMITENTE está obligado a dar de baja o variar el domicilio fiscal facilitado por CENTROS VIRTUALES, dentro del plazo de 72 horas de haber vencido el plazo del presente contrato, en caso contrario, CENTROS VIRTUALES podrá proceder conforme a lo indicado en el numeral siguiente.</td>
				</tr>
			</table>
		</div>
	</div>
</div>
<div class="page_break"></div>
<div class="body-page">
	<div class="clausule">
		<div class="subTitle">4 .- RESOLUCIÓN CONTRACTUAL</div>
		<div class="paragraph">
			<div>Por medio del presente documento EL COMITENTE autoriza a CENTROS VIRTUALES la resolución unilateral y extrajudicial del presente contrato, así como la inmediata suspensión del servicio indicado en el presente anexo, pudiendo comunicar a la entidad competente que el domicilio fiscal declarado por EL COMITENTE ha variado o debe ser dado de baja, cuando se den cualquiera de las siguientes circunstancias:</div>
			<table>
				<tr>
					<th>a)</th>
					<td>Que, habiendo culminado el plazo del contrato, EL COMITENTE no haya cumplido con proceder a declarar la baja o variación del domicilio fiscal dentro del plazo de 72 horas, conforme a lo indicado en el literal "d" de la cláusula precedente.</td>
				</tr>
				<tr>
					<th>b)</th>
					<td>Que, la autoridad competente haya iniciado un proceso de revisión y/o fiscalización contra EL COMITENTE por supuestos incumplimientos de obligaciones tributarias.</td>
				</tr>
				<tr>
					<th>c)</th>
					<td>Que, EL COMITENTE se encuentre inmerso en un proceso de Reclamación Tributaria o cobranza coactiva por supuesto incumplimiento de obligaciones tributarias.</td>
				</tr>
				<tr>
					<th>d)</th>
					<td>Que, existan resolución emitidas por la autoridad competente que sancionen el incumplimiento de obligaciones tributarias.</td>
				</tr>
				<tr>
					<th>e)</th>
					<td>Que, EL COMITENTE haya sido requerido por la entidad competente para sustentar ingresos supuestamente no declarados (cartas inductivas u otros requerimientos)</td>
				</tr>
				<tr>
					<th>f)</th>
					<td>Que, EL COMITENTE utilice la dirección de la empresa como dirección fiscal para constituir su empresa.</td>
				</tr>
				<tr>
					<th>g)</th>
					<td>Que, EL COMITENTE reciba retirados Memorándum por el incumplimiento de las normas de convivencia.</td>
				</tr>
			</table>
		</div>
	</div>
	<div class="clausule">
		<div class="paragraph">Así mismo, CENTROS VIRTUALES podrá valerse de esta cláusula ante cualquier irregularidad o situación que comprometa a EL COMITENTE con el pago o cumplimiento de obligaciones frente a cualquier entidad del Estado Peruano.</div>
	</div>
	@component('pdf.sign')
	@endcomponent
</div>