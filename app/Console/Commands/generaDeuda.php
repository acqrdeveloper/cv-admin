<?php
namespace CVAdmin\Console\Commands;
use Illuminate\Console\Command;
use CVAdmin\CV\Models\EmpresaServicio;
use CVAdmin\CV\Models\FacturaTemporal;
use CVAdmin\CV\Repos\FacturaRepo;
use CVAdmin\Common\Repos\QueryRepo;

class generaDeuda extends Command
{
    protected $signature = 'genera:Deuda';
    protected $description = 'Command description';
    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {
        $diafinal  = ( date( 't', strtotime( date( "Y-m-d" ) ) ) * 1 );
        $diactual  = ( date( "d" ) * 1 );
        $quincena  = 11;
        $diasantes = 3;
        if( $diactual == ( $diafinal - $diasantes ) ){
            $anio = date( "Y", strtotime( "+1 month", strtotime( date("Y-m-d") ) ) );
            $mes = date( "m",  strtotime( "+1 month", strtotime( date("Y-m-d") ) ) );
        }else{
            $anio = date("Y");
            $mes  = date("m");
        }
        //CONDICIONAL SOLO SE EJECUTARA LOS 12 Y 3 DIAS ANTES DE FIN DE MES
        if( ( $diactual == ( $diafinal - $diasantes ) ) || ( $diactual ==  $quincena ) ){
            $ciclo = ( $diactual ==  $quincena ) ? "QUINCENAL" : "MENSUAL";
            $empresas = ( new QueryRepo )->Q_facturacion_mensual_empresas(
                array( 
                    "anio"  => $anio,
                    "mes"   => $mes,
                    "ciclo" => $ciclo
                )
            );

            if( !empty( $empresas ) && count( $empresas ) > 0 ){
                foreach( $empresas["rows"] as $emp ){
                    
                    $emp = (array)$emp;
                    print_r( $emp );

                    /*

                    $plan      = 0;
                    $extra     = 0;
                    $descuento = 0;
                    $combos    = [];

                    $servicios = EmpresaServicio::where( "empresa_id", $emp["id"] )->get();
                    if( !empty( $servicios ) && count( $servicios ) > 0 ){
                        foreach( $servicios as $s ){
                            if( $s["tipo"] == "E" ){
                                $extra     = $extra     + abs( $s["monto"] );
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

                    $this->info( "Plan ".$plan." - Extra ". $extra." - Descuento ".$descuento." " );
                    */
                    ( new FacturaRepo )->comprobanteMensual( $emp, $anio, $mes, false );
                }
            }
        }
    }
}