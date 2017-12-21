<?php 
	$mes = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Setiembre','Octubre','Noviembre','Diciembre'];
?>
<div class="clausule">
	<p style="text-align:right;">Lima, {{ date('d') }} de {{ $mes[date('m')-1] }} de {{ date('Y') }}</p>
</div>
<div class="clausule" style="padding-top: 2.5cm;">
	<table style="width: 100%">
		<tr>
			<td width="50%" style="text-align: center; padding: 0 30px 0 30px;">
				<div style="border-top: 1px dashed #000000;"><b>Centros Virtuales del Perú</b></div>
				<div>RUC Nro. 20546951015</div>
			</td>
			<td width="50%" style="text-align: center; padding: 0 30px 0 30px;">
				<div style="border-top: 1px dashed #000000;"><b>EL COMITENTE</b></div>
				<div>&nbsp;</div>
			</td>
		</tr>
	</table>
</div>