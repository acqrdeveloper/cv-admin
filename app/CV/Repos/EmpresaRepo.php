<?php namespace CVAdmin\CV\Repos;
/**
 * @author Kevin W. Baylon Huerta <kbaylonh@hotmail.com>
 * @author Gonzalo del Portal <gdelportal@sitatel.com>
 */
use CVAdmin\Http\Controllers\Useful;
use Auth;
use Carbon\Carbon;
use DB;
use CVAdmin\Common\Repos\QueryRepo;
use CVAdmin\Common\Repos\SessionRepo;

use CVAdmin\CV\Models\Plan;
use CVAdmin\CV\Models\Factura;
use CVAdmin\CV\Models\Central;
use CVAdmin\CV\Models\Empresa;
use CVAdmin\CV\Models\Oficina;
use CVAdmin\CV\Models\Contrato;
use CVAdmin\CV\Models\FacturaItem;
use CVAdmin\CV\Models\RecursoPeriodo;
use CVAdmin\CV\Models\EmpresaExtras;
use CVAdmin\CV\Models\EmpresaCentral;
use CVAdmin\CV\Models\EmpresaServicio;
use CVAdmin\CV\Models\EmpresaHistorial;

use CVAdmin\CV\Repos\FacturaRepo;
use CVAdmin\CV\Repos\RepresentanteRepo;
use CVAdmin\CV\Repos\ServicioRepo;

class EmpresaRepo {
    use Useful;

    public function getExtras( $empresa_id ){
    	return EmpresaExtras::where( "empresa_id", $empresa_id )->get();
    }

    public function postExtras( $empresa_id, $params ){
    	$exist = EmpresaExtras::where( "empresa_id", $empresa_id )->where( "extra_id", $params["extra_id"] )->first();
    	if( empty( $exist ) ){
    		$return = EmpresaExtras::create( array( 'empresa_id' => $empresa_id, 'extra_id' => $params["extra_id"], 'estado' => $params["estado"] ) );
    	}else{
    		$return = EmpresaExtras::where( "empresa_id", $empresa_id )->where( "extra_id", $params["extra_id"] )->update( array( 'estado' => $params["estado"] ) );
    	}
    	return $return;
    }

    public function create( $params ){
    	$return = [ "empresa" => [], "factura" => [], "representante" => [], "servicio" => [], "contrato" => [], "mail" => [], "ndoc" => [] ];

    	$pass = str_random(8);


    	$return["ndoc"] = Empresa::where( "empresa_ruc", $params["empresa_ruc"] )->first();

    	if(!empty($return["ndoc"]))
    		throw new \Exception("El número de documento " . $params["empresa_ruc"] . " ya está ingresado.");

    	$return["mail"] = Empresa::where( "preferencia_login", $params["representante"]["correo"] )->first();

    	if(!empty($return["mail"]))
    		throw new \Exception("El correo electrónico " . $params["representante"]["correo"] . " ya está ingresado.");
    	$hash = \Hash::make( $pass );
		$empresa = Empresa::create(
			array(
				'plan_id'							=> $params["plan_id"],
				'central_id' 						=> 440,
				'central' 							=> 'N',
				'preferencia_estado'				=> ($params["contrato"]["fecha_inicio"] == date('Y-m-d')?'A':'P'),
				'preferencia_login'					=> $params["representante"]["correo"],
				'preferencia_contrasenia'			=> $pass,
				'preferencia_facturacion'			=> $params["preferencia_facturacion"],
				'preferencia_cdr'					=> '',
				'preferencia_fiscal'				=> $params["preferencia_fiscal"],
				'preferencia_fiscal_nro_partida'	=> $params["preferencia_fiscal_nro_partida"],
				'preferencia_comprobante'			=> $params["preferencia_comprobante"],

				'empresa_nombre' 					=> $params["empresa_nombre"],
				'empresa_ruc' 						=> $params["empresa_ruc"],
				'empresa_direccion' 				=> $params["empresa_direccion"],
				'empresa_rubro' 					=> $params["empresa_rubro"],
				'nombre_comercial' 					=> $params["nombre_comercial"],
				'url_web'  							=> '',

				'fac_nombre'						=> $params["fac_nombre"],
				'fac_apellido'						=> $params["fac_apellido"],
				'fac_dni'							=> $params["fac_dni"],
				'fac_email'							=> $params["fac_email"],
				'fac_telefono'						=> $params["fac_telefono"],
				'fac_celular'						=> $params["fac_celular"],
				'fac_domicilio'						=> $params["fac_domicilio"],

				'moneda' 							=> 'S',
				'asesor'							=> $params["asesor"],
				'fecha_creacion'					=> date("Y-m-d H:i:s"),
				'updated_at'						=> date("Y-m-d H:i:s"),
				'promo' 							=> $params["promo"],
				'flag_encuesta' 					=> 'N',
				'convenio'							=> $params["convenio"],
				'convenio_duration'					=> $params["convenio_duration"],
				'carrera'							=> isset($params["carrera"]) ? $params["carrera"] : "N",
				'password'							=> $hash,
				'api_token'							=> $hash,
			)
		);
		$return["empresa"] = $empresa;

		/*
		if( isset( $params["factura"] ) && is_array( $params["factura"] ) ){
			if( isset( $params["factura"]["items"] ) && is_array( $params["factura"]["items"] ) && count( $params["factura"]["items"] ) > 0 ){
		        $factura = array(
		            "items"             => $params["factura"]["items"],
		            "total"             => $params["factura"]["total"],
		            "fecha_vencimiento" => date( 'Y-m-d', strtotime( "+1 days" ) ),
		            "fecha_limite"      => date( 'Y-m-d', strtotime( "+3 days" ) ),
		            "tipo"              => $empresa["preferencia_comprobante"]
		        );
		        $facturaresp = ( new FacturaRepo )->facturaCreate( $empresa->id, $factura );
		        $return["factura"] = $facturaresp;
			}
		}
		*/

		if( isset( $params['representante'] ) ){
			$rep = $params['representante'];
			$rep["empresa_id"] = $empresa->id;
			$rep["is_login"] = "S";
			(new RepresentanteRepo)->create( $rep );
			$return["representante"] = $rep;
		}

		if( isset( $params["servicios_extras"] ) && is_array( $params["servicios_extras"] ) && count( $params["servicios_extras"] ) > 0 ){
			foreach( $params["servicios_extras"] as $rep ){
				$servicio = (new ServicioRepo)->setEmpresaServicio( $empresa->id, $rep );
				array_push( $return["servicio"], $servicio );
			}
		}

		if( isset( $params["contrato"] ) && is_array( $params["contrato"] ) ){
			$contrato = Contrato::create(
				array(
					'empresa_id'	=> $empresa->id,
					'fecha_inicio'	=> $params["contrato"]["fecha_inicio"],
					'fecha_fin'		=> $params["contrato"]["fecha_fin"],
					'estado'		=> "VIGENTE"
				)
			);
			$return["contrato"] = $contrato;
		}

	    //if( $params["contrato"]["fecha_inicio"] == date("Y-m-d") ){
	    	//FACTURAR AHORA 
	    	//DESCUENTO SI TIENE DISMINUIR EN UN MES DE SER DESCUENTO PERIODICO
	    	( new FacturaRepo )->comprobanteMensual(
	    		array(
					"id" 			=> $empresa->id,
					"plan_id" 		=> $params["plan_id"],
					"comprobante" 	=> $params["preferencia_comprobante"]
	    		), 
	    		date("Y", strtotime( $params["contrato"]["fecha_inicio"] ) ), 
	    		date("m", strtotime( $params["contrato"]["fecha_inicio"] ) ),
	    		true
	    	);
	    //}

    	return $return;
    }

	public function getDeuda( $empresa_id, $estado ){
		return Factura::where( "empresa_id", $empresa_id )->where( "estado", $estado )->get();
	}

	public function changeCiclo($empresa_id, $params){
		$empresa = Empresa::findOrFail($empresa_id);
		$empresa->preferencia_facturacion = $params['preferencia_facturacion'];
		$empresa->save();
		$this->saveHistory([
			'empresa_id' => $empresa->id,
			'estado' => 'N',
			'observacion' => 'Se cambió el ciclo de facturación a ' . $params['preferencia_facturacion']
		]);
	}

	public function changeComprobante($empresa_id, $params){
		$empresa = Empresa::findOrFail($empresa_id);
		$prefix = substr($empresa->empresa_ruc,0,2);
		if( ($prefix != "10" && $prefix != "15" && $prefix != "20") && strlen($empresa->empresa_ruc) != 11  && $params['preferencia_comprobante'] === 'FACTURA'){
			throw new \Exception("La empresa debe contar con un número de RUC correcto.");
		}

		$empresa->preferencia_comprobante = $params['preferencia_comprobante'];
		$empresa->save();
		$this->saveHistory([
			'empresa_id' => $empresa->id,
			'estado' => 'N',
			'observacion' => 'Se cambió el comprobante a ' . $params['preferencia_comprobante']
		]);
	}

	public function getEmpresaRecursoPeriodoHoras( $empresa_id, $ciclo ){
		$anio = date("Y");
		$mes  = date("m");
		if( $ciclo == 'QUINCENAL' ){
			if( date("d") < 15 ){
				$anio = date( "Y", strtotime("-1 months", strtotime( date( "Y-m-d" ) ) ) );
				$mes  = date( "m", strtotime("-1 months", strtotime( date( "Y-m-d" ) ) ) );
			}
		}
		$recurso = RecursoPeriodo::where( "empresa_id", $empresa_id )->where( "anio", $anio )->where( "mes", $mes )->orderByRaw(\DB::raw('anio DESC, mes DESC'))->first();
		empty( $recurso ) ? $return = [ "load" => false, "data" => [] ] : $return = [ "load" => true, "data" => $recurso ];
		return $return;
	}

	public function getFacturaItemGarantia( $empresa_id ){
		$return = ( new QueryRepo )->Q_garantia_disponible( array( "empresa_id" => $empresa_id ) );
		$return["load"] = count( $return["rows"] ) > 0 ? true : false;
		if( $return["load"] ){
			$return["rows"] = $return["rows"][0];
		}
		return $return;
	}

	public function renovarEmpresaContrato( $empresa_id, $param ){
		$return  = [ "load" => false, "message" => "" ];
		$empresa = Empresa::where( "id", $empresa_id  )->first();
		if( !empty($empresa) ){
			$plan    = Plan::where( "id", $param["plan_id"]  )->where( "estado", "A" )->first();
			if( !empty($plan) ){
				Contrato::where( "empresa_id", $empresa_id )->update( array( "estado" => "VENCIDO" ) );
				Contrato::create( 
					array(
						"empresa_id"	=> $empresa_id,
						"fecha_inicio"	=> $param["fecha_inicio"],
						"fecha_fin"		=> $param["fecha_fin"],
						"estado"		=> "VIGENTE"
					) 
				);
				EmpresaServicio::where( "empresa_id", $empresa_id )->where( "tipo", "P" )->update( 
					array(
						"monto" 	=> $plan["precio"],
						"concepto"	=> $plan["nombre"]
					) 
				);
				( new QueryRepo )->Q_comando_update_garantiaPerder($param);
				$return  = [ "load" => true, "message" => "" ];
			}else{
				$return["message"] = "Plan Activo No detectado";	
			}
		}else{
			$return["message"] = "Empresa No detectada";
		}

		return $return;
	}

	public function putEmpresaPlanCambio( $empresa_id, $plan_orig, $plan_nuevo, $param ){
		$return  = [ "load" => false, "message" => "" ];
		$empresa = Empresa::where( "id", $empresa_id  )->first();
		$plan1   = Plan::where( "id", $plan_orig  )->where( "estado", "A" )->first();
		$plan2   = Plan::where( "id", $plan_nuevo )->where( "estado", "A" )->first();
		if( empty( $plan1 ) || empty( $plan2 ) ){
			$return["message"] = "Plan(es) no Encontrado(s), o no Activo(s)";
		}else{
	    	$plan1["precio"] = $plan1["precio"] * 1;
	    	$plan2["precio"] = $plan2["precio"] * 1;
			$factura = Factura::where( "empresa_id" ,$empresa_id )->where( "estado", "PENDIENTE" )->first();
			if( empty( $factura ) && count( $factura ) <= 0 ){
            	if( isset( $param["cantidad"] ) && $param["cantidad"] > 1 ){
            		$plan2["precio"] = $plan2["precio"] * $param["cantidad"];
            	}
				if( $plan1["precio"] != $plan2["precio"]){
					$return["cond1"] = "Precios Diferentes";
                    $garantia = ( new QueryRepo )->Q_garantia_disponible( array( "empresa_id" => $empresa_id ) );
                    if( $garantia["total"] > 0 ){
						$return["cond2"] = "Garantia Mayor a 0 ".$garantia["total"];
						$return["garantia"] = $garantia;
                    	$garantia["rows"] = (array)$garantia["rows"];
                    	$garan = (array)$garantia["rows"][0];
                    	$facgar = Factura::where( "empresa_id" ,$empresa_id )->where( "id", $garan["factura_id"] )->first();
     
						if( $plan1["precio"] > $plan2["precio"] ){
							$return["cond3"] = "Bajo de Plan ".$plan1["precio"]." > ".$plan2["precio"];
							//BAJO DE PLAN
							$contratoI  = \DB::select(\DB::raw("SELECT MIN( fecha_inicio ) AS 'fecha_inicio' FROM contrato where empresa_id = ? LIMIT 1"), [$empresa_id]);
							//Contrato::where( "empresa_id", $empresa_id )->orderBy( "fecha_inicio", "ASC" )->first();
							$contratoF     = Contrato::where( "empresa_id", $empresa_id )->where("estado", "VIGENTE")->orderBy( "fecha_fin", "DESC" )->first();

							if( empty( $contratoI ) || empty( $contratoF ) ){
								$return["message"] = "Hubo un problema con los contratos contacte con sistemas por favor, no se detecto contrato vigente lo que usted requiere es una renovacion";
							}else{
								$contratoI = (array)$contratoI[0];
								$return["cond4"] = "Contratos ".$contratoI["fecha_inicio"]." ".$contratoF["fecha_fin"];
								$inicio    = $contratoI["fecha_inicio"];
								$fin       = date("Y-m-d");/*$contratoF["fecha_fin"];*/
								$datetime1 = new \DateTime($inicio);
								$datetime2 = new \DateTime($fin);
								# obtenemos la diferencia entre las dos fechas
								$interval      = $datetime2->diff($datetime1);
								# obtenemos la diferencia en meses
								$intervalMeses = $interval->format("%m");
								# obtenemos la diferencia en años y la multiplicamos por 12 para tener los meses
								$intervalAnos  = $interval->format("%y") * 12;
								$meses = ( $intervalMeses + $intervalAnos );

								$t = "";
								if( $meses <= 6 ){
									//MENOR A LOS 6 MESES
									//DISMINUIR LA GARANTIA
									//PIERDE PARTE DEL MONTO POR CONTRATO ¬_¬
									$t = "Z";
								}else{
									$t = "W";
								}
								$mt = abs( $plan1["precio"] - $plan2["precio"] );
								FacturaItem::create(
									array(
										"factura_id"				=> $garan["factura_id"],
										"descripcion"				=> $t == "Z" ? "Gastos Administrativos" : "Devolucion de Garantia",
										"descripcion_sunat"			=> "-",
										"precio"					=> $mt,
										"estado"					=> "A",
										"factura_item_temporal_id"	=> 0,
										"warranty"					=> "N",
										"is_nota"					=> "N",
										"anio"						=> date("Y"),
										"mes"						=> date("m"),
										"tipo"						=> $t,
										"custom_id"					=> 0
									)
								);


								FacturaItem::where( "id", $garan["id"] )->where( "tipo", "G" )->update(
		                    		array( 
		                    			"precio" => abs( $plan2["precio"] ) 
		                    		)
		                    	);


								if( $meses > 6 ){
									$mo1 = abs( $plan2["precio"] - $plan1["precio"] );
									$tmpfitem = [];
									if( $mo1 <= 0 ){
										array_push( $tmpfitem, $garan["id"]);
									}
									$return["cond5"] = "Contrato Mayor a 6 meses";
									$params = [
										"monto" 		=> $mo1,
										"anular" 		=> "NO",
										"tipo" 			=> "CREDITO",
										"observacion" 	=> 7,
										"factura_item"  => $tmpfitem,
									];
									if( $facgar["comprobante"] == "FACTURA" ){
										$notanueva = ( new FacturaRepo )->createnota( $empresa_id, $garan["factura_id"], $params );
									}else{
										$notanueva = [
											"load" 		=> true,
											"message" 	=> "Comprobante Tipo ".$facgar["comprobante"]
										];
									}

									$return["notanueva"] = $notanueva;
									if( $notanueva["load"] ){
										$return["load"] = true;
										$return["message"] = "Nota de Credito Creada, actualmente el cliente no cuenta con garantia, su garantia anterior ".$garan["precio"]. " debio de haber sido devuelta, por favor cree manualmente la garantia" ;
									}else{
										$return["message"] = "Ocurrio un Desperfecto al crear la Nota de Credito por favor contacte con Sistemas";
									}
								}else{
									$return["load"] = true;									
								}
							}
						}else if( $plan1["precio"] < $plan2["precio"] ){
							//SUBIO DE PLAN
							$return["cond3"] = "Subio de Plan ".$plan1["precio"]." < ".$plan2["precio"];
							//CREAR NOTA DE CREDITO HACIA LA FACTURA QUE CONTIENE LA GARANTIA
							//LA GARANTIA SERA ESTABLECIDA COMO USADA
							$params = [
								"monto" 		=> $garan["precio"],
								"anular" 		=> "NO",
								"tipo" 			=> "CREDITO",
								"observacion" 	=> 7,
								"factura_item"  => [$garan["id"]],
							];



							if( $facgar["comprobante"] == "FACTURA" ){
								$notanueva = ( new FacturaRepo )->createnota( $empresa_id, $garan["factura_id"], $params );
							}else{
								$notanueva = [
									"load" 		=> true,
									"message" 	=> "Comprobante Tipo ".$facgar["comprobante"]
								];
							}


							$return["notanueva"] = $notanueva;
							if( $notanueva["load"] ){
								//CREAR DEUDA CON NUEVA GARANTIA
								$facturanueva = ( new FacturaRepo )->facturaCreate(
									$empresa_id,
									array(
										"sunat" 			=> "off",
										"tipo" 				=> $empresa["preferencia_comprobante"],
		                           		"fecha_vencimiento" => date( 'Y-m-d', strtotime( "+3 days" ) ),
		                            	"fecha_limite"      => date( 'Y-m-d', strtotime( "+13 days" ) ),
		                            	"total"             => $plan2["precio"],
										"items" 			=> [
											[
												"precio" 	=> $plan2["precio"],
												"tipo" 		=> "G",
												"anio" 		=> date("Y"),
												"mes" 		=> date("m"),
												"descripcion" 		=> "GARANTIA",
												"descripcion_sunat" => "GARANTIA",
												"custom_id" => 0
											]
										]
									)
								);

								$return["factura"] = $facturanueva;
								if( $facturanueva["load"] && isset( $facturanueva["factura"] ) && !empty( $facturanueva["factura"] ) && isset( $facturanueva["factura"]["id"] ) ){
									//CREAR PAGO EN BASE DE LA ANTERIOR GARANTIA
									
									$pagonew = ( new FacturaRepo )->createPago( 
										$empresa_id, 
										$facturanueva["factura"]["id"],  
										array(
											"tipo" 	=> "GARANTIA",
											"monto" => $garan["precio"],
										)
									);
									$return["pago"] = $pagonew;
									if( $pagonew["load"] ){
										$return["load"] = true;
									}else{
										$return["message"] = "Se creo la nota de credito, el comprobante, pero el pago sufrio un fallo por favor contacte con sistemas";	
									}
									
									$return["message"] = "Se creo la nota de credito, el comprobante, y el pago parcial a ese comprobante";
									/*/*/
								}else{
									$return["message"] = "Se creo la nota de credito para la garantia original; pero ocurrio un problema para crear el comprobante contacte con sistemas por favor";
								}
							}else{
								$return["message"] = "Ocurrio un Desperfecto al crear la Nota de Credito por favor contacte con Sistemas";
							}
						}

						if( $return["load"] ){
							EmpresaServicio::where( 'empresa_id', $empresa_id )->where( 'tipo', 'P' )->delete();
							EmpresaServicio::create(
								array(
									"servicio_extra_id"	=> isset( $param["cantidad"] ) ? $param["cantidad"] : 0,
									"empresa_id"		=> $empresa_id,
									"mes"				=> -1,
									"tipo"				=> 'P',
									"monto"				=> $plan2["precio"],
									"concepto"			=> "PLAN"
								)
							);
						}
                    }else{
                    	$return["message"] = "No se Detecto Garantia, no se pude alterar el Plan sin una garantia, lo que usted requiere es una renovacion";
                    }
				}else{
					//PLANES IGUALES NO HACER NADA EXTRA
					$return["load"] = true;
					$return["cond1"] = "Precios Iguales";
				}
				if( $return["load"] ){
					//ACTUALIZAR PLAN
	            	if( $plan1["tipo"] == "CW" ){
	            		Oficina::where( "empresa_id", $empresa_id )->update( array( "empresa_id" => 0 ) );
	            	}
					$updatedata = array();
					$updatedata["plan_id"] = $plan_nuevo;
					if( isset( $param["preferencia_estado"] ) && $param["preferencia_estado"] != "" ){
						$updatedata["preferencia_estado"] = "P";
					}
					Empresa::where( "id", $empresa_id )->update( $updatedata );

					if( isset( $param["fecha_inicio"] ) && $param["fecha_inicio"] != "" && isset( $param["fecha_fin"] ) && $param["fecha_fin"] != "" ){
						Contrato::where( "empresa_id", $empresa_id )->update(
							array(
								"fecha_inicio"  	=> $param["fecha_inicio"],
								"fecha_fin" 		=> $param["fecha_fin"]
							)
						);
					}

				}
			}else{
				$return["message"] = "Deuda(s) Pendiente(s), Detectado(s)";
			}
		}
		return $return;
	}

	public function getEmpresaServicio( $empresa_id ){
		return EmpresaServicio::where( "empresa_id", $empresa_id )->get();
	}

	public function postEmpresaServicio( $empresa_id, $params ){
		$return = array();
		foreach( $params["servicios"] as $s ){
			if( isset( $s["monto"] ) && $s["monto"] > 0 ){
				$new = EmpresaServicio::create(
					array(
						'servicio_extra_id' => isset( $s["servicio_id"] ) ? $s["servicio_id"] : 0,
						'empresa_id' 		=> $empresa_id,
						'mes' 				=> isset( $s["mes"] ) ? $s["mes"] : -1,
						'tipo' 				=> isset( $s["tipo"] ) ? $s["tipo"] : "E",
						'monto' 			=> $s["monto"],
						'concepto' 			=> isset( $s["concepto"] ) ? $s["concepto"] : ""
					)
				);
				array_push( $return, $new );
			}
		}
		return $return;
	}

	public function putEmpresaServicio( $empresa_id, $id, $s ){
		$update = false;
		if( isset( $s["monto"] ) && $s["monto"] > 0 ){
			$update = EmpresaServicio::where( "empresa_id", $empresa_id )->where( "id", $id )->update(
				array(
					'servicio_extra_id' => isset( $s["servicio_id"] ) ? $s["servicio_id"] : 0,
					'mes' 				=> isset( $s["mes"] ) ? $s["mes"] : -1,
					'tipo' 				=> isset( $s["tipo"] ) ? $s["tipo"] : "E",
					'monto' 			=> $s["monto"],
					'concepto' 			=> isset( $s["concepto"] ) ? $s["concepto"] : ""
				)
			);
		}
		return $update;
	}

	public function deleteEmpresaServicio( $empresa_id, $id ){
		$delete = EmpresaServicio::where( "empresa_id", $empresa_id )->where( "id", $id )->delete();
		return $delete;		
	}

	public function updatePlan( $empresa_id, $params ){
		$empresa = Empresa::where( "id", $empresa_id )->update(
			array(
				"plan_id" => $params["plan_id"]
			)
		);
		return $empresa;
	}

	/**
	 * Establece un cdr a una empresa
	 * @param array $params 
	 * @return void
	 */
	public function assignCdr($params){
		$instance = $this;
		DB::transaction(function() use ($params, $instance){
			// Obtener el cdr actual
			$empresa = Empresa::find($params['empresa_id']);
			$cdr = $empresa->preferencia_cdr;

			// Cambiar cdr
			$empresa->preferencia_cdr = $params['cdr'];
			$empresa->save();

			// Guardar historial de empresa
			$instance->saveHistory([
				'empresa_id' => $empresa->id,
				'estado' => 'N',
				'observacion' => 'CDR actualizado a ('.$params['cdr'].'), antes era '.$cdr
			]);
		});
	}

	/**
	 * Retorna la información básica de una empresa
	 * @param int $id ID de la empresa
	 * @return (boolean,array)[]
	 */
	public function basic($id){
		$empresa = $this->getById($id);
		if(is_null($empresa)){
			return ['load'=>false, 'rows'=>[]];
		} else {
			$r = $empresa->toArray();
			$r['representantes'] = $empresa->representantes;
			$r['empleados'] = $empresa->empleados;
			$r['garantia'] = $this->getFacturaItemGarantia( $id );
			$contrato = $empresa->contrato()->orderBy("id","DESC")->first(['id','estado','fecha_inicio','fecha_fin']);

			if(!is_null($contrato)){
				$r['contrato'] = $contrato->toArray();
				$r['contrato']['diferencia'] = (int)(new \DateTime())->diff(new \DateTime($r['contrato']['fecha_fin']))->format('%R%a');
			} else {
				$r['contrato'] = [];
			}

			$entrevistado = "N";
			$ee = EmpresaExtras::where( "empresa_id", $id )->where( "extra_id", 1 )->first();
			if(!empty($ee)){
				$entrevistado = $ee["estado"];
			}
			$r["entrevistado"] = $entrevistado;

			return ["load"=>true, "rows"=>$r];
		}
	}

	public function changeState($id, $params){
		DB::beginTransaction();
		/**/
		DB::commit();
	}

	public function get($params){
		if(isset($params['view']) && $params['view'] == "minimal"){
			$empresas = Empresa::whereRaw("1=1");

			if(isset($params['estado'])){
				$empresas->whereIn('preferencia_estado', explode(",", $params['estado']));
			}

			if(isset($params['empresa_nombre'])){
				$empresas->where('empresa_nombre', 'LIKE', '%'.$params['empresa_nombre'].'%');
			}

			return $empresas->get(['id','empresa_nombre','plan_id'])->take(10);

		} else {
			return ( new QueryRepo )->Q_empresa( $params );
		}
	}

	/**
	 * Obtiene la empresa basado en el id de la central
	 * @param int $id ID de central
	 * @return CVAdmin\CV\Models\Empresa Instancia de la empresa
	 */
	public function getByCentralId($id){
		return Empresa::where('central_id', $id)->first();
	}

    /**
     * Obtiene una empresa con su ID
     * @param int $id ID de la empresa
     * @return CVAdmin\CV\Models\Empresa Instancia de la empresa
     * @throws \Exception
     */
	public function getById($id){
		$empresa = Empresa::find($id);

		if(is_null($empresa))
			throw new \Exception("La empresa no existe.");

		return $empresa;
	}

	/**
	 * Obtiene una empresa con su numero de RUC
	 * @param string $ndoc RUC de la empresa 
	 * @return CVAdmin\CV\Models\Empresa Instancia de la empresa
	 */
	public function getByNDOC($ndoc){
		return Empresa::where( "empresa_ruc", $ndoc )->first();
	}

	public function getCalls( $empresa_id, $values ){
		$v[':empresa_id'] = $empresa_id;

		$sql = "SELECT src, SUBSTRING(dst FROM 3)  as 'numero', IF(accountcode='operator','OPERADORA',accountcode) as 'dst', calldate, SEC_TO_TIME( IF(accountcode!='' AND disposition = 'ANSWERED', billsec, 0) ) as 'duration', IF(disposition!='ANSWERED','No Contestado', IF(accountcode!='','Contestado','No contestado')) as 'disposition' FROM astcdr WHERE userfield = :empresa_id AND DATE(calldate) >= '2017-11-06'";

		if(isset($values['disposicion']) && $values['disposicion'] != 'ALL'){
			if($values['disposicion'] == 'ANSWERED')
				$sql .= ' AND (disposition = "ANSWERED" && accountcode != "" )';
			
			if($values['disposicion'] == 'NO ANSWER')
				$sql .= ' AND ( disposition != "ANSWERED" OR accountcode = "" )';
		}

		if(isset($values['inicio']) && isset($values['fin'])){
			$sql.= 'AND DATE(calldate) BETWEEN :inicio AND :fin';
			$v[':inicio'] = $values['inicio'];
			$v[':fin'] = $values['fin'];
		}

		$sql .= ' ORDER BY uniqueid DESC';

		return DB::connection('pbx')->select(\DB::raw($sql), $v);
	}

	public function getCentral($id){
		$central = Central::find($id);

		if(is_null($central))
			throw new \Exception("La empresa no tiene central.");

		return [
			'numero' => $central->numero,
			'cancion' => $central->cancion,
			'texto' => $central->texto,
			'opciones' => $central->opciones
		];
	}

	public function scheduleEliminacion($empresa_id, $params){
		$empresa = Empresa::findOrFail($empresa_id);

		if(isset($params['delete']) && $params['delete'] == 1){
			$params['fecha_eliminacion'] = null;
			$obs = "Se canceló la programacion para que se elimine la empresa";
		} else {
			$obs = "Se programó para que se elimine la empresa el día ".$params['fecha_eliminacion'];
		}

		$empresa->fecha_eliminacion = $params['fecha_eliminacion'];
		$empresa->save();

		$this->saveHistory([
			'empresa_id' => $empresa->id,
			'estado' => 'N',
			'observacion' => $obs
		]);
	}

	public function saveHistory($params){
		$h = new EmpresaHistorial;
		$h->empresa_id = $params['empresa_id'];
		$h->estado = $params['estado'];
		$h->observacion = $params['observacion'];
		$h->empleado = Auth::user()->nombre;
		$h->fecha = Carbon::now()->format('Y-m-d H:i:s');
		$h->save();
	}

	/**
	 * Actualiza informacion de una empresa INFO , tambien hace un insert a la tabla empresa historial
	 * GONZALO estuvo aqui what upp
	 * @param int $id ID de la empresa
	 * @param array $params Datos de actualizacion
	 * @return void
	 */
	public function updateEmpresa( $id, $params){
		$update = Empresa::where("id", $id)->update(
			array(
				"empresa_nombre" 		=> isset($params["empresa_nombre"]) 	? $params["empresa_nombre"] : '',
				"nombre_comercial" 		=> isset($params["nombre_comercial"]) 	? $params["nombre_comercial"] : '',
				"empresa_direccion" 	=> isset($params["empresa_direccion"]) 	? $params["empresa_direccion"] : '',
				"empresa_ruc" 			=> isset($params["empresa_ruc"]) 	? $params["empresa_ruc"] : '',
				"preferencia_fiscal" 	=> isset($params["preferencia_fiscal"]) 	? $params["preferencia_fiscal"] : '',
				"empresa_rubro" 		=> isset($params["empresa_rubro"]) 	? $params["empresa_rubro"] : '',
				"asesor" 				=> isset($params["asesor"]) 		? $params["asesor"] : '',
				"url_web" 				=> isset($params["url_web"]) 		? $params["url_web"] : '',
				"fac_nombre" 			=> isset($params["fac_nombre"]) 	? $params["fac_nombre"] : '',
				"fac_apellido" 			=> isset($params["fac_apellido"]) 	? $params["fac_apellido"] : '',
				"fac_email" 			=> isset($params["fac_email"]) 		? $params["fac_email"] : '',
				"fac_dni" 				=> isset($params["fac_dni"]) 		? $params["fac_dni"] : '',
				"fac_domicilio" 		=> isset($params["fac_domicilio"]) 	? $params["fac_domicilio"] : '',
				"fac_celular" 			=> isset($params["fac_celular"]) 	? $params["fac_celular"] : '',
				"fac_telefono" 			=> isset($params["fac_telefono"]) 	? $params["fac_telefono"] : '',
				"carrera"				=> isset($params["carrera"]) 		? $params["carrera"] : "N",
			)
		);
		$this->saveHistory( array( "empresa_id" => $id, "estado" => "N", "observacion" => "Actualizado de datos." ) );
		return ["load" => $update];
	}

	/**
	 * Actualiza informacion de una empresa relacionado a la entrevista
	 * @param int $id ID de la empresa
	 * @param array $params Datos de actualizacion
	 * @return void
	 */
	public function updateInterview($id, $params){
		DB::beginTransaction();

		// Actualizado el estado de entrevista
		$empresa = Empresa::find($id);
		$empresa->entrevistado = $params['estado'];

		if($params['estado'] == 'P'){
			$empresa->entrevista_recordatorio = $params['fecha'];
		}

		$empresa->save();

		// Enviar a Pusher...
		$message = $params['estado'] == 'S' ? 'Esta empresa quedó como entrevistado.' : 'Empresa establecido en No Interesado.';

		$empresas = [
            'id' => $id,
            'entrevistado' => $params['estado']
		];

		if($params['estado'] == 'P'){
			$message = 'postergó la entrevista para ' . $params['fecha'] . ' de';
			$empresas['entrevista_recordatorio'] = $params['fecha'];
		}

		(new SessionRepo)->Notification([
				'empresas' => $empresas,
				'id'=>$empresa->id, 
				'empresa_nombre'=>$empresa->empresa_nombre
			],
			$message, 
			Auth::user()->nombre, 
			'Empresa');		
		DB::commit();
	}







	/**
	 * NO DA HORAS SOLO GENERA DEUDA EN FACTURACION TEMPORAL Y ACTIVA LA EMPRESA
	 * @param int $empresa_id ID de la empresa
	 * @param array $params parametros de actualizacion
	 * @return void
	 */
	public function activarAhoraProrateo( $empresa_id ){
		$sql = "CALL AL_EMPRESA_ACTIVAR_AHORA( ? )";
		$val = [ $empresa_id ];
        $ret = (\DB::select(\DB::raw($sql), $val));
        /* 
        //GENERAR DOCUMENTO AHORA AUN EN DEBATE
        if( $ret[0]["load"] >= 1 ){
        	$emp = Empresa::where( 'id', $empresa_id )->first();
        	if( !empty( $emp ) ){
        		( new FacturaRepo )->comprobanteMensual(
        			array(
						"id" 			=> $emp["id"],
						"plan_id" 		=> $emp["plan_id"],
						"comprobante" 	=> $emp["preferencia_comprobante"]        				
        			)
        		);
        	}
        }
        */
		return $ret[0];
	}


	/**
	 * Actualiza el estado de una empresa
	 * @param int $id ID de la empresa
	 * @param array $params parametros de actualizacion
	 * @return void
	 */
	public function updateState($id, $params){
		DB::beginTransaction();
		$empresa = $this->getById($id);
		$empresa->preferencia_estado = $params['estado'];
		$empresa->fecha_eliminacion = "";
		if ($params['estado'] == 'S'){
			$d = 'Inactivo por ';
		}elseif ($params['estado'] == 'E'){
			$empresa->fecha_eliminacion = date("Y-m-d");
			$d = 'Eliminado por ';
		}else{
			$d = ' por ';
		}
		//else
		//	$d = 'Activado por ';
		//	$this->isProrrateo($empresa);
		$empresa->save();


		$this->saveHistory([
			'empresa_id' => $empresa->id,
			'estado' => 'N',
			'observacion' => $d
		]);
		DB::commit();
	}

	public function testProrrateo($id){
		$empresa = $this->getById($id);
		return $this->isProrrateo($empresa);
	}

	private function isProrrateo( $empresa ){
		$anio = date("Y");
		$mes  = date("m");
		if( $empresa["preferencia_facturacion"] == 'QUINCENAL' ){
			if( date("d") < 15 ){
				$anio = date( "Y", strtotime("-1 months", strtotime( date( "Y-m-d" ) ) ) );
				$mes  = date( "m", strtotime("-1 months", strtotime( date( "Y-m-d" ) ) ) );
			}
		}

		$plan = Plan::where("id", $empresa["plan_id"])->first();
		$empresa_id = $empresa["id"];
		$recurso = RecursoPeriodo::where( "empresa_id", $empresa_id )->where( "anio", $anio )->where( "mes", $mes )->orderByRaw(\DB::raw('anio DESC, mes DESC'))->first();
 		$factura = array();
 		$facturaresp = array();
		if( empty( $recurso ) || count( $recurso ) <= 0 ){
			$now  = strtotime(date("Y-m-d"));
			$anio = date("Y");
			$mes  = date("m");
			if( $empresa["preferencia_facturacion"] == 'QUINCENAL' ){
				if( date("d") < 15 ){
					$diastrabajo = floor( ( strtotime( date("Y-m-15") ) - strtotime( date( "Y-m-d" ) ) ) / ( 60 * 60 * 24 ) );
					$anio = date( "Y", strtotime("-1 months", strtotime( date( "Y-m-d" ) ) ) );
					$mes  = date( "m", strtotime("-1 months", strtotime( date( "Y-m-d" ) ) ) );
					$desde = date($anio."-".$mes."-15");
					$hasta = date("Y-m-14");
				}else{
					$anio = date( "Y", strtotime("+1 months", strtotime( date( "Y-m-d" ) ) ) );
					$mes  = date( "m", strtotime("+1 months", strtotime( date( "Y-m-d" ) ) ) );
					$diastrabajo = floor( ( strtotime( date($anio."-".$mes."-15") ) - strtotime( date( "Y-m-d" ) ) ) / ( 60 * 60 * 24 ) );
					$desde = date("Y-m-15");
					$hasta = date($anio."-".$mes."-14");
				}
			}else{
				$diastrabajo = floor( ( strtotime( date("Y-m-t") ) - strtotime( date( "Y-m-d" ) ) ) / ( 60 * 60 * 24 ) );
				$desde = date($anio."-".$mes."-1");
				$hasta = date("Y-m-t");
			}
			$diastotal = floor( ( strtotime( $hasta ) - strtotime( $desde ) ) / ( 60 * 60 * 24 ) );
			$porcentaj = ( $diastrabajo ) / $diastotal;


            $plan      = 0;
            $extra     = 0;
            $descuento = 0;
            //OBTENER DEUDA FIJA MENSUAL
            $servicios = EmpresaServicio::where( "empresa_id", $empresa_id )->get();
            if( !empty( $servicios ) && count( $servicios ) > 0 ){
                foreach( $servicios as $s ){
                    if( $s["tipo"] == "E" ){
                        $extra     = $extra     + abs( $s["monto"] );
                    }else if( $s["tipo"] == "D" ){
                        $descuento = $descuento + abs( $s["monto"] );
                    }else if( $s["tipo"] == "P" ){
                        $plan      = $plan      + abs( $s["monto"] );
                    }
                }
            }

            if( $descuento > 0 ){
                $plan = $plan - ( $plan * $descuento / 100 );
            }

			$precio =  round( ( ( $plan + $extra ) * $porcentaj ) ,2 );
			$hreun  =  round( ( $plan["horas_reunion"] * $porcentaj ) ,2 );
			$hpriv  =  round( ( $plan["horas_privada"] * $porcentaj ) ,2 );
			$hcapa  =  round( ( $plan["horas_capacitacion"] * $porcentaj ) ,2 );

    		$detalle = array();
            array_push( $detalle, 
                array(
                    "precio"            => ( 1 ),
                    "descripcion"       => "HORAS EXTRAS PRORRATEO",
                    "custom_id"         => $hreun,
                    "descripcion_sunat" => "SERVICIO EN OFICINAS VIRTUALES",
                    "estado"            => "A",
                    "anio"              => $anio,
                    "mes"               => $mes,
                    "tipo"              => "HO"
                )
            );
            array_push( $detalle, 
                array(
                    "precio"            => ( 1 ),
                    "descripcion"       => "HORAS EXTRAS PRORRATEO",
                    "custom_id"         => $hpriv,
                    "descripcion_sunat" => "SERVICIO EN OFICINAS VIRTUALES",
                    "estado"            => "A",
                    "anio"              => $anio,
                    "mes"               => $mes,
                    "tipo"              => "HR"
                )
            );
            array_push( $detalle, 
                array(
                    "precio"            => ( $precio - 2 ),
                    "descripcion"       => "PLAN PRORRATEO",
                    "custom_id"         => 0,
                    "descripcion_sunat" => "SERVICIO EN OFICINAS VIRTUALES",
                    "estado"            => "A",
                    "anio"              => $anio,
                    "mes"               => $mes,
                    "tipo"              => "P"
                )
            );
            $factura = array(
                "items"             => $detalle,
                "total"             => ( $precio ),
                "fecha_vencimiento" => date( 'Y-m-d', strtotime( "+1 days" ) ),
                "fecha_limite"      => date( 'Y-m-d', strtotime( "+3 days" ) ),
                "tipo"              => $empresa["preferencia_comprobante"]
            );
            //echo "<pre>";
            //print_r( $factura );
            //$diastrabajo
			//$diastotal
            //echo "</pre>";
            $facturaresp = ( new FacturaRepo )->facturaCreate( $empresa_id, $factura );

		}else{
			//echo "<pre>";
			//print_r( $plan );
			//echo "</pre>";
			//echo "<pre>";
			//print_r( $recurso );
			//echo "</pre>";
		}
		return ["factura" => $facturaresp ];

	}
	/**
	 * Actualiza el estado CRM de una empresa
	 * @param int $id ID de la empresa
	 * @param array $params parametros de actualizacion
	 * @return void
	 */
	public function updateCRMState($id, $params){
		DB::beginTransaction();
		$update = Empresa::where( "id", $id )->update(
			array(
				"crm_estado" => $params["crm_estado"]
			)
		);
		DB::commit();
		return [ "load" => $update ];
	}

    /**
     * metodo que devuelve data sobre el historial de las acciones segun sea el parametro por entero recibido
     * @param $getparams : arreglo de parametros recibidos
     * @return array
     */
    function _getListTipoHistorial($getparams)
    {
        try {
            $arraypush = [];
            switch ((int)$getparams['tipo']) {
                case 3:
                	/*
                    $sql = " SELECT id,tipo, fecha_registro, descripcion, cantidad, empleado, observacion, oficina FROM recursos_historial WHERE recursos_id = ? AND estado = 'E' ORDER BY  fecha_registro DESC ";
                    if (isset($getparams['recurso_id'])) {
                        array_push($arraypush, $getparams['recurso_id']);
                    }
                    */
                    $sql = "SELECT a.*, r.fecha_reserva, r.hora_inicio, r.hora_fin, o.nombre AS 'oficina_nombre', m.nombre AS 'modelo', r.id FROM ( 
							SELECT anio, mes, reserva_id, fecha, horas_reunion, horas_privada, cantidad_copias, cantidad_impresiones, estado,usuario FROM recurso_periodo_historial WHERE empresa_id = ? 
						) a LEFT JOIN reserva r 
							LEFT JOIN oficina o 
								LEFT JOIN modelo m ON m.id = o.modelo_id  AND m.id = 1
							ON o.id = r.oficina_id
						ON r.id = a.reserva_id and r.id > 0
						 ORDER BY a.fecha DESC";
					if (isset($getparams['empresa_id'])) {
                        array_push($arraypush, $getparams['empresa_id']);
                    }
                    break;
                case 2:
                    $sql = " SELECT id,fecha_inicio, fecha_fin, observacion, created_at FROM contrato_historial WHERE contrato_id = ?  ORDER BY created_at DESC ";
                    if (isset($getparams['contrato_id'])) {
                        array_push($arraypush, $getparams['contrato_id']);
                    }
                    break;
                default:
                    $sql = " SELECT id,estado, observacion, fecha, empleado FROM empresa_historial WHERE empresa_id = ?  ORDER BY fecha DESC";
                    if (isset($getparams['empresa_id'])) {
                        array_push($arraypush, $getparams['empresa_id']);
                    }
                    break;
            }

            $query = " SELECT SQL_CALC_FOUND_ROWS * FROM (" . $sql . ") x ";

            if (isset($getparams["limite"])) {
                if (isset($getparams["pagina"]) && $getparams["pagina"] > 0) {
                    $query .= " LIMIT " . (($getparams["pagina"] - 1) * $getparams["limite"]) . "," . $getparams["limite"];
                } else {
                    $query .= " LIMIT " . $getparams["limite"];
                }
            }

            $rows = DB::select(DB::raw($query), $arraypush);
            $tota = DB::select(DB::raw("SELECT FOUND_ROWS() AS 'rows' "));

            $this->rpta = ['load' => true, "rows" => $rows, "total" => $tota[0]->rows];

        } catch (\PDOException $e) {
            $this->returnCatch($e);
        }
        return $this->rpta;
    }
}
