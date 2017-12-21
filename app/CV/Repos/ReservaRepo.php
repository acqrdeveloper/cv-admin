<?php
namespace CVAdmin\CV\Repos;

use Auth;
use Mail;
use Carbon\Carbon;
use CVAdmin\CV\Models\Cochera;
use CVAdmin\CV\Models\OficinaPromocion;
use CVAdmin\CV\Models\RecursoHistorial;
use CVAdmin\CV\Models\Reserva;
use CVAdmin\CV\Models\ReservaDetalle;
use CVAdmin\CV\Models\ReservaHistorial;

use CVAdmin\CV\Repos\AsistenciaRepo;
use CVAdmin\Common\Repos\SessionRepo;
use CVAdmin\Common\Repos\QueryRepo;

use CVAdmin\Mail\ReservaMail;

class ReservaRepo{

    public function auditorioprecio( $localID, $modeloID, $planID ){
        return OficinaPromocion::where( 'local_id', $localID)->where( 'modelo_id', $modeloID)->where( 'plan_id', $planID)->where( 'tipo', "H")->where( "desde", "<=", 0 )->first();        
    }

    public function getDetalle($reserva_id){
        return ReservaDetalle::where( "reserva_id", $reserva_id)->get();
    }

    public function changestate( $id, $estado ) {
        $a = Reserva::where( "id", $id )->update( array( "estado" => $estado ) );
        if( $estado == "A" ){
            ( new SessionRepo )->CallRaw("mysql", "AL_RESERVA_AUDITORIO", [ $id, \Auth::user()->nombre ]);
            \Mail::send( new ReservaMail($id) );
            //, $v["empresa_id"], $v["oficina_id"], $v["fecha"], $v["hini"], $v["hfin"], \Auth::user()->empresa_nombre, $montoExtras
            //GENERAR DEUDA
        }
        return $a;
    }

    public function create( $v ) {

    	if(!isset($v['proyector'])){
    		$v['proyector'] = 'NO';
    	}

    	if(!isset($v['cochera_id'])){
    		$v['cochera_id'] = 1;
    	}

    	if(!isset($v['placa'])){
    		$v['placa'] = "";
    	}

    	if(!isset($v['movil'])){
    		$v['movil'] = "N";
    	}

        if(!isset($v["observacion"]) || empty($v["observacion"])){
            $v["observacion"] = "[]";
        } else {
            $v["observacion"] = json_encode([[
                'usuario' => Auth::user()->nombre,
                'body' => $v["observacion"],
                'created_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]]);
        }

		$reserva = ( new SessionRepo )->CallRaw("mysql", "AL_RESERVA_CREATE", [ 
			$v["fecha"], 
			$v["oficina_id"],
			$v["hini"], 
			$v["hfin"], 
			$v["empresa_id"],
			$v["proyector"], 
			$v["cochera_id"],
			$v["placa"], 
			$v["movil"], //NO ANDROID IPHONE
			$v["observacion"], 
			\Auth::user()->nombre,
            isset($v["nombre"]) ? $v["nombre"] : "",
            isset($v["silla"])  ? $v["silla"]  : "0",
            isset($v["mesa"])   ? $v["mesa"]   : "N",
            isset($v["audio"])  ? $v["audio"]  : "N",
            isset($v["cupon"])  ? $v["cupon"]  : ""
		]);

        if( $reserva[0]["id"] > 0){
            $montoExtras = 0;
            if( isset( $v["detalle"] ) && count( $v["detalle"] ) > 0 ){
                foreach( $v["detalle"] as $det ){
                    //INSERT RESERVA DETALLE
                    ReservaDetalle::create(
                        array(
                            'reserva_id'    => $reserva[0]["id"], 
                            'concepto_id'   => $det["concepto"], 
                            'precio'        => $det["precio"], 
                            'cantidad'      => $det["cantidad"], 
                            'created_at'    => date("Y-m-d H:i:s")
                        )
                    );
                    $montoExtras = $montoExtras + ( $det["cantidad"] * $det["precio"] );
                }
            }

            if( isset( $v["estructura"] ) && count( $v["estructura"] ) > 0 ){
                ( new AsistenciaRepo )->massive( $reserva[0]["id"], $v["estructura"] );
            }


            /** Envio de correo **/
            Mail::send(new \CVAdmin\Mail\ReservaMail($reserva[0]['id']));
            /** fin envio correo **/
        }


		return $reserva[0];
    }


    public function delete($id) {
        $reserva = Reserva::find($id);

        if(is_null($reserva))
            throw new \Exception("La reserva no existe");

        // Iniciamos la transaccion
        \DB::beginTransaction();
        $result = (new SessionRepo)->CallRaw("mysql", "AL_RESERVA_CANCEL", [$id,\Auth::user()->nombre]);
        \DB::commit();

        if($result[0]['load'] == 1)
            Mail::send(new \CVAdmin\Mail\ReservaMail($id));
    }
 

    public function update( $id, $v ) {

        /**
          * Las observaciones en las reservas es un dato tipo json, 
          * al actualizar hay que fijarnos en la primera observacion
          */
        $reserva = Reserva::where('id', $id)->where('estado', '!=', 'E')->first();

        if(is_null($reserva)){
            throw new \Exception("La reserva buscada no existe");
        }

        $observaciones = $reserva->observacion;

        if(is_null($observaciones) || empty($observaciones)){
            $observaciones = [];
        } else {
            if(substr($observaciones,0,1) === "["){
                $observaciones = json_decode($observaciones, true);
                $observaciones = array_splice($observaciones, 1);
            } else {
                $observaciones = [];
            }
        }

        if(!empty($v['observacion'])){

            $obs = [
                'usuario' => Auth::user()->nombre,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'body' => $v['observacion']
            ];

            $observaciones = array_merge([$obs], $observaciones);
        }

        $observaciones = json_encode($observaciones);

        if(!isset($v['proyector'])){
            $v['proyector'] = 'NO';
        }

        if(!isset($v['cochera_id']) || empty($v['cochera_id'])){
            $v['cochera_id'] = 1;
        }

        if(!isset($v['placa'])){
            $v['placa'] = '';
        }

        if(!isset($v['movil'])){
            $v['movil'] = 'N';
        }

        $reserva = ( new SessionRepo )->CallRaw("mysql", "AL_RESERVA_UPDATE", [ 
            $id, 
            $v["fecha"], 
            $v["oficina_id"],
            $v["hini"], 
            $v["hfin"], 
            $v["proyector"], 
            $v["cochera_id"],
            $v["placa"], 
            $v["movil"], //NO ANDROID IPHONE
            $observaciones, 
            \Auth::user()->nombre,
            isset( $v["limpieza"] ) ? $v["limpieza"] : 0
        ]);



        if( $reserva[0]["load"] >= 1 ){
            $montoExtras = 0;
            ReservaDetalle::where( 'reserva_id', $id )->delete();
            if( isset( $v["detalle"] ) && count( $v["detalle"] ) > 0 ){
                foreach( $v["detalle"] as $det ){
                    //INSERT RESERVA DETALLE
                    ReservaDetalle::create(
                        array(
                            'reserva_id'    => $id, 
                            'concepto_id'   => $det["concepto"], 
                            'precio'        => $det["precio"], 
                            'cantidad'      => $det["cantidad"], 
                            'created_at'    => date("Y-m-d H:i:s")
                        )
                    );
                    $montoExtras = $montoExtras + ( $det["cantidad"] * $det["precio"] );
                }
            }

            if( isset( $v["estructura"] ) && count( $v["estructura"] ) > 0 ){
                ( new AsistenciaRepo )->massive( $id, $v["estructura"] );
            }
            
            //( new SessionRepo )->CallRaw("mysql", "AL_RESERVA_AUDITORIO", [ $reserva[0]["id"], $v["empresa_id"], $v["oficina_id"], $v["fecha"], $v["hini"], $v["hfin"], \Auth::user()->nombre, $montoExtras ]);
        }





        return $reserva[0];
    }

    public function getHistory($reserva_id){
        return ReservaHistorial::where( "reserva_id", $reserva_id )->get();
    	//return RecursoHistorial::getReserveHistory($reserva_id)->get(['tipo','fecha_registro', 'descripcion', 'observacion', 'cantidad', 'empleado']);
    }

	public function search( $fecha, $getparams ){
		$getparams["fecha"] = $fecha;
		return ( new QueryRepo )->Q_reserva( $getparams );
	}

    public function updateObs($id, $obs){
        $params = [
            'usuario' => Auth::user()->nombre,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'body' => $obs
        ];

        \DB::transaction(function() use ($id, $params){
            $reserva = Reserva::find($id);


            $observaciones = $reserva->observacion;

            if(is_null($observaciones) || empty($observaciones)){
                $observaciones = [];
            } else {
                $observaciones = json_decode($observaciones, true);
            }

            $observaciones = array_merge([$params], $observaciones);
            /*
            $observaciones[] = $params;
            */

            $reserva->observacion = json_encode($observaciones);
            $reserva->save();
        });

        return $params;
    }
}
?>