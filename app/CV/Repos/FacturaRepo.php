<?php
namespace CVAdmin\CV\Repos;
ini_set('max_execution_time', 3000);
use CVAdmin\CV\Models\Pago;
use CVAdmin\CV\Models\Plan;
use CVAdmin\CV\Models\Empresa;
use CVAdmin\CV\Models\RecursoPeriodo;
use CVAdmin\CV\Models\Reserva;
use CVAdmin\CV\Models\ReservaDetalle;
use CVAdmin\CV\Models\Factura;
use CVAdmin\CV\Models\FacturaNota;
use CVAdmin\CV\Models\FacturaItem;
use CVAdmin\CV\Models\FacturaHistorial;
use CVAdmin\CV\Models\FacturaTemporal;
use CVAdmin\CV\Models\EmpresaServicio;
use CVAdmin\CV\Models\RecursoPeriodoHistorial;

use CVAdmin\CV\Repos\EmpresaRepo;
use CVAdmin\Common\Repos\SessionRepo;
use CVAdmin\Common\Repos\QueryRepo;
use CVAdmin\Common\Repos\PDFRepo;

use CVAdmin\CV\Repos\ConfiguracionRepo;
class FacturaRepo{


    public function changeComprobante( $empresa_id, $facturaID, $comprobante ){
        $factura = Factura::where( "empresa_id", $empresa_id )->where( "id", $facturaID )->first();
        if( !empty($factura) ){
            if($factura["numero"]==""){
                $this->facturaHistorialAdd( $empresa_id, $facturaID, 0, "CAMBIO DE COMPROBANTE", $comprobante  );
                return Factura::where( "empresa_id", $empresa_id )->where( "id", $facturaID )->update(
                    array(
                        "comprobante" => $comprobante
                    )
                );
            }else{
                throw new \Exception("El Comprobante ya posee Numero no puede Alterarse");    
            }
        }else{
            throw new \Exception("El Comprobante no existe");
        }
    }

    /**
     * generaComprobante Mensual de acuerdo al Contrato
     * @param array $emp arreglo
     * @param int $emp["id"] ID de la empresa
     * @param int $emp["plan_id"] ID del plan
     * @param string $emp["comprobante"] TIPO DE COMPROBANTE
     * @return void
     */

    public function comprobanteMensual( $emp, $anio = 0, $mes = 0, $garantia = false ){
        $plan      = 0;
        $extra     = 0;
        $descuento = 0;
        $combos    = [];
        $extrarray = [];
        $emp = (array)$emp;
        //echo "<pre>";
          //  print_r( $emp );
        //echo "</pre>";
        //OBTENER DEUDA FIJA MENSUAL
        $servicios = EmpresaServicio::where( "empresa_id", $emp["id"] )->get();
        if( !empty( $servicios ) && count( $servicios ) > 0 ){
            foreach( $servicios as $s ){
                if( $s["tipo"] == "E" ){
                    $extra     = $extra     + abs( $s["monto"] );
                    array_push( 
                        $extrarray, 
                        [
                            "monto"         => abs( $s["monto"] ),
                            "descripcion"   => $s["concepto"],
                            "tipo"          => "E",
                            "custom_id"     => 0
                        ]
                    );
                }else if( $s["tipo"] == "D" ){
                    $descuento = $descuento + abs( $s["monto"] );
                }else if( $s["tipo"] == "P" ){
                    $plan      = $plan      + ( abs( $s["monto"] ) * ($s["servicio_extra_id"] > 0 ? $s["servicio_extra_id"] : 1) );
                }else if ( $s["tipo"] == "C" ){
                    array_push( $combos, [
                        "custom_id"     => $s["servicio_extra_id"],
                        "monto"         => $s["monto"],
                        "concepto"      => $s["concepto"],
                    ]);
                }
            }
        }
        if($garantia){
            $garantia = $plan;    
        }else{
            $garantia  = 0;
        }
        
        //try{
            //FACTURACION TEMPORAL OBTENER LISTADO IRAN AL MONTO EXTRAS
            $temporals = FacturaTemporal::where( "empresa_id", $emp["id"] )->where( "estado", "PENDIENTE" )->get();
            if( !empty( $temporals ) && count( $temporals ) > 0 ){
                foreach( $temporals as $t ){
                    $extra     = $extra     + abs( $t["precio"] );

                    array_push( 
                        $extrarray, 
                        [
                            "monto"         => abs( $t["precio"] ),
                            "descripcion"   => $t["descripcion"],
                            "tipo"          => "T",
                            "custom_id"     => $t["id"]
                        ]
                    );


                }
                /*
                echo "<pre>-------------------------------temporals------------------------------";
                    print_r( $temporals );
                echo "</pre>";*/
            }
            if( $plan > 0 || $extra > 0 ){
                //echo "____---___".$plan."-".$extra."-".$descuento."-";
                //BAJAR EL MONTO DEL PLAN EN BASE AL PORCENTAJE DEL DESCUENTO
                //DESCUENTO SOLO SE APLICA AL PLAN NO A EXTRAS
                if( $descuento > 0 ){
                    //$plan = $plan - ( $plan * $descuento / 100 );CUANDO ERA POR PORCENTAJE
                    $plan = $plan - $descuento; //AHORA QUIEREN POR MONTO ESTATICO LAS INDECISAS
                }
                //GENERAR DEUDA 
                $detalle = array();
                if( $plan > 0 ){
                    array_push( $detalle, 
                        array(
                            "precio"            => ( $plan ),
                            "descripcion"       => "SERVICIO EN OFICINAS VIRTUALES",
                            "custom_id"         => $emp["plan_id"],
                            "descripcion_sunat" => "SERVICIO EN OFICINAS VIRTUALES",
                            "estado"            => "A",
                            "anio"              => $anio > 0 ? $anio : date( "Y" ),
                            "mes"               => $mes  > 0 ? $mes  : date( "m" ),
                            "tipo"              => "P"
                        )
                    );
                }
                if( $garantia > 0 ){
                    array_push( $detalle, 
                        array(
                            "precio"            => ( $garantia ),
                            "descripcion"       => "GARANTIA",
                            "custom_id"         => 0,
                            "descripcion_sunat" => "GARANTIA",
                            "estado"            => "A",
                            "anio"              => 0,
                            "mes"               => 0,
                            "tipo"              => "G"
                        )
                    );
                }
                if( $extra > 0 ){
                    foreach( $extrarray as $e ){
                        array_push( $detalle, 
                            array(
                                "precio"            => $e["monto"],/*( $extra ),*/
                                "descripcion"       => $e["descripcion"],/*"SERVICIO EXTRAS",*/
                                "custom_id"         => $e["custom_id"],
                                "descripcion_sunat" => "SERVICIO EN OFICINAS VIRTUALES - EXTRAS",
                                "estado"            => "A",
                                "anio"              => 0,
                                "mes"               => 0,
                                "tipo"              => $e["tipo"]
                            )
                        );
                    }

                }
                if( count( $combos ) > 0 ){
                    foreach( $combos as $c ){
                        array_push( $detalle, 
                            array(
                                "precio"            => $c["monto"],
                                "descripcion"       => $c["concepto"],
                                "custom_id"         => $c["custom_id"],
                                "descripcion_sunat" => $c["concepto"],
                                "estado"            => "A",
                                "anio"              => date( "Y" ),
                                "mes"               => date( "m" ),
                                "tipo"              => "C"
                            )
                        );
                    }
                }

                $factura = array(
                    "items"             => $detalle,
                    "total"             => ( $plan + $extra ),
                    "fecha_vencimiento" => date( 'Y-m-d', strtotime( "+3 days" ) ),
                    "fecha_limite"      => date( 'Y-m-d', strtotime( "+13 days" ) ),
                    "tipo"              => $emp["comprobante"]
                );

                $resp = $this->facturaCreate( $emp["id"], $factura );


                /*
                echo "<pre>...........................factura...........................";
                    print_r( $resp );
                echo "</pre>";
                */

                $query = "UPDATE empresa_servicio SET mes = mes - 1 WHERE mes > 0 AND empresa_id = ? ";
                \DB::statement( $query, [$emp["id"]] );
                if( !empty( $temporals ) && count( $temporals ) > 0 ){
                    $factura_id = 0;
                    if( isset( $resp["factura"] ) ){
                        if( isset($resp["factura"]["id"]) ){
                            $factura_id = $resp["factura"]["id"];
                        }
                    }
                    foreach( $temporals as $t ){
                        FacturaTemporal::where( "id", $t["id"] )->where( "empresa_id", $emp["id"] )->update( 
                            array( 
                                "estado"        => "FACTURADO",
                                "factura_id"    => $factura_id
                            ) 
                        );
                    }
                }


            }else{
                /*
                print_r( $plan );
                print_r( "----------".$extra );
                */
            }
            //DISMINUIR EN UN MES A EMPRESA SERVICIO DONDE MES SEA > -1

            //ELIMINAR A EMPRESA SERVICIO DONDE MES = 0
            EmpresaServicio::where( "empresa_id", $emp["id"] )->where( "mes", 0 )->delete();

            //FACTURACION TEMPORAL DE HABER HABIDO ELEMENTOS PONERLOS EN ESTADO FACTURADO
        //}catch(\Exception $ex){
            //print_r( $ex->getLine()." - ".$ex->getMessage()." - ".$ex->getFile() );
        //}
    }


    public function getFacturacionTemporal($empresa_id){
        return FacturaTemporal::where("empresa_id", $empresa_id)->where( "estado", "PENDIENTE" )->get();
    }
    public function setFacturacionTemporal( $params, $empresa_id){
        return FacturaTemporal::where("empresa_id", $empresa_id)->where("id", $params["id"] )->update(
            array(
                "descripcion" => $params["descripcion"],
                "precio"      => $params["precio"]
            )
        );
    }
    public function deleteFacturacionTemporal( $id, $empresa_id){
        return FacturaTemporal::where("empresa_id", $empresa_id)->where("id", $id )->update( array( "estado" => "ANULADO" ) );
    }
    public function facturadoFacturacionTemporal( $id, $empresa_id){
        return FacturaTemporal::where("empresa_id", $empresa_id)->where("id", $id )->update( array( "estado" => "FACTURADO" ) );
    }

    public function garantiaList($empresa_id){
        $data = ( new QueryRepo )->Q_garantia_list( array( "empresa_id" => $empresa_id ) );
        return $data;
    }
    /**
     * Edita el numero de la boleta de venta
     * @param int $factura_id ID de la boleta de venta 
     * @param int $empresa_id ID de la empresa
     * @param string $numero Numero de boleta
     * @return void
     */
    public function addInvoiceNumber($empresa_id, $factura_id, $numero){
        $f = Factura::find($factura_id);
        if($f->empresa_id != $empresa_id)
            throw new \Exception("La factura no corresponde a la empresa");
        //var_dump($f);
        $f->numero = $numero; 
        $f->save();
    }

    /**
     *Envio Masivo de Facturas.
     *Posible falla: el numero se asignara pero puede que la Sunat devuelva que no pudo subirse
     *Posible falla: filtro de validacion puede quedarse atorado tabla configuracion si alguien mueve algo durante el envio.
     * @param array ninguno todo es por base de Datos.
    **/
    public function send_facturame()
    {
        $ismassive = ( new ConfiguracionRepo )->GetValor('MASIVE_INVOICE')["valor"];
        if( $ismassive == 'N' ){
            ( new ConfiguracionRepo )->toggleFacturaMasiva();
            $config = (array)json_decode( ( new ConfiguracionRepo )->GetValor('SUNAT_PARAMS')["valor"] );
            $data = ( new QueryRepo )->Q_facturasmasivo( true );
            foreach( $data["rows"] as $d ){
                $d = (array)$d;
                $dn = "";
                if( isset( $d["numero"] ) && strpos( $d['numero'], "-" ) !== false ){
                    $partnum = explode( "-", $d["numero"] );
                    $dn = $partnum[1];
                }

                $d['empresa_ruc'] = substr($d['empresa_ruc'], 0, 11);
                if( strlen( $d['empresa_ruc'] ) == 11 ){
                    list( $result, $numberValue, $documento_serie, $documento_numero, $values, $detalle ) = $this->factura_config( $d, $config, $dn );
                    $numeroFactura = $documento_serie . '-' . $documento_numero;
                    $values["id"]                 = $d["id"];
                    $values["empresa_ruc"]        = $d["empresa_ruc"];
                    $values["empresa_nombre"]     = $d["empresa_nombre"];
                    $values["fac_email"]          = $d["fac_email"];
                    $values["fac_nombre"]         = $d["fac_nombre"];
                    $values["fecha_vencimiento"]  = $d["fecha_vencimiento"];
                    $this->sendSunatandMail( $values, $documento_serie, $documento_numero, $detalle, $config, false, $numberValue, $d );
                }
            }
            ( new ConfiguracionRepo )->toggleFacturaMasiva(false);
        } else {
            echo "Hay un envio en proceso";
        }
    }

    /**
     * Crea una nueva factura y envia a Sunat si el usuario lo requiere.
     * @param array $params Datos para crear la factura.
    **/
    public function facturaCreate( $empresa_id, $params )
    {
        $result = ["load" => false, "message" => ""];
        if( $empresa_id > 0 && ( $params["tipo"] == "FACTURA" || $params["tipo"] == "BOLETA" || $params["tipo"] == "PROVICIONAL" ) ){    
            $result = $this->validateDetalle( $empresa_id, $params["items"] );
            if( $result["load"] ){
                if( $result["total"] > 0 ){
                    /*
                        Columna Accion Tabla Factura Se Volvera innecesario debido a que ahora las acciones iran en el detalle
                        P  => Al pagar se le da sus horas y copias de su plan, 
                        A  => pago por adelantado ya el fin de mes al pagar su factura se le da sus horas y copias, 
                        T  => Se actualiza las horas según el plan temporal escogido, 
                        S  => Dar horas facturadas extras. 
                        X  => Auditorio, 
                        CV => Coffee Break
                    */
                    $newfactura = array(
                        'empresa_id'        => $empresa_id,
                        'numero'            => '',
                        'fecha_creacion'    => date("Y-m-d H:i:s"),
                        'monto'             => $result["total"],
                        'monto_fisico'      => $result["total"],
                        'estado'            => "PENDIENTE",
                        'fecha_emision'     => date("Y-m-d"),
                        'fecha_vencimiento' => $params["fecha_vencimiento"],
                        'fecha_limite'      => $params["fecha_limite"],
                        'comprobante'       => $params["tipo"],
                        'accion'            => 'N',
                        'moneda'            => 'S',
                        'usuario'           => null !== \Auth::user() ? \Auth::user()->nombre : "SISTEMA",
                        'su_state'          => 'X',
                        'su_info'           => 'No enviado a la sunat',
                        'detraccion'        => $result["total"] > 700 ? $result["total"]*0.1 : 0
                    );
                    $factura = Factura::create( $newfactura );

                    $this->facturaHistorialAdd( $empresa_id, $factura["id"], 0, "CREADO", $params["tipo"] );

                    $result['factura'] = $factura;
                    $empresa = ( new EmpresaRepo )->getById( $factura["empresa_id"] );

                    $factura["empresa_ruc"]        = $empresa["empresa_ruc"];
                    $factura["empresa_nombre"]     = $empresa["empresa_nombre"];


                    $this->facturaDetalle( $factura, $params );

                    if ( $params['tipo'] === 'FACTURA' ){
                        $documento_serie = 'FF01';
                        $numberValue = 'NUMBER_INVOICE';
                    }else if( $params['tipo'] === 'BOLETA' ){
                        $documento_serie = 'BB01';
                        $numberValue = 'NUMBER_VOUCHER';
                    }else if( $params['tipo'] === 'PROVICIONAL' ){

                        $documento_serie = '';
                        $numberValue = '';
                        $params["sunat"] = "off";
                    }
                    $result["load"] = true;

                    if( isset( $params["sunat"] ) && $params["sunat"] == "on" ){
                        //$this->facturasend( $factura["id"] );
                        $config  = (array)json_decode( ( new ConfiguracionRepo )->GetValor('SUNAT_PARAMS')["valor"] );
                        list( $resultconfig, $numberValue, $documento_serie, $numero, $values, $detalle ) = $this->factura_config( $factura, $config );
                        $values["id"] = $factura["id"];

                        $values["empresa_ruc"]        = $empresa["empresa_ruc"];
                        $values["empresa_nombre"]     = $empresa["empresa_nombre"];
                        $values["fac_email"]          = $empresa["fac_email"];
                        $values["fac_nombre"]         = $empresa["fac_nombre"];
                        $values["fecha_vencimiento"]  = $factura["fecha_vencimiento"];

                        list( $load, $message ) = $this->sendSunatandMail( ($values), $documento_serie, $numero, $detalle, $config, false, $numberValue, $factura );

                        $result["message"] = $message;
                    }else{
                        $result["message"] = "Creado, Pendiente de ser Enviado a Sunat";
                    }
                    // Retornar factura temporal, si fue alterada
                    $result['factura_temporal'] = FacturaTemporal::where('empresa_id', $factura['empresa_id'])->where('estado','PENDIENTE')->get();

                }else{
                    $result = ["load" => false, "message" => "El total debe ser mayor a 0"];
                }
            }
        }else{
            $result["message"] = "Tipo de Comprobante solo puede ser FACTURA o BOLETA y la empresa debe ser valida ";   
        }
        return $result;
    }

    /**
     * Intenta enviar una factura ya creada a la Sunat.
     * @param array $params ID de la factura/boleta.
    **/
    public function facturasend( $factura_id )
    {
        $load    = false;
        $message = "";
        $factura = Factura::where( "id", $factura_id )->first();
        //print_r( $factura );
        if(!empty($factura)){
            $documento_numero = "";
            if( $factura["numero"] != '' ){
                $numero  = explode( "-", $factura["numero"] );
                $documento_numero = $numero[1];
            }
            $empresa = ( new EmpresaRepo )->getById( $factura["empresa_id"] );
            $factura["empresa_ruc"]        = $empresa["empresa_ruc"];
            $factura["empresa_nombre"]     = $empresa["empresa_nombre"];
            $config  = (array)json_decode( ( new ConfiguracionRepo )->GetValor('SUNAT_PARAMS')["valor"] );
            $factura['documento_tdocemisor'] = 'FACTURA';
            list( $result, $numberValue, $documento_serie, $numero, $values, $detalle ) = $this->factura_config( $factura, $config, $documento_numero );

            //if( $documento_numero == '' ){
            //    $numeroFactura = $documento_serie . '-' . $numero;
            //    $this->factura_set_numero( $factura_id, $numeroFactura );
            //}
            
            $values["fecha_emision"]        = $factura["fecha_emision"];
            $values["id"]                 = $factura["id"];
            $values["fecha_vencimiento"]  = $factura["fecha_vencimiento"];
            $values["empresa_ruc"]        = $empresa["empresa_ruc"];
            $values["empresa_nombre"]     = $empresa["empresa_nombre"];
            $values["fac_email"]          = $empresa["fac_email"];
            $values["fac_nombre"]         = $empresa["fac_nombre"];
            
            $ismassive = ( new ConfiguracionRepo )->GetValor('MASIVE_INVOICE')["valor"];
            if( $ismassive == 'N' ){
                ( new ConfiguracionRepo )->toggleFacturaMasiva();
                list( $load, $message ) = $this->sendSunatandMail( ($values), $documento_serie, $numero, $detalle, $config, false, $numberValue, $factura );
                ( new ConfiguracionRepo )->toggleFacturaMasiva(false);
            }else{
                $message = "Un usuario esta intentando enviar a Sunat su Factura ha sido creada pero no enviada ha Sunat intente enviarla nuevamente desde el Tab Facturas";
            }
            /**/
        }else{
            $message = "Documento No encontrado";
        }
        return [ "load" => $load, "message" => $message ];
    }

    /**
     * Crea una nueva nota de credito/debito e intentara enviarla a la Sunat.
     * @param array $params Datos para crear la nota.
    **/
    public function createnota( $empresa_id, $factura_id, $params )
    {
        $factura = Factura::where("id",$factura_id)->where("empresa_id", $empresa_id)->first();
        $load = false;
        $message = "";
        $toinsert = array();
        if(!empty( $factura )){
            $factura["monto"] = $factura["monto"] * 1;
            $params["monto"]  = $params["monto"] * 1;
            if( $factura["comprobante"] == "FACTURA" ){
                if( $factura["su_state"] == "S" ){
                    if( $factura["estado"] != "ANULADA" ){
                        $precio   = strtoupper($params["anular"]) == 'SI' ? $factura["monto"] : $params["monto"];
                        if( ( $params["tipo"] == "CREDITO" && $factura["monto"] > 0 && $factura["monto"] >= $precio ) || $params["tipo"] == "DEBITO" ){

                            if( isset( $params["factura_item"] ) && count($params["factura_item"]) > 0 ){
                                $params["discrepancia"] = 4;
                            }
                            if( !isset( $params["discrepancia"] ) ){
                                $params["discrepancia"] = 4;
                            }

                            $toinsert = array(
                                "factura_id"        => $factura_id,
                                "numero"            => "",
                                "observacion"       => isset( $params["observacion"] ) ? ((strlen($params["observacion"])) < 5 ? "Descuento ". date("Y-m-d") : $params["observacion"]." - " . date("Y-m-d") ) : "Descuento ". date("Y-m-d"),
                                "precio"            => $precio,
                                "empleado"          => \Auth::user()->nombre,
                                "tipo"              => $params["tipo"],
                                "fecha_emision"     => date("Y-m-d"),
                                "fecha_creacion"    => date("Y-m-d H:i:s"),
                                "factura_notascol"  => '',
                                "su_state"          => 'N',
                                "su_info"           => '',
                                "cod_discrepancia"  => ($params["discrepancia"]*1),
                                "mail_send"         => 0
                            );

                            $empresa = ( new EmpresaRepo )->getById( $empresa_id );
                            $config  = (array)json_decode( ( new ConfiguracionRepo )->GetValor('SUNAT_PARAMS')["valor"] );
                            $newnota = FacturaNota::create( $toinsert );
                            $toinsert["id"]               = $newnota->id;
                            $toinsert["docmod"]           = $factura["numero"];
                            $toinsert["empresa_ruc"]      = $empresa["empresa_ruc"];
                            $toinsert["empresa_nombre"]   = $empresa["empresa_nombre"];
                            $toinsert["cod_discrepancia"] = $params["discrepancia"]*1;

                            if( isset( $params["factura_item"] ) && count($params["factura_item"]) > 0 ){
                                foreach( $params["factura_item"] as $factura_item ){
                                    FacturaItem::where( "id", $factura_item )->where( "tipo", "G" )->update(
                                        array( "custom_id" => 1 )
                                    );
                                }
                            }

                            list( $result, $numberValue, $documento_serie, $documento_numero, $values, $detalle ) = $this->nota_config( $toinsert, $config );
                            //$this->factura_set_numero( $result['id'], $documento_serie . '-' . $documento_numero, true );
                            //( new ConfiguracionRepo )->ComprobanteSgteNumero( $numberValue );

                            if( $params["tipo"] == "CREDITO" ){
                                $monto = $factura->monto - $precio;
                                if( $factura["estado"] == "PAGADA" ){
                                    Pago::create(
                                        array(
                                            'tipo'              => "DEVOLUCION",
                                            'fecha_creacion'    => date("Y-m-d H:i:s"),
                                            'deposito_banco'    => "NINGUNO",
                                            'deposito_cuenta'   => "",
                                            'deposito_fecha'    => "",
                                            'detalle'           => "",
                                            'observacion'       => "",
                                            'monto'             => ($precio*-1),
                                            'factura_id'        => $factura_id,
                                            'usuario'           => \Auth::user()->nombre,
                                            'pago_factura_id'   => 0,
                                            'id_pos'            => 0,
                                            'dif_dep_pos'       => 0,
                                            'des_com_pos'       => 0,
                                            'detraccionD'       => 0,
                                            'detraccionE'       => 0
                                        )
                                    );
                                }else if( $factura["estado"] == "PENDIENTE" ){

                                    $totpagado = 0;
                                    $pago = Pago::where( "factura_id", $factura_id )->groupBy('factura_id')->selectRaw('SUM(IFNULL(monto,0)) as total')->first();
                                    if( !empty( $pago ) ){
                                        $totpagado = $pago["total"]*1;
                                    }

                                    if( $totpagado>=($factura["monto"]-$precio) ){
                                        Factura::where( "id", $factura_id )->update( array( "estado" => "PAGADA" ) );
                                    }


                                }
                            }else if( $params["tipo"] == "DEBITO" ){
                                $monto = $factura->monto + $precio;
                                if( $factura["estado"] == "PAGADA" ){
                                    Factura::where( "id", $factura_id )->update( array( "estado" => "PENDIENTE" ) );
                                }
                            }
                            Factura::where( "id", $factura_id )->update( array( "monto" => $monto ) );
                            FacturaItem::create( 
                                array(
                                    'factura_id'                => $factura_id,
                                    'descripcion'               => "NOTA DE ".$params["tipo"]." Nro: ".$documento_serie . '-' . $documento_numero,
                                    'descripcion_sunat'         => "NOTA DE ".$params["tipo"]." Nro: ".$documento_serie . '-' . $documento_numero,
                                    'precio'                    => $params["tipo"] == "CREDITO" ? ($precio*-1) : $precio,
                                    'estado'                    => 'A',
                                    'factura_item_temporal_id'  => '',
                                    'tipo'                      => 'N',
                                    'warranty'                  => 'N',
                                    'is_nota'                   => 'S',
                                )
                            );
                            $load = true;
                            $this->facturaHistorialAdd(
                                $empresa_id, $factura_id, 0, "NUEVO",
                                "SE HIZO NOTA DE ".$params["tipo"]." Nro: ".$documento_serie . '-' . $documento_numero
                            );

                            if( $params["tipo"] == "CREDITO" && $monto <= 0 ){
                                Factura::where( "id", $factura_id )->update( array( "estado" => "ANULADA" ) );
                                $this->facturaHistorialAdd( $empresa_id, $factura_id, 0, "ANULADA", "Anulacion por Nota de Credito" );
                            }

                            $ismassive = ( new ConfiguracionRepo )->GetValor('MASIVE_INVOICE')["valor"];
                            if( $ismassive == 'N' ){
                                ( new ConfiguracionRepo )->toggleFacturaMasiva();

                                $values["empresa_ruc"]        = $empresa["empresa_ruc"];
                                $values["empresa_nombre"]     = $empresa["empresa_nombre"];
                                $values["fac_email"]          = $empresa["fac_email"];
                                $values["fac_nombre"]         = $empresa["fac_nombre"];
                                $values["id"]                 = $toinsert["id"];
                                $values["fecha_vencimiento"]  = "";
                                $message = "";
                                list( $load, $message ) = $this->sendSunatandMail( ($values), $documento_serie, $documento_numero, $detalle, $config, true, $numberValue, $newnota );
                                ( new ConfiguracionRepo )->toggleFacturaMasiva(false);
                                $load = true;
                            }else{
                                $message = "Un usuario esta intentando enviar a Sunat su Nota ha sido creada pero no enviada ha Sunat intente enviarla nuevamente desde el Tab Notas";
                            }
                        }else{
                            if( $params["tipo"] == "CREDITO" ){
                                $message = "TIPO CREDITO Y MONTO ES CERO NO SE PUEDE QUITAR MAS";
                            }else{
                                $message = "TIPO NO ES DEBITO NI CREDITO";
                            }
                        }
                    }else{
                        $message = "FACTURA ANULADA NO PUEDE SUFRIR ALTERACIONES";
                    }
                }else{
                    $message = "FACTURA AUN NO HA SIDO ENVIADA A LA SUNAT";
                }
            }else{
                $message = "COMPROBANTE NO ES FACTURA";    
            }
        }else{
            $message = "COMPROBANTE NO EXISTE";
        }
        return [ "load" => $load, "message" => $message, "factura" => $factura ];
    }

    /**
     * Intenta enviar una nota de en caso haber fallado al momento de ser creada.
     * @param array $params ID de la nota.
    **/
    public function notasend( $factura_id )
    {
        $load    = false;
        $nota    = FacturaNota::where( "id", $factura_id )->first();
        if( !empty($nota) ){
            $factura = Factura::where( "id", $nota["factura_id"] )->first();
            if( !empty($factura) ){
                $numero  = explode( "-", $nota["numero"] );
                $documento_numero = $numero[1];
                $empresa = ( new EmpresaRepo )->getById( $factura["empresa_id"] );
                $message = "";
                $nota["empresa_ruc"]        = $empresa["empresa_ruc"];
                $nota["empresa_nombre"]     = $empresa["empresa_nombre"];
                $nota["docmod"]             = $factura["numero"];
                $config  = (array)json_decode( ( new ConfiguracionRepo )->GetValor('SUNAT_PARAMS')["valor"] );
                list( $result, $numberValue, $documento_serie, $documento_numero, $values, $detalle ) = $this->nota_config( $nota, $config, $documento_numero );
                $values["id"]                 = $nota["id"];
                $values["empresa_ruc"]        = $empresa["empresa_ruc"];
                $values["empresa_nombre"]     = $empresa["empresa_nombre"];
                $values["fac_email"]          = $empresa["fac_email"];
                $values["fac_nombre"]         = $empresa["fac_nombre"];
                $values["fecha_vencimiento"]  = "";
                $ismassive = ( new ConfiguracionRepo )->GetValor('MASIVE_INVOICE')["valor"];
                if( $ismassive == 'N' ){
                    ( new ConfiguracionRepo )->toggleFacturaMasiva();
                    list( $load, $message ) = $this->sendSunatandMail( ($values), $documento_serie, $documento_numero, $detalle, $config, true, $numberValue, $nota );
                    ( new ConfiguracionRepo )->toggleFacturaMasiva(false);
                }else{
                    $message = "Un usuario esta intentando enviar a Sunat su Nota ha sido creada pero no enviada ha Sunat intente enviarla nuevamente desde el Tab Notas";
                }
            }else{
                $message = "Comprobante anexado a la nota no encontrado.";    
            }
        }else{
            $message = "Nota no encontrada en el sistema.";
        }


        return [ "load" => $load, "message" => $message ];
    }


    /**
     * Manda los Comprobantes a la Sunat al igual que el correo de ser afirmativo la respuesta de la sunat.
     * @param valores configurados.
    **/
    private function sendSunatandMail( $values, $documento_serie, $documento_numero, $detalle, $config, $nota = false, $numberValue = '', $documento = array() )
    {
        $return = array(false,"");
        $values["modo"] = config('app.env') == "production" ? "produccion" : "test";///COMENTAME DESPUES DE LAS PRUEBAS
        try{
            if( ( isset( $documento["numero"] ) && $documento["numero"] == '' ) || !isset( $documento["numero"] ) ){
                //echo PHP_EOL."+++++++++++++++++++++++++++++++++++++++++++++++++".PHP_EOL;
                //echo "<pre>"; print_r( $documento ); echo "</pre>"; echo PHP_EOL;
                //echo PHP_EOL."+++++++++++++++++++++++++++++++++++++++++++++++++".PHP_EOL;
                //echo PHP_EOL."*************************************************".PHP_EOL;
                //echo "<pre>"; print_r( $values ); echo "</pre>"; echo PHP_EOL;
                //echo PHP_EOL."*************************************************".PHP_EOL;
                $this->factura_set_numero( $values["id"], $documento_serie . '-' . $documento_numero, $nota );
                ( new ConfiguracionRepo )->ComprobanteSgteNumero( $numberValue );
            }
            $rpta = $this->sendToSunat($values);
                #echo "<pre>"; print_r( $values ); echo "</pre>";
            if ( is_array($rpta)) {
                if( isset( $rpta['data'] ) && strpos( $rpta['data'], "|" ) !== false ){
                    //echo "<pre>.................................................</pre>";
                    //echo "<pre>"; print_r( $rpta ); echo "</pre>";
                    //echo "<pre>"; print_r( strpos( $rpta['data'], "|" ) ); echo "</pre>";
                    //echo "<pre>.................................................</pre>";
                    $estadoSunat = explode('|', $rpta['data']);
                    $value = ['state' => 'S', 'info' => $estadoSunat[1] ];
                    $value["factura_id"] = $values["id"];
                    $this->factura_update_sunat($value, $nota);//DESCOMENTAME PARA ESCENARIO REAL
                    if (!$rpta['load']) {
                        $return = array(false,"Documento creado; pero se detecto problemas en Sunat por favor re intentar en unos momentos");
                        $value['state'] = 'X';
                        $value['info'] = $rpta['message'];
                        $value["factura_id"] = $values["id"];
                    } else {
                        $values["serie"]  = $documento_serie;
                        $values["numero"] = $documento_numero;

                        $this->sendCompMensaje( $values, $config, $detalle, $nota );//--------------

                        /*
                        list( $createPDFfile, $pdf ) = $this->factura_pdf_prepare( $values, $detalle, $config  );

                        if( $createPDFfile ){
                            $pdf->setPaper('a4', 'portrait')->setWarnings(false)->save( public_path() .'/pdf/'.$values["serie"].'-'.$values["numero"].'.pdf');
                            $this->sendCompMensaje( $values, $config, $nota );    
                        }
                        */

                        $return = array( true, $estadoSunat[1] );
                    }
                    //echo "<pre>_________________________________________________</pre>";
                    //echo "<pre>"; print_r( $value ); echo "</pre>"; echo PHP_EOL;
                    //echo "<pre>_________________________________________________</pre>";
                }else{
                    $return = array(false,"Documento no se pudo enviar a la sunat intentelo de nuevo en unos momentos" );
                }                
            } else {
                $return = array(false,"Documento creado; pero se detecto problemas en Sunat por favor re intentar en unos momentos");
            }
            /**/
        }catch(\Exception $e){
            $return = array( false, $e->getMessage()." - ".$e->getLine()." - ".$e->getFile() );
            //echo "<pre>^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^/pre>";
            //echo "<pre>"; print_r( $return ); echo "</pre>"; echo PHP_EOL;
            //echo "<pre>^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^</pre>";
        }
        return $return;
    }


    /**
     * Setea los numeros de los comprobantes al intentar enviar a la Sunat.
     * @param valores configurados.
    **/
    private function factura_set_numero( $id, $numero, $nota = false )
    {
        if ($nota) {
            return FacturaNota::where("id",$id)->update(
                array(
                    "numero"        => $numero,
                    "fecha_emision" => date("Y-m-d")
                )
            );
        } else {
            $doc = Factura::where("id", $id)->first();
            $emp = Empresa::where("id", $doc["empresa_id"] )->first();
            if( strlen($emp["empresa_ruc"]) != 11 ){
                throw new \Exception("El Nro Doc. de la Empresa no es RUC");
            }
            return Factura::where("id",$id)->update(
                array(
                    "numero"        => $numero,
                    "fecha_emision" => date("Y-m-d")
                )
            );

        }
    }


    public function facturaUpdate( $empresa_id, $factura_id, $params )
    {
        $result = array( "load" => false, "message" => "" );
        if( $empresa_id > 0 && ( $params["tipo"] == "FACTURA" || $params["tipo"] == "BOLETA" ) ){
            $factura = Factura::where( "id", $factura_id )->first();
            if( !empty( $factura ) ){
                if( $factura["su_state"] != "S" && $factura["estado"] == "PENDIENTE" ){
                    $result = $this->validateDetalle( $empresa_id, $params["items"], true );
                    if( $result["load"] ){
                        $oldfactura = array(
                            'monto'             => $result["total"],
                            'monto_fisico'      => $result["total"],
                            'fecha_emision'     => date("Y-m-d"),
                            'fecha_vencimiento' => $params["fecha_vencimiento"],
                            'fecha_limite'      => $params["fecha_limite"],
                            'comprobante'       => $params["tipo"],
                            'accion'            => 'N',
                            'moneda'            => 'S',
                            'usuario'           => \Auth::user()->nombre,
                            'detraccion'        => $result["total"] > 700 ? $result["total"]*0.1 : 0
                        );

                        $load = Factura::where( "id", $factura_id )->update( $oldfactura );
                        ReservaDetalle::where( "factura_id", $factura_id )->delete();
                        FacturaItem::where( "factura_id", $factura_id )->delete();
                        $this->facturaDetalle( $factura, $params );

                        $this->facturaHistorialAdd( $empresa_id, $factura_id, 0, "ACTUALIZADO", $params["tipo"]  );

                        $result["load"] = $load;
                        $result['factura_temporal'] = FacturaTemporal::where('empresa_id', $factura['empresa_id'])->where('estado','PENDIENTE')->get();
                    }
                }else{
                    $result["message"] = "El comprobante solo puede ser alterado mientras no este declaradoa Sunat, y se encuentre en estado PENDIENTE";
                }
            }else{
                $result["message"] = "El comprobante de pago ya no existe";
            }
        }
        return $result;
    }

    private function facturaDetalle( $factura, $params )
    {
        foreach( $params["items"] as $d ){
            $detallecreate = array(
                'factura_id'            => $factura["id"],
                'descripcion'           => isset( $d["descripcion"] ) ? $d["descripcion"] : "Extras" ,
                'descripcion_sunat'     => isset( $d["descripcion_sunat"] ) ? $d["descripcion_sunat"] : "Extras" ,
                'precio'                => $d["precio"],
                'estado'                => 'A',
                'anio'                  => $d["anio"],
                'mes'                   => $d["mes"],
                'tipo'                  => $d["tipo"],
                'custom_id'             => isset( $d["custom_id"] ) ? $d["custom_id"] : 0
            );
            if( $d["tipo"] == "CB" ){
                if( isset($d["reserva_id"]) && isset($d["preciou"]) && isset($d["cantidad"]) ){
                    if( $d["reserva_id"] > 0 && $d["preciou"] > 0 && $d["cantidad"] > 0   ){
                        ReservaDetalle::create( 
                            array(
                                "reserva_id"      => $d["reserva_id"],
                                "concepto_id"     => $d["custom_id"],
                                "precio"          => $d["preciou"],
                                "cantidad"        => $d["cantidad"],
                                "created_at"      => date("Y-m-d H:i:s"),
                                "factura_id"      => $factura["id"]
                            ) 
                        );
                    }    
                }
            }else if( $d["tipo"] == "T" ){
                FacturaTemporal::where( "id", $d["custom_id"] )->update( array( "estado" => "FACTURADO" ) );
            }

            FacturaItem::create( $detallecreate );
        }
    }

    private function validateDetalle( $empresa_id, $detalle, $update = false )
    {
        $result = ["load" => true, "message" => "", "total" => 0 ];
        $error  = 0;
        /*
            N  => nota,             G  => garantia, 
            P  => PLAN,             C  => Horas Combo
            HO => Horas Oficina,    HR => Horas Reunion, 
            D  => Descuento,        A  => Auditorio / S. Capacitacion _ estado pendiente
            R  => Reserva _ estado pendiente para usuarios plan Externo
        */
        foreach( $detalle as $d ){
            $result["total"] = $result["total"] + $d["precio"];
            if( $error <= 0 ){
                if( $d["tipo"] == "G" && !$update ){
                    $fires = ( new QueryRepo )->Q_facturadetallegarantia_validate([ "anio" => $d["anio"], "mes" => $d["mes"], "mes" => $d["mes"], "empresa_id" => $empresa_id ]);
                    $fi = $fires["rows"];
                    if( !empty( $fi ) || sizeof( $fi ) > 0 ){
                        $result["load"] = false;
                        $result["message"] = "Ya existe una Garantia que esta sin usar";
                        $error  = 1;
                    }
                }

                if( $d["tipo"] == "P" && !$update ){
                    $fires = ( new QueryRepo )->Q_facturadetalle_validate([ "anio" => $d["anio"], "mes" => $d["mes"], "mes" => $d["mes"], "empresa_id" => $empresa_id ]);

                    $fi = $fires["rows"];
                    if( !empty( $fi ) || sizeof( $fi ) > 0 ){
                        $result["load"] = false;
                        $result["message"] = "Ya existe un comprobante con deuda tipo Plan no puede haber dos en un mismo periodo";
                        $error  = 1;
                    }
                }

                if( $d["tipo"] == "P" || $d["tipo"] == "HO" || $d["tipo"] == "HR" || $d["tipo"] == "C" ){
                    if( $d["anio"] <= 0 || $d["mes"] <= 0 ){
                        $result["load"] = false;
                        $result["message"] = "Periodo No Indicado Correctamente";
                        $error  = 1;
                    }
                }

                if( $d["tipo"] == "C" || $d["tipo"] == "R" || $d["tipo"] == "A" ){//$d["tipo"] == "P" || 
                    if( $d["custom_id"] <= 0 ){
                        $result["load"] = false;
                        $result["message"] = "Se debe indicar el ID del plan para Plan y Paquete de Horas";
                        $error  = 1;
                    }
                }

                if( $d["tipo"] == "D" ){
                    if( $d["precio"] >= 0 ){
                        $result["load"] = false;
                        $result["message"] = "El Monto del descuento debe ser menor a 0";
                        $error  = 1;
                    }
                }

                if( $d["tipo"] == "HO" || $d["tipo"] == "HR" ){
                    if( $d["precio"] <= 0 ){
                        $result["load"] = false;
                        $result["message"] = "El Precio de las Horas Privada o Reunion debe ser superior a 0";
                        $error  = 1;
                    }
                }
            }
        }

        if( $result["total"] < 0 ){
            $result["load"] = false;
            $result["message"] = "El Total del comprobante no puede ser menor a 0";
            $error  = 1;
        }

        return $result;
    }

    public function factura_anula( $empresa_id, $factura_id )
    {
        $result = array( "load" => false, "message" => "" );
        $factura = Factura::where( "id", $factura_id )->where( "empresa_id", $empresa_id )->first();
        if( !empty( $factura ) ){
            if( $factura["su_state"] != 'S' ){
                $this->facturaHistorialAdd( $empresa_id, $factura_id, 0, "SE ANULO", "-"  );
                $result["load"] = Factura::where( "id", $factura_id )->where( "empresa_id", $empresa_id )->update(
                    array(
                        "estado" => "ANULADA"
                    )
                );
            }else{
                $result["message"] = "El Comprobante ya emitio en Sunat solo se puede anular con Nota de Credito";    
            }
        }else{
            $result["message"] = "El Comprobante ya no existe refresque por favor";
        }
        return $result;
    }

    //ENVIAR EMAIL CCO DE COMPROBANTE FACTURA/NOTA DE CREDITO/NOTA DE DEBITO
    private function sendCompMensaje( $comp, $config, $detalle, $nota = false )
    {
        list( $createPDFfile, $pdf ) = $this->factura_pdf_prepare( $comp, $detalle, $config  );
        
        if( $createPDFfile ){
            $pdf->setPaper('a4', 'portrait')->setWarnings(false); 
        }

        if(!filter_var($comp["fac_email"], FILTER_VALIDATE_EMAIL)){
            return false;
        }

        if( $comp["documento_tdocemisor"] == '01' ){
            $comp["documento_tdocemisor"] = 'FACTURA';
        }else if( $comp["documento_tdocemisor"] == '07' ){
            $comp["documento_tdocemisor"] = 'NOTA DE CREDITO';
        }else if( $comp["documento_tdocemisor"] == '08' ){
            $comp["documento_tdocemisor"] = 'NOTA DE DEBITO';
        }

        $params = [
            'fullname' => $comp['fac_nombre'],
            'attach' => [
                $pdf->stream(),
                $comp["serie"] . "-" . $comp["numero"] . ".pdf", 
                ['mime'=>'application/pdf']
            ],
            'vencido' => -1,
            'documento' => $comp["documento_tdocemisor"] . ' ' . $comp["serie"] . "-" . $comp["numero"]
        ];

        if($nota){
            $params['msg'] = 'Adjuntamos la <b>' . $comp["documento_tdocemisor"] . ' ' . $comp["serie"]. '-' .$comp["numero"] . '</b> emitida para modificar el documento '. $comp["documento_billing"]["id"];
            $params['nota'] = 1;
        } else {
            $params['msg'] = 'Adjuntamos la <b>' . $comp["documento_tdocemisor"] . '</b> detallando los consumos correspondientes de este mes.';
            $params['monto'] = number_format($comp['totalventa'], 2);//totalventa
            $params['vencimiento'] = $comp["fecha_vencimiento"];
            $params['detalle'] = \DB::table('factura_item')->where('factura_id', $comp['id'])->get(['descripcion','precio']);
        }



        if(isset($comp["cc"]) && !empty($comp['cc'])){
            $params["cc"] = $comp["cc"];
        }

        if(env('APP_ENV') == 'local'){
            \Mail::to(env('MAIL_TESTER'))->send(new \CVAdmin\Mail\Factura("COMPROBANTE", $params));
        } else {
            \Mail::to($comp["fac_email"])->bcc('eramirez@centrosvirtuales.com')->send(new \CVAdmin\Mail\Factura("COMPROBANTE", $params));
        }

        /*
        $remitente = array(
            "de"        => "no-reply@centrosvirtuales.com",
            "asunto"    => "COMPROBANTE"
        );
        $destinatario = array(
            "correo"    =>"gdelportal@sitatel.com",//$comp["fac_email"],
            "nombre"    => (!isset($comp["fac_nombre"]) || empty($comp["fac_nombre"]))?$comp["fac_email"]:$comp["fac_nombre"]
        );

        $file = public_path() .'/pdf/' .$comp["serie"]."-".$comp["numero"].".pdf" ;
        $filename = $comp["serie"]."-".$comp["numero"].".pdf";
        $newmensaje = array(
            "mensaje" => 
                "<p>Estimado cliente, <b>".$comp["fac_nombre"]."</b>.</p>".
                "<p>Adjunto la <b>".$comp["documento_tdocemisor"]." ".$comp["serie"]."-".$comp["numero"]."</b>".
                (($nota)?(" emitida para modificar el documento ".$comp["documento_billing"]["id"]):(" emitida por los servicios de <b>".$config["emisor_nombre_comercial"]."</b> que vence el dia <b>".$comp["fecha_vencimiento"]."</b>.</p>")).

                "<p>Recuerde que su codigo de cliente es el RUC de su empresa.</p>".
                "<p>Saludos.</p>".
                "<p><a href='http://service.centrosvirtuales.com/comprobante/pdfdownload/".$comp["empresa_ruc"]."/".$comp["documento_tdocemisor"]."/".$comp["serie"]."/".$comp["numero"]."'>DESCARGAR</a></p><p></p>".
                "<p><a href='http://service.centrosvirtuales.com/comprobante/pdf/".$comp["empresa_ruc"]."/".$comp["documento_tdocemisor"]."/".$comp["serie"]."/".$comp["numero"]."'>VER</a></p>"
        );
        $extras= array(
            "addbcc" => array(
                'ga.dp.ch@gmail.com'//,
                //'eramirez@centrosvirtuales.com'//CORREO CON COPIA OCULTA
            )
        );
        return ( new SessionRepo )->send( $remitente, $destinatario, $newmensaje, $extras, false );*/
    }

    private function facturaHistorialAdd( $empresa_id, $factura_id, $item_id, $tipo, $observacion )
    {
        FacturaHistorial::create(
            array(
                "empresa_id"    => $empresa_id,
                "factura_id"    => $factura_id,
                "item_id"       => $item_id,
                "tipo"          => $tipo,
                "observacion"   => $observacion,
                'usuario'       => null !== \Auth::user() ? \Auth::user()->nombre : "SISTEMA",
                "fecha"         => date("Y-m-d H:i:s"),
            )
        );
        return true;
    }

    public function emailfacturasend( $factura_id, $cc = null )
    {
        $load    = false;
        $message = "";
        $factura = Factura::where( "id", $factura_id )->first();
        $numero  = explode( "-", $factura["numero"] );
        $documento_numero = $numero[1];
        $empresa = ( new EmpresaRepo )->getById( $factura["empresa_id"] );
        $factura["empresa_ruc"]        = $empresa["empresa_ruc"];
        $factura["empresa_nombre"]     = $empresa["empresa_nombre"];
        $config  = (array)json_decode( ( new ConfiguracionRepo )->GetValor('SUNAT_PARAMS')["valor"] );
        list( $result, $numberValue, $documento_serie, $documento_numero, $values, $detalle ) = $this->factura_config( $factura, $config, $documento_numero );
        $values["id"]                 = $factura["id"];
        $values["fecha_vencimiento"]  = $factura["fecha_vencimiento"];
        $values["fecha_emision"]        = $factura["fecha_emision"];
        $values["empresa_ruc"]        = $empresa["empresa_ruc"];
        $values["empresa_nombre"]     = $empresa["empresa_nombre"];
        $values["fac_email"]          = $empresa["fac_email"];
        $values["fac_nombre"]         = $empresa["fac_nombre"];
        $values["serie"]              = $values["documento_serie"];
        $values["numero"]             = $values["documento_numero"];

        if(!is_null($cc)){
            $values["cc"]                 = $cc;
        }


        $load = $this->sendCompMensaje( $values, $config, $detalle, false );
        if( $load ){
            Factura::where( "id", $factura_id )->update(
                array(
                    "mail_send" => $factura["mail_send"]+1
                )
            );
        }
        return [ "load" => $load, "message" => $message ];
    }

    public function emailnotasend( $factura_id, $cc = null )
    {
        $nota    = FacturaNota::where( "id", $factura_id )->first();
        $factura = Factura::where( "id", $nota["factura_id"] )->first();
        $numero  = explode( "-", $nota["numero"] );
        $documento_numero = $numero[1];
        $empresa = ( new EmpresaRepo )->getById( $factura["empresa_id"] );
        $load    = false;
        $message = "";
        $nota["empresa_ruc"]        = $empresa["empresa_ruc"];
        $nota["empresa_nombre"]     = $empresa["empresa_nombre"];
        $nota["docmod"]             = $factura["numero"];
        $config  = (array)json_decode( ( new ConfiguracionRepo )->GetValor('SUNAT_PARAMS')["valor"] );
        list( $result, $numberValue, $documento_serie, $documento_numero, $values, $detalle ) = $this->nota_config( $nota, $config, $documento_numero );
        $values["id"]                 = $nota["id"];
        $values["empresa_ruc"]        = $empresa["empresa_ruc"];
        $values["empresa_nombre"]     = $empresa["empresa_nombre"];
        $values["fac_email"]          = $empresa["fac_email"];
        $values["fac_nombre"]         = $empresa["fac_nombre"];
        $values["fecha_vencimiento"]  = "";
        $values["serie"]              = $values["documento_serie"];
        $values["numero"]             = $values["documento_numero"];

        if(!is_null($cc)){
            $values['cc'] = $cc;
        }

        $load = $this->sendCompMensaje( $values, $config, $detalle, true );

        return [ "load" => $load, "message" => $message ];
    }

    public function sunatDetalle($detalle){

        $data = [];
        $detalleValues = [];
        $totalventa = 0;
        $totalMontoPagar = 0;
        //Importe
        $totalWarrantyVenta = 0;
        $totalWarrantyMontoPagar = 0;
        $descWarranty = '';

        $garantia  = 0;
        $extra     = 0;
        $audiextra = 0;
        $gadminist = 0;
        $devolucio = 0;
        $descuento = 0;
        $plan      = array();
        $auditorio = array();
        $notas     = array();

        foreach ($detalle as $key) {
            if(!isset($key["tipo"])){
                $key["tipo"] = "-";
            }
            if( $key["tipo"] == "P" ){
                $temp = [
                    "descripcion"         => $key["descripcion"],
                    "descripcion_sunat"   => $key["descripcion_sunat"],
                    "precioconimpuesto"   => $key["precioconimpuesto"],
                    "preciosinimpuesto"   => $key["preciosinimpuesto"],
                ];
                array_push( $plan, $temp );
            }else if( $key["tipo"] == "A" ){
                $temp = [
                    "descripcion"         => $key["descripcion"],
                    "descripcion_sunat"   => $key["descripcion_sunat"],
                    "precioconimpuesto"   => $key["precioconimpuesto"],
                    "preciosinimpuesto"   => $key["preciosinimpuesto"],
                ];
                array_push( $auditorio, $temp );
            }else if( $key["tipo"] == "N" ){
                $temp = [
                    "descripcion"         => $key["descripcion"],
                    "descripcion_sunat"   => $key["descripcion_sunat"],
                    "precioconimpuesto"   => $key["precioconimpuesto"],
                    "preciosinimpuesto"   => $key["preciosinimpuesto"],
                ];
                array_push( $notas, $temp );
            }else if( $key["tipo"] == "G" ){
                $garantia = $garantia + $key["precio"];
            }else if( $key["tipo"] == "Z" ){
                $gadminist = $gadminist + $key["precio"];
            }else if( $key["tipo"] == "W" ){
                $devolucio = $devolucio + $key["precio"];
            }else if( $key["tipo"] == "CB" ){
                $audiextra = $audiextra + $key["precio"];
            }else if( $key["tipo"] == "D" ){
                $descuento = $descuento + $key["precio"];
            }else{ 
                /*
                        T  => F TEMPOral,    E  => Extras, 
                        HO => H. Oficina,    HR => H. Reunion, 
                        C  => Horas Combo,   R  => RESERVA, 
                        -  => DATA ANTIGUA                    
                */
                $extra = $extra + $key["precio"];
            }
            $totalMontoPagar = $totalMontoPagar + $key["precioconimpuesto"];
            /*
            if ($key['warranty'] == 'S') {
                $totalWarrantyMontoPagar = $key['precioconimpuesto'];
                $descWarranty = $key['descripcion_sunat'];
            } else {
                $totalMontoPagar += $key['precioconimpuesto'];
            }
            */
        }

        /*
            echo "<pre>";   print_r("garantia");    echo "<br />"; print_r($garantia);     echo "</pre>";
            echo "<pre>";   print_r("extra");       echo "<br />"; print_r($extra);        echo "</pre>";
            echo "<pre>";   print_r("audiextra");   echo "<br />"; print_r($audiextra);    echo "</pre>";
            echo "<pre>";   print_r("gadminist");   echo "<br />"; print_r($gadminist);    echo "</pre>";
            echo "<pre>";   print_r("devolucio");   echo "<br />"; print_r($devolucio);    echo "</pre>";
            echo "<pre>";   print_r("descuento");   echo "<br />"; print_r($descuento);    echo "</pre>";
            echo "<pre>";   print_r("plan");        echo "<br />"; print_r($plan);         echo "</pre>";
            echo "<pre>";   print_r("auditorio");   echo "<br />"; print_r($auditorio);    echo "</pre>";
            echo "<pre>";   print_r("notas");       echo "<br />"; print_r($notas);        echo "</pre>";
            echo "<br />-------------------------------------<br />";
        */
        if( $descuento < 0 ){
            if( $extra > 0 && $descuento < 0 ){
                if( $extra > abs( $descuento ) ){
                    $extra = $extra + $descuento;
                    $descuento = 0;
                }else{
                    $descuento = $descuento + $extra;
                    $extra = 0;
                }
            }
            if( $audiextra > 0 && $descuento < 0 ){
                if( $audiextra > abs( $descuento ) ){
                    $audiextra = $audiextra + $descuento;
                    $descuento = 0;
                }else{
                    $descuento = $descuento + $audiextra;
                    $audiextra = 0;
                }
            }
            if( count($auditorio) > 0 && $descuento < 0 ){
                foreach( $auditorio as $k => $v ){
                    if( $v["precioconimpuesto"] > 0 && $descuento < 0 ){
                        if( $v["precioconimpuesto"] > abs( $descuento ) ){
                            $auditorio[$k]["precioconimpuesto"] = $auditorio[$k]["precioconimpuesto"] + $descuento;
                            $auditorio[$k]["preciosinimpuesto"] = round( ( $auditorio[$k]["precioconimpuesto"] / 1.18 ), 2 );
                            $descuento = 0;
                        }else{
                            $descuento = $descuento + $v["precioconimpuesto"];
                            unset($auditorio[$k]);
                        }
                    }
                }
            }
            if( count($plan) > 0 && $descuento < 0 ){
                foreach( $plan as $k => $v ){
                    if( $v["precioconimpuesto"] > 0 && $descuento < 0 ){
                        if( $v["precioconimpuesto"] > abs( $descuento ) ){
                            $plan[$k]["precioconimpuesto"] = $plan[$k]["precioconimpuesto"] + $descuento;
                            $plan[$k]["preciosinimpuesto"] = round( ( $plan[$k]["precioconimpuesto"] / 1.18 ), 2 );
                            $descuento = 0;
                        }else{
                            $descuento = $descuento + $v["precioconimpuesto"];
                            unset($plan[$k]);
                        }
                    }
                }
            }
        }
        /*
            echo "<pre>";   print_r("garantia");    echo "<br />"; print_r($garantia);     echo "</pre>";
            echo "<pre>";   print_r("extra");       echo "<br />"; print_r($extra);        echo "</pre>";
            echo "<pre>";   print_r("audiextra");   echo "<br />"; print_r($audiextra);    echo "</pre>";
            echo "<pre>";   print_r("gadminist");   echo "<br />"; print_r($gadminist);    echo "</pre>";
            echo "<pre>";   print_r("devolucio");   echo "<br />"; print_r($devolucio);    echo "</pre>";
            echo "<pre>";   print_r("descuento");   echo "<br />"; print_r($descuento);    echo "</pre>";
            echo "<pre>";   print_r("plan");        echo "<br />"; print_r($plan);         echo "</pre>";
            echo "<pre>";   print_r("auditorio");   echo "<br />"; print_r($auditorio);    echo "</pre>";
            echo "<pre>";   print_r("notas");       echo "<br />"; print_r($notas);        echo "</pre>";
        */

        if( count($plan) > 0 ){
            foreach( $plan as $k => $v ){
                $temp = array(
                    "cantidad"              => 1,
                    "medida"                => 'ZZ',
                    "precioconimpuesto"     => $v["precioconimpuesto"],
                    "preciosinimpuesto"     => $v["preciosinimpuesto"],
                    "preciototal"           => $v["precioconimpuesto"],
                    "precioventatipo"       => '01',
                    "descripcion"           => isset($v["descripcion_sunat"]) ? $v["descripcion_sunat"] : $v["descripcion"],
                    "descripcion_sunat"     => isset($v["descripcion_sunat"]) ? $v["descripcion_sunat"] : $v["descripcion"],
                    "customcode"            => 1,
                    "tipo_igv"              => '10',
                    "igv"                   => ( $v["precioconimpuesto"] - $v["preciosinimpuesto"] )
                );
                array_push( $detalleValues, $temp );
            }
        }
        if( count($auditorio) > 0 ){
            foreach( $auditorio as $k => $v ){
                $temp = array(
                    "cantidad"              => 1,
                    "medida"                => 'ZZ',
                    "precioconimpuesto"     => $v["precioconimpuesto"],
                    "preciosinimpuesto"     => $v["preciosinimpuesto"],
                    "preciototal"           => $v["precioconimpuesto"],
                    "precioventatipo"       => '01',
                    "descripcion"           => isset($v["descripcion_sunat"]) ? $v["descripcion_sunat"] : $v["descripcion"],
                    "descripcion_sunat"     => isset($v["descripcion_sunat"]) ? $v["descripcion_sunat"] : $v["descripcion"],
                    "customcode"            => 1,
                    "tipo_igv"              => '10',
                    "igv"                   => ( $v["precioconimpuesto"] - $v["preciosinimpuesto"] )
                );
                array_push( $detalleValues, $temp );
            }
        }
        if( count($notas) > 0 ){
            foreach( $notas as $k => $v ){
                $temp = array(
                    "cantidad"              => 1,
                    "medida"                => 'ZZ',
                    "precioconimpuesto"     => $v["precioconimpuesto"],
                    "preciosinimpuesto"     => $v["preciosinimpuesto"],
                    "preciototal"           => $v["precioconimpuesto"],
                    "precioventatipo"       => '01',
                    "descripcion"           => isset($v["descripcion_sunat"]) ? $v["descripcion_sunat"] : $v["descripcion"],
                    "descripcion_sunat"     => isset($v["descripcion_sunat"]) ? $v["descripcion_sunat"] : $v["descripcion"],
                    "customcode"            => 1,
                    "tipo_igv"              => '10',
                    "igv"                   => ( $v["precioconimpuesto"] - $v["preciosinimpuesto"] )
                );
                array_push( $detalleValues, $temp );
            }
        }
        if( $extra > 0 ){
            $temp = array(
                "cantidad"              => 1,
                "medida"                => 'ZZ',
                "precioconimpuesto"     => $extra,
                "preciosinimpuesto"     => round(($extra/1.18),2),
                "preciototal"           => $extra,
                "precioventatipo"       => '01',
                "descripcion"           => "SERVICIO DE OFICINA VIRTUAL - EXTRAS",
                "descripcion_sunat"     => "SERVICIO DE OFICINA VIRTUAL - EXTRAS",
                "customcode"            => 1,
                "tipo_igv"              => '10',
                "igv"                   => ( $extra - (round(($extra/1.18),2)) )
            );
            array_push( $detalleValues, $temp );
        }
        if( $audiextra > 0 ){
            $temp = array(
                "cantidad"              => 1,
                "medida"                => 'ZZ',
                "precioconimpuesto"     => $audiextra,
                "preciosinimpuesto"     => round(($audiextra/1.18),2),
                "preciototal"           => $audiextra,
                "precioventatipo"       => '01',
                "descripcion"           => "SERVICIO DE OFICINA VIRTUAL - AUDITORIO EXTRAS",
                "descripcion_sunat"     => "SERVICIO DE OFICINA VIRTUAL - AUDITORIO EXTRAS",
                "customcode"            => 1,
                "tipo_igv"              => '10',
                "igv"                   => ( $audiextra - (round(($audiextra/1.18),2)) )
            );
            array_push( $detalleValues, $temp );
        }
        if( $garantia > 0 ){
            $temp = array(
                "cantidad"              => 1,
                "medida"                => 'ZZ',
                "precioconimpuesto"     => $garantia,
                "preciosinimpuesto"     => round(($garantia/1.18),2),
                "preciototal"           => $garantia,
                "precioventatipo"       => '01',
                "descripcion"           => "GARANTIA",
                "descripcion_sunat"     => "GARANTIA",
                "customcode"            => 1,
                "tipo_igv"              => '10',
                "igv"                   => ( $garantia - (round(($garantia/1.18),2)) )
            );
            array_push( $detalleValues, $temp );
        }
        if( abs($gadminist) > 0 ){
            $temp = array(
                "cantidad"              => 1,
                "medida"                => 'ZZ',
                "precioconimpuesto"     => $gadminist,
                "preciosinimpuesto"     => round(($gadminist/1.18),2),
                "preciototal"           => $gadminist,
                "precioventatipo"       => '01',
                "descripcion"           => isset($v["descripcion_sunat"]) ? $v["descripcion_sunat"] : $v["descripcion"],
                "descripcion_sunat"     => isset($v["descripcion_sunat"]) ? $v["descripcion_sunat"] : $v["descripcion"],
                "customcode"            => 1,
                "tipo_igv"              => '10',
                "igv"                   => ( $gadminist - (round(($gadminist/1.18),2)) )
            );
            array_push( $detalleValues, $temp );
        }
        if( abs($devolucio) > 0 ){
            $temp = array(
                "cantidad"              => 1,
                "medida"                => 'ZZ',
                "precioconimpuesto"     => $devolucio,
                "preciosinimpuesto"     => round(($devolucio/1.18),2),
                "preciototal"           => $devolucio,
                "precioventatipo"       => '01',
                "descripcion"           => isset($v["descripcion_sunat"]) ? $v["descripcion_sunat"] : $v["descripcion"],
                "descripcion_sunat"     => isset($v["descripcion_sunat"]) ? $v["descripcion_sunat"] : $v["descripcion"],
                "customcode"            => 1,
                "tipo_igv"              => '10',
                "igv"                   => ( $devolucio - (round(($devolucio/1.18),2)) )
            );
            array_push( $detalleValues, $temp );
        }
        return array( $totalMontoPagar, $detalleValues );
    }

    private function factura_config( $result, $config, $documento_numero = '' )
    {
        $detalle = $this->factura_item( $result["id"] );
        $detalleValues = [];
        $totalventa = 0;
        $totalMontoPagar = 0;
        list( $totalMontoPagar, $detalleValues ) = $this->sunatDetalle($detalle);

        $totalventa = round($totalMontoPagar / 1.18, 2);
        $importeIgv = $totalMontoPagar - $totalventa;


        /*
            $detalleValues[0]['cantidad'] = 1;
            $detalleValues[0]['medida'] = 'ZZ';
            $detalleValues[0]['precioconimpuesto'] = $totalMontoPagar;
            $detalleValues[0]['preciosinimpuesto'] = $totalventa;
            $detalleValues[0]['preciototal'] = $totalMontoPagar;
            $detalleValues[0]['precioventatipo'] = '01';
            $detalleValues[0]['descripcion'] = isset($detalle[0]['descripcion_sunat']) ? $detalle[0]['descripcion_sunat'] : 'SERVICIO EN OFICINAS VIRTUALES';
            $detalleValues[0]['customcode'] = 1;
            $detalleValues[0]['tipo_igv'] = '10';
            $detalleValues[0]['igv'] = $importeIgv;

            if ($totalWarrantyMontoPagar > 0) {
                $totalWarrantyVenta = round($totalWarrantyMontoPagar / 1.18, 2);
                $importeIgv = $totalWarrantyMontoPagar - $totalWarrantyVenta;

                $itemWarranty = [
                    'cantidad' => 1,
                    'medida' => 'ZZ',
                    'preciosinimpuesto' => $totalWarrantyVenta,
                    'precioconimpuesto' => $totalWarrantyMontoPagar,
                    'preciototal' => $totalWarrantyMontoPagar,
                    'precioventatipo' => '01',
                    'descripcion' => $descWarranty != '' ? $descWarranty : 'SERVICIO EN OFICINAS VIRTUALES - GARANTIA',
                    'customcode' => 1,
                    'tipo_igv' => '10',
                    'igv' => $importeIgv,
                ];
                $detalleValues[1] = $itemWarranty;

                $totalMontoPagar = $totalMontoPagar + $totalWarrantyMontoPagar;
                $totalventa = round($totalMontoPagar / 1.18, 2);
                $importeIgv = $totalMontoPagar - $totalventa;
            }
        */

        $documento_serie = 'FF01';
        $numberValue = 'NUMBER_INVOICE';
        $tdocemisor = '01';
        $tdocidentidad = 6;

        if ($result['documento_tdocemisor'] === 'BOLETA') {
            $documento_serie = 'BB01';
            $numberValue = 'NUMBER_VOUCHER';
            $tdocemisor = '03';
            $tdocidentidad = 1;
        }

        if( $documento_numero == '' ){
            $documento_numero = str_pad( ( new ConfiguracionRepo )->GetValor($numberValue)["valor"], 5, '0', STR_PAD_LEFT);
        }

        $values = array(
            'modo' => $config["modo"],
            'emisor_ruc' => $config["emisor_ruc"],
            'emisor_pass' => $config["emisor_pass"],
            'documento_tdocemisor' => $tdocemisor,
            'documento_serie' => $documento_serie,
            'documento_numero' => $documento_numero,
            'documento_moneda' => 'PEN',
            'documento_emision' => date("Y-m-d"),
            'fecha_emision' => date("Y-m-d"),
            'receptor_ruc' => substr($result['empresa_ruc'], 0, 11),
            'receptor_tdocidentidad' => $tdocidentidad,
            'receptor_razonsocial' => $result['empresa_nombre'],
            'propiedad_adicional' => [
                ['id' => '1000', 'nombre' => 'Monto en Letras', 'valor' => ( new SessionRepo )->numeroLetras($totalMontoPagar)],
            ],
            'totalventa' => $totalMontoPagar,
            'totalmonetario' => [
                array('id' => '1001', 'montopagable' => $totalventa), //CATALOGO 15
            ],
            'totalimpuesto' => [
                array(
                    'importe' => $importeIgv,
                    'subtotals' => array(
                        array(
                            'id' => '1000',
                            'nombre' => 'IGV',
                            'codigo_impuesto' => 'VAT',
                            'importe' => $importeIgv,
                        )
                    )
                )
            ],
            'comprobante_detalle' => $detalleValues
        );
        // Detracción
        if (isset($result['detraccion']) && $result['detraccion'] > 0.00) {
            array_push($values['propiedad_adicional'], ['id' => '3000', 'valor' => '022']);
            array_push($values['propiedad_adicional'], ['id' => '3001', 'valor' => '00-003-065189']);
            $values['totalmonetario'][] = ['id' => '2003', 'montopagable' => $result['detraccion'], 'porcentaje' => '10'];
        }
        return array( $result, $numberValue, $documento_serie, $documento_numero, $values, $detalle );
    }

    private function nota_config( $result, $config, $documento_numero = '' )
    {
        $data = [];
        $detalleValues = [];
        $totalSinImpuesto = 0;
        $totalMontoPagar  = $result["precio"];
        //Importe
        $totalWarrantyVenta = 0;
        $totalWarrantyMontoPagar = 0;
        $descWarranty = '';

        $totalSinImpuesto = round( $totalMontoPagar / 1.18, 2);
        $importeIgv = $totalMontoPagar - $totalSinImpuesto;

        $detalleValues[0]['cantidad'] = 1;
        $detalleValues[0]['medida'] = 'ZZ';
        $detalleValues[0]['precioconimpuesto'] = $totalMontoPagar;
        $detalleValues[0]['preciosinimpuesto'] = $totalSinImpuesto;
        $detalleValues[0]['preciototal']       = $totalMontoPagar;
        $detalleValues[0]['precioventatipo']   = '01';
        $detalleValues[0]['descripcion']       = 'SERVICIO OFICINA VIRTUAL';
        $detalleValues[0]['customcode']        = 1;
        $detalleValues[0]['tipo_igv']          = '10';
        $detalleValues[0]['igv']               = $importeIgv;

        $numberValue = 'NUMBER_NOTA_DEBITO';
        $documento_serie = 'FF08';
        $documento_tdocemisor = '08';
        if ($result['tipo'] === 'CREDITO') {
            $numberValue = 'NUMBER_NOTA_CREDITO';
            $documento_serie = 'FF07';
            $documento_tdocemisor = '07';
        }

        if( $documento_numero == '' ){
            $documento_numero = str_pad( ( new ConfiguracionRepo )->GetValor($numberValue)["valor"], 5, '0', STR_PAD_LEFT);
        }

        $values = array(
            'modo' => $config["modo"],
            'emisor_ruc' => $config["emisor_ruc"],
            'emisor_pass' => $config["emisor_pass"],
            'documento_tdocemisor' => $documento_tdocemisor,
            'documento_serie' => $documento_serie,
            'documento_numero' => $documento_numero,
            'documento_moneda' => 'PEN',
            'documento_emision' => date("Y-m-d"),
            'fecha_emision' => date("Y-m-d"),
            'receptor_ruc' => substr($result['empresa_ruc'], 0, 11),
            'receptor_tdocidentidad' => 6,
            'receptor_razonsocial' => $result['empresa_nombre'],
            'propiedad_adicional' => [
                ['id' => '1000', 'nombre' => 'Monto en Letras', 'valor' => ( new SessionRepo )->numeroLetras($totalMontoPagar)],
            ],
            'totalventa' => $totalMontoPagar,
            'totalmonetario' => [
                array('id' => '1001', 'montopagable' => $totalSinImpuesto), //CATALOGO 15
            ],
            'totalimpuesto' => [
                array(
                    'importe' => $importeIgv,
                    'subtotals' => array(
                        array(
                            'id' => '1000',
                            'nombre' => 'IGV',
                            'codigo_impuesto' => 'VAT',
                            'importe' => $importeIgv,
                        )
                    )
                )
            ],
            'comprobante_detalle' => $detalleValues,
            'documento_discrepancia' => array(
                'id' => $result['docmod'],
                'codigo' => '0' . $result['cod_discrepancia'],
                'descripcion' => $result['observacion'],
            ), //CATALOGO 09
            'documento_billing' => array('id' => $result['docmod'], 'tipo_documento' => '01',)
        );

        return array( $result, $numberValue, $documento_serie, $documento_numero, $values, $detalleValues );//$detalle
    }

    private function factura_update_sunat( $value, $nota = false )
    {
        //echo "<pre>,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,</pre>";
        //echo "<pre>"; print_r( $value ); echo "</pre>";
        //echo "<pre>,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,</pre>";
        if ($nota) {
            return FacturaNota::where( "id", $value["factura_id"] )->update(
                array(
                    'su_state' => $value['state'],
                    'su_info' => $value['info'],
                    "fecha_emision" => date("Y-m-d")
                )
            );
        } else {
            return Factura::where( "id", $value["factura_id"] )->update(
                array(
                    'su_state' => $value['state'],
                    'su_info' => $value['info'],
                    "fecha_emision" => date("Y-m-d")
                )
            );
        }
    }

    public function payment_detail($factura_id)
    {
        $factura = Factura::where( "id", $factura_id )->first();
        $pago    = Pago::where( "factura_id", $factura_id )->get();
        $adelant = ( new QueryRepo )->Q_adelantos_pendientes( array( "factura_id" => $factura_id ) );
        if( $adelant["load"] ){
            $adelant = $adelant["rows"];
        }else{
            $adelant = [];
        }
        return [ "data" => [ "factura" => $factura, "pago" => $pago, "adelantos" => $adelant  ] ];
    }

    public function search( $getparams )
    {
        return  ( new QueryRepo )->Q_facturacion( $getparams );
    }

    public function getone($factura_id)
    {
        $comprobante = $this->factura_by_id($factura_id);
        $detalle     = $this->factura_item($factura_id);
        return [ "comprobante" => $comprobante, "detalle" => $detalle];
    }

    public function report_pagos( $anio, $mes, $getparams )
    {
        $getparams["anio"] = $anio;
        $getparams["mes"] = $mes;
        return ( new QueryRepo )->Q_pagos( $getparams );
    }

    public function report_facturacion( $anio, $mes, $getparams )
    {
        $getparams["anio"] = $anio;
        $getparams["mes"] = $mes;
        return ( new QueryRepo )->Q_facturacion( $getparams );
    }

    public function facturacion_empresas( $anio, $mes, $ciclo )
    {
        $nextnumero = ( new ConfiguracionRepo )->GetValor("NUMBER_INVOICE");
        return [
            "nextnumero" => $nextnumero["valor"],
            "rows" => ( new SessionRepo )->CallRaw( "mysql", "AL_FACTURACION_EMPRESAS", array( $anio, $mes, $ciclo ) )
        ];
    }


    public function delete_pago( $pagoID )
    {
        return (new SessionRepo )->CallRaw( "mysql", "AL_PAGO_ANULAR", array( $pagoID ) )[0];
    }
    

    public function factura_by_id($id)
    {
        return Factura::where( 'id', $id )->first();
    }

    public function factura_detalle($id)
    {
        return array( "item" => $this->factura_item($id), "notas" => $this->factura_nota($id)["rows"] );
    }

    public function factura_nota($id)
    {
        return ( new QueryRepo )->Q_notas_lista( array( "factura_id" => $id ) );
    }

    public function nota_search( $params ){
        return ( new QueryRepo )->Q_notas_lista( $params );
    }

    public function factura_historial($id)
    {
        return ( new QueryRepo )->Q_facturacion_historial( array( "factura_id" => $id ) );
    }

    public function factura_item($id)
    {
        $items = FacturaItem::where( 'factura_id', $id )->where( 'estado', 'A' )->select(\DB::raw("id, descripcion, descripcion_sunat, precio AS precioconimpuesto, ROUND(precio/1.18, 2) AS preciosinimpuesto, is_nota, warranty, anio, mes, tipo, custom_id, precio"))->get();
        return $items;
    }


    public function comprobantePDF( $receptor_ruc, $documento, $serie, $numero )
    {
        $pdf = array("load" => false, "message" => "Documento No encontrado" );
        $empresa = (array)json_decode( ( new EmpresaRepo )->getByNDOC( $receptor_ruc ) );
        if( !empty( $empresa ) ){
            $config = (array)json_decode( ( new ConfiguracionRepo )->GetValor('SUNAT_PARAMS')["valor"] );
            $documento = str_replace( "NOTA DE ", "", $documento );
            if( $documento == 'FACTURA' ){
                $comp = (array)json_decode( Factura::where( "comprobante", $documento )->where( "numero", $serie."-".str_pad( $numero, 5, "0", STR_PAD_LEFT ) )->where( "empresa_id", $empresa["id"] )->first() );
                if(!empty($comp)){
                    $comp['documento_tdocemisor'] = $documento;
                    $comp['empresa_ruc'] = $receptor_ruc;
                    $comp['serie'] = explode( "-", $comp['numero'] )[0];
                    $comp['numero'] = str_replace( $comp["serie"]."-", "", $comp["numero"] );
                    list( $result, $numberValue, $documento_serie, $documento_numero, $values, $detalle ) = $this->factura_config( ($comp+$empresa), $config);
                    list( $loadedPDFfile, $pdf ) = $this->factura_pdf_prepare( ($comp+$empresa), $detalle, $config  );
                    if( $loadedPDFfile ){
                        $pdf = ["load" => true, "data" => $pdf];//landscape
                    }
                }
            }elseif( $documento == 'CREDITO' || $documento == 'DEBITO' ){
                $comp = (array)json_decode( FacturaNota::where( "tipo", $documento )->where( "numero", $serie."-".str_pad( $numero, 5, "0", STR_PAD_LEFT ) )->first() );
                if(!empty($comp)){
                    $compmod = (array)json_decode( Factura::where( "id", $comp["factura_id"] )->first() );
                    $comp['docmod']               = $compmod["numero"];
                    $comp['docmodemision']        = $compmod["fecha_emision"];
                    $comp['documento_tdocemisor'] = "NOTA DE ".$documento;
                    $comp['empresa_ruc']          = $receptor_ruc;
                    $comp['serie']                = explode( "-", $comp['numero'] )[0];
                    $comp['numero']               = str_replace( $comp["serie"]."-", "", $comp["numero"] );
                    list( $result, $numberValue, $documento_serie, $documento_numero, $values, $detalle ) = $this->nota_config( ($comp+$empresa), $config);
                    list( $loadedPDFfile, $pdf ) = $this->factura_pdf_prepare( ($comp+$empresa), $detalle, $config  );
                    if( $loadedPDFfile ){
                        $pdf = ["load" => true, "data" => $pdf];//landscape
                    }
                }
            }
        }
        return $pdf;
    }

    //url codificada de comunicacion con la SUNAT.
    private function sendToSunat($order_data)
    {
        $url = 'http://service.facturame.pe/sunat/send/sendBill?json=' . urlencode(json_encode($order_data));
        #echo "<pre>"; print_r( $url ); echo "</pre>";
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $url
        ]);
        $rpta = json_decode(curl_exec($ch), true);
        curl_close($ch);
        return $rpta;
    }

    //ESTRUCTURA PDF
    private function factura_pdf_prepare( $comprobante, $detalle, $config )
    {
        $montoTotal       = 0.00;
        $montoUnitario    = 0.00;
        $subTotalVentas   = 0.00;
        $descuento        = 0.00;
        $subWarrantyTotal = 0.00;
        $warrantyTotal    = 0.00;
        $descWarranty     = '';
        /*
        foreach ($detalle as $key) {
            if (isset($key['warranty']) && $key['warranty'] == 'S') {
                // Para crear otra linea de descripcion en la factura, si tiene un item de garantía
                $subWarrantyTotal = $key['preciosinimpuesto'];
                $warrantyTotal    = $key['precioconimpuesto'];
                $descWarranty     = $key['descripcion_sunat'];
            } else {
                $subTotalVentas += $key['preciosinimpuesto'];
                $montoTotal     += $key['precioconimpuesto'];
            }
        }
        */
        //$montoUnitario = round($montoTotal / 1.18, 2);
        //$datos = array( $montoTotal, $montoUnitario, $subTotalVentas, $descuento, $subWarrantyTotal, $warrantyTotal, $descWarranty );
        /*
        echo "<pre>";
        print_r( $comprobante );
        echo "</pre>";
        
        echo "<pre>";
        print_r( $detalle );
        echo "</pre>";
        */


        if(       isset( $comprobante["comprobante"] ) && ( $comprobante["comprobante"] == "CREDITO" || $comprobante["comprobante"] == "DEBITO") ){
            $totalMontoPagar = $comprobante["totalventa"];
            $datos = $detalle;
        }else if( isset( $comprobante["tipo"] )        && ( $comprobante["tipo"] == "CREDITO"        || $comprobante["tipo"] == "DEBITO" ) ){ 

            $totalMontoPagar = $comprobante["precio"];
            $datos = $detalle;
        }else if( isset( $comprobante["documento_tdocemisor"] ) && ($comprobante["documento_tdocemisor"] == "07" || $comprobante["documento_tdocemisor"] == "08")  ){

            $totalMontoPagar = $comprobante["totalventa"];
            $datos = $detalle;
        }else{
            list( $totalMontoPagar, $datos ) = $this->sunatDetalle($detalle);    
            /*print_r( $datos );*/
        }
        
        /**
        echo "<pre>";
        print_r( $datos );
        echo "</pre>";
        /**/
        $html = ( new PDFRepo )->HTML_comprobante( $comprobante, $detalle, $config, $datos, $totalMontoPagar );
        $pdf = ( new PDFRepo )->PDF_HTML( $html );
        /**/
        /*return $pdf->setPaper('a4', 'portrait')->setWarnings(false)->download();*/
        return array( true, $pdf );
        //->stream();//->download();
    }




    public function createPago( $empresa_id, $factura_id, $params )
    {
        $return  = [ "load" => false, "message" => "" ];
        $factura = Factura::where( "id", $factura_id )->first();
        if( !empty( $factura ) ){
            if( $factura["estado"] == "PENDIENTE" ){
                $factura["monto"] = $factura["monto"] * 1;
                $params["monto"] = $params["monto"] * 1;
                $totpagado = 0;
                $pago = Pago::where( "factura_id", $factura_id )->groupBy('factura_id')->selectRaw('SUM(IFNULL(monto,0)) as total')->first();
                if( !empty( $pago ) ){
                    $totpagado = $pago["total"]*1;
                }

                $garantiamonto = 0;
                $garantiaevade = false;
                if( $params["tipo"] == "GARANTIA" ){
                    //$params["monto"]                     
                    if( ( $totpagado + $params["monto"] ) > $factura["monto"] ){
                        $garantiamonto = ( $factura["monto"] - $totpagado );
                        $garantiaevade = true;
                    }
                }

                if( ( ( $totpagado + $params["monto"] ) <= $factura["monto"] ) || $garantiaevade ){
                
                    $error = 0;
                    if( $error <= 0 ){
                        if( isset( $params["detraccionD"] ) && $params["detraccionD"] > 0 ){
                            if( ( $params["monto"] > 700 ) && ( round( ( $params["monto"] * 0.1 ), 2 ) != $params["detraccionD"] ) ){
                                $error = 1;
                                $return["message"] = "Para ser detraccion, el monto debe ser mayor a 700";
                            }
                        }
                    }
                    
                    if( $error <= 0 ){
                        $params["monto"] = $params["monto"] * 1;
                        $params["detraccionD"] = isset( $params["detraccionD"] ) ? $params["detraccionD"] : 0;
                        if( isset( $params["id_pos"] ) && isset( $params["des_com_pos"] ) && $params["des_com_pos"] > 0 ){
                            $m = ( ( $params["monto"] - ( $params["detraccionD"] > 0 ? ( $params["monto"] * 0.1 ) : 0 ) ) * 0.0337 );
                            if( $params["id_pos"] == '1' ){

                                $params["des_com_pos"] = $params["des_com_pos"] * 1;
                                if( $m != $params["des_com_pos"] ){
                                    $error = 1;
                                    $return["monto1"] = $m;
                                    $return["monto2"] = $params["des_com_pos"];
                                    $return["message"] = "El monto por interes de tarjeta esta mal Pago no aceptado";
                                }
                            }else if( $params["id_pos"] == '2' && $params["des_com_pos"] > 0 ){

                                $params["des_com_pos"] = $params["des_com_pos"] * 1;
                                $m = ( ( $params["monto"] - ( $params["detraccionD"] > 0 ? ( $params["monto"] * 0.1 ) : 0 ) ) * 0.042451 );
                                if( $m != $params["des_com_pos"] ){
                                    $error = 1;
                                    $return["monto1"] = $m;
                                    $return["monto2"] = $params["des_com_pos"];
                                    $return["message"] = "El monto por interes de tarjeta esta mal Pago no aceptado";
                                }
                            }
                        }
                    }

                    if( $error <= 0 ){
                        $newpago = Pago::create( 
                            array(
                                'tipo'              => $params["tipo"],
                                'fecha_creacion'    => date("Y-m-d H:i:s"),
                                'deposito_banco'    => isset($params["deposito_banco"]) ? $params["deposito_banco"] : "",
                                'deposito_cuenta'   => isset($params["deposito_cuenta"]) ? $params["deposito_cuenta"] : "",
                                'deposito_fecha'    => isset($params["deposito_fecha"]) ? $params["deposito_fecha"] : "",
                                'detalle'           => isset($params["detalle"]) ? $params["detalle"] : "",
                                'observacion'       => isset($params["observacion"]) ? $params["observacion"] : "",
                                'monto'             => $garantiamonto > 0 ? $garantiamonto : $params["monto"],
                                'factura_id'        => $factura_id,
                                'usuario'           => \Auth::user()->nombre,
                                'pago_factura_id'   => isset($params["factura_item_id"]) ? $params["factura_item_id"] : 0,
                                'id_pos'            => isset($params["id_pos"]) ? $params["id_pos"] : 0,
                                'dif_dep_pos'       => isset($params["dif_dep_pos"]) ? $params["dif_dep_pos"] : 0,
                                'des_com_pos'       => isset($params["des_com_pos"]) ? $params["des_com_pos"] : 0,
                                'detraccionD'       => isset($params["detraccionD"]) ? $params["detraccionD"] : 0,
                                'detraccionE'       => isset($params["detraccionE"]) ? $params["id_pos"] : 0
                            ) 
                        );
                        if( $params["tipo"] == "GARANTIA" && isset( $params["factura_item_id"] ) ){




                            $fitemgarantia = FacturaItem::where( "id", $params["factura_item_id"] )->first();
                            if( isset( $params["factura_item_id"] ) && $params["factura_item_id"] > 0 ){
                                FacturaItem::where( "id", $params["factura_item_id"] )->where( "tipo", "G" )->update(
                                    array( "custom_id" => 1 )
                                );
                            }
                            //CREAR NOTA DE CREDITO HACIA LA FACTURA QUE CONTIENE LA GARANTIA
                            //LA GARANTIA SERA ESTABLECIDA COMO USADA
                            if( !empty($fitemgarantia) ){



                                if( $garantiamonto > 0 ){
                                        FacturaItem::where( "id", $params["factura_item_id"] )->where( "tipo", "G" )->update(
                                            array( "precio" => $garantiamonto )
                                        );
                                        FacturaItem::create(
                                            array(
                                                "factura_id"                => $fitemgarantia["factura_id"],
                                                "descripcion"               => "GARANTIA SALDO DESPUES DE PAGO",
                                                "descripcion_sunat"         => "GARANTIA",
                                                "precio"                    => ( $params["monto"] - $garantiamonto ),
                                                "estado"                    => "A",
                                                "factura_item_temporal_id"  => 0, 
                                                "warranty"                  => "N",
                                                "is_nota"                   => "N",
                                                "anio"                      => 0,
                                                "mes"                       => 0,
                                                "tipo"                      => "G",
                                                "custom_id"                 => 0
                                            )
                                        );
                                }

                                
                                $return["fitemgarantia1"] = $fitemgarantia;
                                $params = [
                                    "monto"         => $garantiamonto > 0 ? $garantiamonto : $params["monto"],
                                    "anular"        => "NO",
                                    "tipo"          => "CREDITO",
                                    "observacion"   => "DEVOLUCION POR GARANTIA",
                                    "factura_item"  => [$params["factura_item_id"]],
                                ];
                                $notanueva = ( new FacturaRepo )->createnota( $empresa_id, $fitemgarantia["factura_id"], $params );
                            }
                        }
                        $return["pagado"] = 0;

                        $return["load"] = true;
                        if( ( ($totpagado) + ($params["monto"]) ) >= ($factura["monto"]) ){
                            //DAR PRIVILEGIOS AL COMPLETAR EL PAGO DEL COMPROBANTE
                            $return["pagado"] = 1;
                            $this->pagoComprobantePrivilegio( $empresa_id, $factura_id );
                            Factura::where( "id", $factura_id )->update( array( "estado" => "PAGADA", "fecha_pago" => date( "Y-m-d H:i:s" ) ) );
                        }
                    }

                    $return["pagototal"] = ( ($totpagado*1) + ($params["monto"]*1) );
                    $return["condi"] = ( ( ($totpagado*1) + ($params["monto"]*1) ) >= ($factura["monto"]*1) );
                    $return["error"] = $error;
                    $return["pago"] = $totpagado;
                    $return["monto"] = $params["monto"];
                    $return["factura"] = $factura["monto"];

                }else{
                    if( $totpagado == $factura["monto"] && $factura["estado"] == "PENDIENTE" ){
                        Factura::where( "id", $factura_id )->update( array( "estado" => "PAGADA", "fecha_pago" => date( "Y-m-d H:i:s" ) ) );
                        $return["message"] = "Parece que el pago no se proceso correctamente pero ya esta pagado ahora gracias y disculpe por las molestias";
                        $this->pagoComprobantePrivilegio( $empresa_id, $factura_id );
                        $return["load"] = true;
                    }else{
                        $return["message"] = "El monto a pagar supera el monto del comprobante con posiblemente pagos previos ( Monto de Comp. ".number_format( $factura["monto"], 2 ).", montos de pagos previos ".number_format( $totpagado, 2 ).", monto de pago ".number_format( $params["monto"], 2 )."  )";
                    }
                }
            }else{
                $return["message"] = "El Comprobante debe estar PENDIENTE";
            }
        }else{
            $return["message"] = "El Comprobante no existe";
        }
        return $return;
    }

    private function pagoComprobantePrivilegio( $empresa_id, $factura_id )
    {
        $detale = FacturaItem::where( "factura_id", $factura_id )->get();
        if( !empty( $detale ) ){
            foreach( $detale as $det ){
                $horas = $det["custom_id"] > 0 ? $det["custom_id"] : ( $det["precio"] / 30 );
                if( $det["tipo"] == "HO" ){
                    $this->recursoHorasPeriodo( $empresa_id, $det["anio"], $det["mes"], 0, $horas, 0, 0, 0 );
                }else if( $det["tipo"] == "HR" ){
                    $this->recursoHorasPeriodo( $empresa_id, $det["anio"], $det["mes"], $horas, 0, 0, 0, 0 );
                }else if( $det["tipo"] == "C" ){
                    $p = $this->getPlanParam( $det["custom_id"] );
                    $this->recursoHorasPeriodo( 
                        $empresa_id, $det["anio"], $det["mes"], 
                        $p["horas_reunion"], $p["horas_privada"], $p["horas_capacitacion"], 
                        $p["cantidad_copias"], $p["cantidad_impresiones"]
                    );
                }else if( $det["tipo"] == "P" && $det["custom_id"] > 0 ){
                    $p = $this->getPlanParam( $det["custom_id"] );
                    $this->recursoHorasPeriodo( 
                        $empresa_id, $det["anio"], $det["mes"], 
                        $p["horas_reunion"], $p["horas_privada"], $p["horas_capacitacion"], 
                        $p["cantidad_copias"], $p["cantidad_impresiones"]
                    );
                }else if( $det["tipo"] == "R" ||  $det["tipo"] == "A" ){
                    Reserva::where( "id", $det["custom_id"] )->update( array( "estado" => "A" ) );
                    //->where( "estado", "P" ) -- SE QUITO DEBIDO A QUE AHORA PUEDE ESTAR EN P PENDIENTE O J JUSTIFICADO
                }
            }
        }
    }

    public function getPlanParam( $plan_id )
    {
        return Plan::where( "id", $plan_id )->first();
    }

    private function recursoHorasPeriodo( $empresa_id, $anio, $mes, $h_reunion = 0, $h_privada = 0, $h_capacitacion = 0, $copias = 0, $impresion = 0 )
    {
        $recperiodo = RecursoPeriodo::where('empresa_id', $empresa_id )->where('anio', $anio )->where('mes', $mes )->first();
        if( empty( $recperiodo ) ){
            RecursoPeriodo::create( 
                array(
                    'empresa_id'            => $empresa_id,
                    'anio'                  => $anio,
                    'mes'                   => $mes,
                    'cantidad_copias'       => !$copias ? 0 : $copias,
                    'cantidad_impresiones'  => !$impresion ? 0 : $impresion,
                    'horas_reunion'         => !$h_reunion ? 0 : $h_reunion,
                    'horas_privada'         => !$h_privada ? 0 : $h_privada,
                    'horas_capacitacion'    => !$h_capacitacion ? 0 : $h_capacitacion
                ) 
            );
        }else{
            RecursoPeriodo::where('empresa_id', $empresa_id )->where('anio', $anio )->where('mes', $mes )->update(
                array(
                    'cantidad_copias'       => \DB::raw( 'cantidad_copias+'.$copias ),
                    'cantidad_impresiones'  => \DB::raw( 'cantidad_impresiones+'.$impresion ),
                    'horas_reunion'         => \DB::raw( 'horas_reunion+'.$h_reunion ),
                    'horas_privada'         => \DB::raw( 'horas_privada+'.$h_privada ),
                    'horas_capacitacion'    => \DB::raw( 'horas_capacitacion+'.$h_capacitacion )
                )
            );
        }
        RecursoPeriodoHistorial::create(
            array(
                'empresa_id'            => $empresa_id, 
                'anio'                  => $anio, 
                'mes'                   => $mes, 
                'reserva_id'            => date("YmdHis"), 
                'fecha'                 => date("Y-m-d H:i:s"), 
                'horas_reunion'         => $h_reunion, 
                'horas_privada'         => $h_privada, 
                'horas_capacitacion'    => $h_capacitacion, 
                'estado'                => 2, 
                'usuario'               => \Auth::user()->nombre
            )
        );

    }
}
?>