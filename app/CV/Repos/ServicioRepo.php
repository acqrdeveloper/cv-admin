<?php
namespace CVAdmin\CV\Repos;
ini_set('max_execution_time', 3000);
use CVAdmin\CV\Models\RecursoPeriodo;
use CVAdmin\CV\Models\Empresa;
use CVAdmin\CV\Models\EmpresaServicio;
use CVAdmin\CV\Models\RecursoPeriodoHistorial;
use CVAdmin\CV\Repos\EmpresaRepo;
use CVAdmin\CV\Models\FacturaTemporal;
use CVAdmin\CV\Repos\FacturaRepo;
use CVAdmin\Common\Repos\QueryRepo;
class ServicioRepo
{
    public function getRecursoPeriodo( $empresa_id, $anio, $mes = 0 )
    {
        return (new QueryRepo)->Q_recurso_periodo([ "empresa_id" => $empresa_id, "anio" => $anio, "mes" => $mes ]);
    }

    public function setRecursoHoras( $empresa_id, $anio, $mes, $params )
    {
        $load = false;
        $data = [];
        $location = "start ";
        $emp = Empresa::where( "id", $empresa_id )->first();
        if( $params["tipo"] >= 1 ){
            $location .= " tipo > 1 ";
            if( ( count( ( new EmpresaRepo )->getDeuda( $empresa_id, "PENDIENTE" ) ) <= 0 ) || ( isset( $params["facturar"] ) && $params["facturar"] == 'on' ) ){
                $location .= " facturar on ";
                $pu = 0;
                if( $params["recurso"] == "horas_reunion" || $params["recurso"] == "horas_privada" ){
                    $pu = ( ( $emp["plan_id"] * 1 ) == 31 ) ? 40 :30;
                }else if( $params["recurso"] == "cantidad_copias" ){
                    $pu = 0.1;
                }else if( $params["recurso"] == "cantidad_impresiones" ){
                    $pu = 0.3;
                }

                $monto = ( $params["monto"] * $pu );

                if($params["recurso"] != "horas_reunion" && $params["recurso"] != "horas_privada"){
                    $descripcion = "SERVICIOS EXTRAS (".$anio."/".$mes.")";
                } else {
                    $descripcion = $params["monto"]. ' HORA(S) EXTRA(S) ' . (($params['recurso'] == 'horas_reunion')?'REUNIÓN':'PRIVADA') . ' (PERIODO ' . $mes.'/'.$anio.') - '.date('d/m/Y');
                }


                if( isset( $params["next"] ) && $params["next"] == 'on' ){
                    $location .= "  next on ";
                    //GENERAR FACTURA TEMPORAL
                    FacturaTemporal::create( 
                        array(
                            "empresa_id"        => $empresa_id,
                            "fecha_creacion"    => date("Y-m-d H:i:s"),
                            "descripcion"       => $descripcion,
                            "estado"            => "PENDIENTE",
                            "precio"            => $monto,
                            "periodo"           => $anio."-".$mes."-01",
                            "ex"                => "N",
                            "reserva_id"        => 0,
                            "cochera_proyector" => "N"
                        )
                    );
                    //RECARGAR SERVICIO
                    $data = $this->setHoras( $empresa_id, $anio, $mes, $params );
                    $load = true;
                }else if( isset( $params["facturar"] ) && $params["facturar"] == 'on' ){
                    $location .= "  facturar on ";
                    //GENERAR COMPROBANTE
                    $detalle = [];
                    array_push( $detalle, 
                        array(
                            "precio"            => $monto,
                            "descripcion"       => $descripcion,
                            "custom_id"         => 0,
                            "descripcion_sunat" => "SERVICIO EXTRA",
                            "estado"            => "A",
                            "anio"              => ( $params["recurso"] != "horas_reunion" && $params["recurso"] != "horas_privada" ) ? 0 : $anio,
                            "mes"               => ( $params["recurso"] != "horas_reunion" && $params["recurso"] != "horas_privada" ) ? 0 : $mes,
                            "tipo"              => ( $params["recurso"] != "horas_reunion" && $params["recurso"] != "horas_privada" ) ? "E" : (  $params["recurso"] == "horas_reunion" ? "HR" : "HO" )
                        )
                    );

                    $factura = array(
                        "items"             => $detalle,
                        "total"             => $monto,
                        "fecha_vencimiento" => date( 'Y-m-d', strtotime( "+3 days" ) ),
                        "fecha_limite"      => date( 'Y-m-d', strtotime( "+13 days" ) ),
                        "tipo"              => $emp["preferencia_comprobante"]
                    );
                    $data = [];
                    ( new FacturaRepo )->facturaCreate( $empresa_id, $factura );
                    $load = true;
                }else{
                  
                    $data = $this->setHoras( $empresa_id, $anio, $mes, $params );
                    $load = true;
                }

                $location .= " facturar off ";
                $load = true;
            }else{
                throw new \Exception( "Deuda Pendiente horas no agregadas" );
            }
        }else if( $params["tipo"] < 1 ){
            $location .= " tipo <> 1 ";
            $data = $this->setHoras( $empresa_id, $anio, $mes, $params );
            $load = true;
        }
        $recurso = [];
        if( $load ){
            $recurso = $this->getRecursoPeriodo( $empresa_id, $anio, $mes );
        }
    	return [ "load" => $load, "data" => $data, "recurso" => $recurso, "location" => $location ];
    }

    private function setHoras( $empresa_id, $anio, $mes, $params ){
        $recursope      = RecursoPeriodo::where( "empresa_id", $empresa_id )->where( "anio", $anio )->where( "mes", $mes )->first();
        if( !empty( $recursope ) ){
            $data = RecursoPeriodo::where( "empresa_id", $empresa_id )->where( "anio", $anio )->where( "mes", $mes )->update(
                array(
                    $params["recurso"]  => ( ( $recursope[$params["recurso"]] ) + ( $params["monto"] * ( $params["tipo"] >= 1 ? 1 : -1 ) ) )
                )
            );
        }else{
            $data = RecursoPeriodo::create( 
                array(
                    'empresa_id'            => $empresa_id, 
                    'anio'                  => $anio, 
                    'mes'                   => $mes, 
                    'cantidad_copias'       => $params["recurso"] == "cantidad_copias"      ? ( $params["monto"] * ( $params["tipo"] >= 1 ? 1 : -1 ) ) : 0, 
                    'cantidad_impresiones'  => $params["recurso"] == "cantidad_impresiones" ? ( $params["monto"] * ( $params["tipo"] >= 1 ? 1 : -1 ) ) : 0, 
                    'horas_reunion'         => $params["recurso"] == "horas_reunion"        ? ( $params["monto"] * ( $params["tipo"] >= 1 ? 1 : -1 ) ) : 0, 
                    'horas_privada'         => $params["recurso"] == "horas_privada"        ? ( $params["monto"] * ( $params["tipo"] >= 1 ? 1 : -1 ) ) : 0, 
                    'horas_capacitacion'    => $params["recurso"] == "horas_capacitacion"   ? ( $params["monto"] * ( $params["tipo"] >= 1 ? 1 : -1 ) ) : 0
                )
            );
        }


        RecursoPeriodoHistorial::create(
            array(
                'empresa_id'            => $empresa_id, 
                'anio'                  => $anio, 
                'mes'                   => $mes, 
                'reserva_id'            => date("His"), 
                'fecha'                 => date("Y-m-d H:i:s"), 
                'horas_reunion'         => $params["recurso"] == "horas_reunion"        ? abs( $params["monto"] ) : 0,
                'horas_privada'         => $params["recurso"] == "horas_privada"        ? abs( $params["monto"] ) : 0,
                'horas_capacitacion'    => $params["recurso"] == "horas_capacitacion"   ? abs( $params["monto"] ) : 0,
                'cantidad_copias'       => $params["recurso"] == "cantidad_copias"      ? abs( $params["monto"] ) : 0,
                'cantidad_impresiones'  => $params["recurso"] == "cantidad_impresiones" ? abs( $params["monto"] ) : 0,
                'estado'                => ( $params["tipo"] >= 1 ? 2 : 3 ), 
                'usuario'               => \Auth::user()->nombre
            )
        );

        return $data;
    }

    public function getEmpresaServicio( $empresa_id ){
        return EmpresaServicio::where( 'empresa_id', $empresa_id )->orderBy('monto','DESC')->get();
    } 

    public function setEmpresaServicio( $empresa_id, $params ){
        return EmpresaServicio::create(
            array(
                'servicio_extra_id'     => isset( $params["servicio_extra_id"] ) ? $params["servicio_extra_id"] : 0,
                'empresa_id'            => $empresa_id,
                'mes'                   => isset( $params["mes"] ) ? $params["mes"] : -1,
                'tipo'                  => $params["tipo"],
                'monto'                 => abs($params["monto"]),
                'concepto'              => isset( $params["concepto"] ) ? $params["concepto"] : ""
            )
        );
    }

    public function deleteEmpresaServicio( $id, $empresa_id ){
        return EmpresaServicio::where( 'id', $id )->where( 'empresa_id', $empresa_id )->delete();
    }
}