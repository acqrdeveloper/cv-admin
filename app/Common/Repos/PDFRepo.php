<?php 
namespace CVAdmin\Common\Repos;
use CVAdmin\Common\Repos\SessionRepo;

use CVAdmin\CV\Models\Contrato;
use CVAdmin\CV\Models\Plan;
use CVAdmin\CV\Models\Empresa;
use CVAdmin\CV\Models\Representante;
use CVAdmin\CV\Models\FacturaItem;
use CVAdmin\CV\Models\EmpresaServicio;

use CVAdmin\Common\Repos\QueryRepo;

class PDFRepo {

	public $sess = null;

	public function __construct(){
		$this->sess = new SessionRepo();
	}

	public function render($empresa_id){

		$empresa = Empresa::where('id', $empresa_id)->firstOrFail(['id','plan_id','empresa_nombre','empresa_ruc','empresa_direccion','empresa_rubro','preferencia_fiscal','preferencia_fiscal_nro_partida', 'fac_nombre', 'fac_apellido', 'fac_domicilio','fac_email','fac_telefono','fac_celular', 'fac_dni', 'preferencia_facturacion','promo']);

		$representante = $empresa->representantes()->first();

		$plan = Plan::where('id', $empresa->plan_id)->firstOrFail(['id','nombre','precio','promocion','promocion_mes','tipo']);

		$contrato = Contrato::where('empresa_id', $empresa->id)->orderBy('id','DESC')->firstOrFail();

		$garantia = ( new QueryRepo )->Q_garantia_list( ["empresa_id" => $empresa->id] );
		$garantia = isset( $garantia["rows"][0] ) ? (array)$garantia["rows"][0] : ["precio" => 0];

		$servicio = EmpresaServicio::where('empresa_id', $empresa->id)->where('tipo','D')->first();
		$servplan = EmpresaServicio::where('empresa_id', $empresa->id)->where('tipo','P')->first();

		$serv_contrat = "";
		if($plan->promocion > 0 && $empresa->promo === 'S'){
			$serv_contrat = "S/." . $plan->promocion. " (" . $this->sess->numeroLetras($plan->promocion) . ")<br><div>Precio promoción de S/." .$plan->promocion. " " .($plan->promocion_mes == 1?"solo por el primer mes </div>":"en los ".$plan->promocion_mes." primeros meses");

			if($plan->promocion_mes>0){
				$serv_contrat .= "<div>A partir del <b>" . $this->postDescuento($plan->promocion_mes) . "</b> el pago será de <b>S/. " . $plan->precio . "</b> (" . $this->sess->numeroLetras($plan->precio) . ")</div>";
			}

		} else if(!is_null($servicio)){
			$tot = number_format(($plan->precio*$servplan->servicio_extra_id) - $servicio->monto,2);
			$serv_contrat = "S/." . $tot . " (" . $this->sess->numeroLetras($tot) . ")<br>" . "<div>Descuento de S/. " . number_format($servicio->monto,2) . " "  . ($servicio->mes == 1?" solo por el primer mes": ($servicio->mes>1?"en los " . $servicio->mes . " primeros meses":"cada mes")) .   " sobre el precio del plan contratado.</div>";
			if($servicio->mes > 0){
				$serv_contrat .= "<div>A partir del <b>" . $this->postDescuento($servicio->mes) . "</b> el pago será de <b>S/. " . ( $plan->precio * $servplan->servicio_extra_id  ) . "</b> (" . $this->sess->numeroLetras($plan->precio) . ")</div>";
			}
		} else {
			$serv_contrat = "S/." . $plan->precio . " (" . $this->sess->numeroLetras($plan->precio) . ")";
		}

		$mes_gratis = ( new QueryRepo )->Q_mesgratiscontrato(array( "empresa_id" => $empresa->id ));

		if(count($mes_gratis['rows'])>0){
			$serv_contrat .= "<div><b>Pagado por " . ($mes_gratis['rows'][0]->periodo - 1) . " meses</b></div>";
		}

		$data = [
			'contrato' => $contrato,
			'empresa'=>$empresa,
			'plan'=>$plan,
			'representante'=>$representante,
			'garantia' => $garantia['precio'] . " (" . ($this->sess->numeroLetras($garantia['precio'])) . ")",
			'servicio' => $servicio,
			'servicio_contratado' => $serv_contrat
		];

		if($plan->tipo == "CW"){
			$data['cw_modulos'] = \DB::table('oficina')->where('empresa_id', $empresa->id)->get([\DB::raw("GROUP_CONCAT(nombre) AS 'modulos'")]);
		}

		$blade = ($plan->tipo=='CW'?'pdf.coworking':'pdf.oficina');

		//return view($blade, $data);
		return \PDF::loadView($blade, $data)->setPaper('a4', 'portrait')->stream();
	}

	public function HTML_comprobante( $comprobante, $detalle, $config, $datos, $montoTotal = 0 , $moddocnum = "", $moddocemi = "", $modmotivo = ""){
		//list( $montoTotal, $montoUnitario, $subTotalVentas, $descuento, $subWarrantyTotal, $warrantyTotal, $descWarranty ) = $datos;

		if( $comprobante["documento_tdocemisor"] == '01' ){
			$comprobante["documento_tdocemisor"] = 'FACTURA';
		}else if( $comprobante["documento_tdocemisor"] == '07' ){
			$comprobante["documento_tdocemisor"] = 'NOTA DE CREDITO';
		}else if( $comprobante["documento_tdocemisor"] == '08' ){
			$comprobante["documento_tdocemisor"] = 'NOTA DE DEBITO';
		}

		$head = '<div>
			        <div style="display: inline-block; width:65%; vertical-align: top;">
			            <div style="line-height:20px; text-align: left; font-weight: bolder;">'.$config["emisor_razonsocial"].'</div>
			            <div style="line-height:20px; text-align: left;">'.$config["emisor_direccion"].'</div>
			            <div style="line-height:20px; text-align: left;">'.strtoupper($config["emisor_distrito"])." - ".strtoupper($config["emisor_provincia"])." - ".strtoupper($config["emisor_departamento"]).'</div>
			        </div>
			        <div style="display: inline-block; width:33%; text-align: right;">
			            <div style="border:1px solid black; font-weight: bolder; text-align: center;">
			                <div style="line-height:20px;">'.$comprobante["documento_tdocemisor"].' ELECTRONICA</div>
			                <div style="line-height:20px;">RUC: '.$config["emisor_ruc"].'</div>
			                <div style="line-height:20px;">'.$comprobante["serie"].'-'.$comprobante["numero"].'</div>
			            </div>
			        </div>
			    </div>';

		$break1 = '<div style="border:0px solid transparent; height: 15px;"></div><div style="border:1px solid black; height: 0px;"></div><div style="border:0px solid transparent; height: 15px;"></div>';

		$comp   = '<div>';
		if( isset($comprobante["fecha_vencimiento"]) && $comprobante["fecha_vencimiento"] != '' ){
			$comp   .= '<div style="line-height: 20px;">
				            <div style="display: inline-block; width:25%; text-align: left;">Fecha de Vencimiento </div>
				            <div style="display: inline-block; width:70%; text-align: left; font-weight: bolder;">: '.$comprobante["fecha_vencimiento"].'</div>
				        </div>';
		}

		$comp   .= '<div style="line-height: 20px;">
			            <div style="display: inline-block; width:25%; text-align: left;">Fecha de Emision     </div>
			            <div style="display: inline-block; width:70%; text-align: left; font-weight: bolder;">: '.$comprobante["fecha_emision"].'</div>
			        </div>
			        <div style="line-height: 20px;">
			            <div style="display: inline-block; width:25%; text-align: left;">Señor(es)            </div>
			            <div style="display: inline-block; width:70%; text-align: left; font-weight: bolder;">: '.$comprobante["empresa_nombre"].'</div>
			        </div>
			        <div style="line-height: 20px;">
			            <div style="display: inline-block; width:25%; text-align: left;">RUC                  </div>
			            <div style="display: inline-block; width:70%; text-align: left; font-weight: bolder;">: '.$comprobante["empresa_ruc"].'</div>
			        </div>
			        <div style="line-height: 20px;">
			            <div style="display: inline-block; width:25%; text-align: left;">Tipo de Moneda       </div>
			            <div style="display: inline-block; width:70%; text-align: left; font-weight: bolder;">: SOLES</div>
			        </div>';
		if( isset($comprobante["docmod"]) && $comprobante["docmod"] != '' ){
			$comp   .= '<div style="line-height: 20px;">
				            <div style="display: inline-block; width:25%; text-align: left;">Doc. que Modifica </div>
				            <div style="display: inline-block; width:70%; text-align: left; font-weight: bolder;">: '.$comprobante["docmod"].'</div>
				        </div>';
		}

		if( isset($comprobante["docmodemision"]) && $comprobante["docmodemision"] != '' ){
			$comp   .= '<div style="line-height: 20px;">
				            <div style="display: inline-block; width:25%; text-align: left;">Doc. se Emitió </div>
				            <div style="display: inline-block; width:70%; text-align: left; font-weight: bolder;">: '.$comprobante["docmodemision"].'</div>
				        </div>';
		}

		$comp   .= '<div style="line-height: 20px;">
			            <div style="display: inline-block; width:25%; text-align: left;">Observación          </div>
			            <div style="display: inline-block; width:70%; text-align: left; font-weight: bolder;">: '.(isset($comprobante["observacion"]) ? $comprobante["observacion"] : '' ).'</div>
			        </div>';
			        
		$comp   .= '</div>';

		$break2 = '<div style="border:0px solid transparent; height: 15px;"></div>';
		$deta   =    '<div>
				        <table style="width: 100%; border-collapse: collapse; border:1px solid black; ">
				            <thead>
				                <tr style="font-weight: bolder; font-size: 13px;">
				                    <td style="padding:3px; border-top:1px solid black; text-align: center;">Cantidad</td>
				                    <td style="padding:3px; border-top:1px solid black; ">Medida</td>
				                    <td style="padding:3px; border-top:1px solid black; ">Descripción</td>
				                    <td style="padding:3px; border-top:1px solid black; text-align: center;">Valor Unitario</td>
				                </tr>
				            </thead>
				            <tbody>';

		if( $comprobante["documento_tdocemisor"] == '07' || $comprobante["documento_tdocemisor"] == '08' ){
			/*
			$deta   .=  '<tr>
		                    <td style="padding:3px; border-top:1px solid black; text-align: center;">1 </td>
		                    <td style="padding:3px; border-top:1px solid black; text-align: left;">UNIDAD </td>
		                    <td style="padding:3px; border-top:1px solid black; text-align: left;">SERVICIO EN OFICINAS VIRTUALES</td>
		                    <td style="padding:3px; border-top:1px solid black; text-align: right;">S/. ' . number_format($subTotalVentas, 2).'</td>
		                </tr>';		*/	
		}else{
			foreach( $datos as $d ){
				$deta   .=  '<tr>
			                    <td style="padding:3px; border-top:1px solid black; text-align: center;">'.$d["cantidad"].' </td>
			                    <td style="padding:3px; border-top:1px solid black; text-align: left;">UNIDAD </td>
			                    <td style="padding:3px; border-top:1px solid black; text-align: left;">'.(isset($d["descripcion_sunat"]) ? $d["descripcion_sunat"] : $d["descripcion"] ).'</td>
			                    <td style="padding:3px; border-top:1px solid black; text-align: right;">S/. ' . number_format(($d["preciosinimpuesto"]*($d["cantidad"] <= 0 ? 1 : $d["cantidad"])), 2).'</td>
			                </tr>';	

			}
		}
		/*
		if ($subTotalVentas > 0) {
			$deta   .=  '<tr>
		                    <td style="padding:3px; border-top:1px solid black; text-align: center;">1 </td>
		                    <td style="padding:3px; border-top:1px solid black; text-align: left;">UNIDAD </td>
		                    <td style="padding:3px; border-top:1px solid black; text-align: left;">'.(isset($detalle[0]['descripcion_sunat']) ? $detalle[0]['descripcion_sunat'] : 'SERVICIO EN OFICINAS VIRTUALES').'</td>
		                    <td style="padding:3px; border-top:1px solid black; text-align: right;">S/. ' . number_format($subTotalVentas, 2).'</td>
		                </tr>';
		}

		if ($subWarrantyTotal > 0) {
			$deta   .=  '<tr>
		                    <td style="padding:3px; border-top:1px solid black; text-align: center;">1 </td>
		                    <td style="padding:3px; border-top:1px solid black; text-align: left;">UNIDAD </td>
		                    <td style="padding:3px; border-top:1px solid black; text-align: left;">'.(isset($descWarranty) ? $descWarranty : 'GARANTIA').'</td>
		                    <td style="padding:3px; border-top:1px solid black; text-align: right;">S/. ' . number_format($subWarrantyTotal, 2).'</td>
		                </tr>';

			$subTotalVentas += $subWarrantyTotal;
			$montoTotal += $warrantyTotal;
		}
		*/
		$montoUnitario = round($montoTotal / 1.18, 2);
		$IGV = $montoTotal - $montoUnitario;
		$deta   .=          '</tbody></table></div>';
		$break3 = '<div style="border:0px solid transparent; height: 10px;"></div>';
		$totale =  '<div>
				        <table style="width: 100%;">
				            <thead><tr><td></td><td</td><td></td><td></td><td</td><td></td></tr></thead>
				            <tbody>
				                <tr>
				                    <td colspan="3"></td>
				                    <td style="text-align: right;">Sub Total Ventas</td>
				                    <td style="text-align: center;">:</td>
				                    <td style="text-align: right; border: 1px solid black;">S/. '.number_format($montoUnitario, 2).'</td>
				                </tr>
				                <tr>
				                    <td colspan="3"></td>
				                    <td style="text-align: right;">Anticipos</td>
				                    <td style="text-align: center;">:</td>
				                    <td style="text-align: right; border: 1px solid black;">S/. 0.00</td>
				                </tr>
				                <tr>
				                    <td style="text-align: right;">Valor de Venta de Operaciones Gratuitas </td>
				                    <td style="text-align: center;">: </td>
				                    <td style="text-align: right; border: 1px solid black;">S/. 0.00</td>
				                    <td style="text-align: right;">Descuentos</td>
				                    <td style="text-align: center;">:</td>
				                    <td style="text-align: right; border: 1px solid black;">S/. 0.00</td>
				                </tr>
				                <tr>
				                    <td colspan="3"></td>
				                    <td style="text-align: right;">Valor de Venta</td>
				                    <td style="text-align: center;">:</td>
				                    <td style="text-align: right; border: 1px solid black;">S/. '.number_format($montoUnitario, 2).'</td>
				                </tr>
				                <tr>
				                    <td colspan="3" style="text-align: left; font-weight: bolder;">SON: '.( new SessionRepo )->numeroLetras($montoTotal).'</td>
				                    <td style="text-align: right;">ISC</td>
				                    <td style="text-align: center;">:</td>
				                    <td style="text-align: right; border: 1px solid black;">S/. 0.00</td>
				                </tr>
				                <tr>
				                    <td colspan="3"></td>
				                    <td style="text-align: right;">IGV</td>
				                    <td style="text-align: center;">:</td>
				                    <td style="text-align: right; border: 1px solid black;">S/. '.number_format($IGV, 2).'</td>
				                </tr>
				                <tr>
				                    <td colspan="3"></td>
				                    <td style="text-align: right;">Otros Cargos</td>
				                    <td style="text-align: center;">:</td>
				                    <td style="text-align: right; border: 1px solid black;">S/. 0.00</td>
				                </tr>
				                <tr>
				                    <td colspan="3"></td>
				                    <td style="text-align: right;">Otros Tributos</td>
				                    <td style="text-align: center;">:</td>
				                    <td style="text-align: right; border: 1px solid black;">S/. 0.00</td>
				                </tr>
				                <tr>
				                    <td colspan="3"></td>
				                    <td style="text-align: right; font-weight:bolder; ">Importe Total</td>
				                    <td style="text-align: center;">:</td>
				                    <td style="text-align: right; border: 1px solid black; font-weight:bolder; font-size:102%;">S/. '.number_format($montoTotal, 2).'</td>
				                </tr>
				            </tbody>
				        </table>
				    </div>';
		$break4 = '<div style="border:0px solid transparent; height: 20px;"></div><div style="border:1px solid black; text-align:center; padding:5px;"> Esta es una representación impresa de la factura electrónica, generada en el Sistema de SUNAT. Puede verificarla utilizando su clave SOL.</div>';

		$html = '<div style=" font-family: monospace; font-size: 12px;">'.$head.$break1.$comp.$break2.$deta.$break3.$totale.$break4.'</div>';

		return $html;
	}

	public function PDF_HTML( $html ){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);
        return $pdf;
	}

	public function postDescuento($mes){
		
		$next = $mes + 1;

		$ordinals = ['','PRIMER','SEGUNDO','TERCER','CUARTO','QUINTO','SEXTO','SEPTIMO','OCTAVO','NOVENO','DECIMO','DECIMO PRIMER','DECIMO SEGUNDO']; 

		if($next>=13){
			return 'SIGUIENTE AÑO';
		}

		return $ordinals[$next] . ' MES';
	}
}
