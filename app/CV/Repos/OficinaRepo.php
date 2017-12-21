<?php
namespace CVAdmin\CV\Repos;
use CVAdmin\CV\Models\Oficina;
use CVAdmin\CV\Models\OficinaAnulacion;
use CVAdmin\CV\Models\EmpresaServicio;
use CVAdmin\Common\Repos\SessionRepo;
use CVAdmin\Common\Repos\QueryRepo;

class OficinaRepo{

	public function ReservaAuditorio_CoffeeBreak( $p ){
		return ( new QueryRepo )->Q_ReservaAuditorio_CoffeeBreak( $p );
	}
	
	public function ofiAnulaList( $p ){
		return ( new QueryRepo )->Q_OficinaAnulacion( $p );
	}
	public function ofiAnulaCreate( $p ){
		$return = ( new SessionRepo )->CallRaw("mysql", "AL_OFICINA_ANULACION_INSERT", [ $p["oficina_id"], $p["hini"], $p["hfin"], $p["dia"], $p["empresa_id"] ] ) ;
		return $return[0];
	}
	public function ofiAnulaDelete( $p ){
		return OficinaAnulacion::where( 'oficina_id', $p["oficina_id"] )->where( 'hini', $p["hini"] )->where( 'hfin', $p["hfin"] )->where( 'dia', $p["dia"] )->delete();
	}


	public function disponibility( $fecha, $oficinaID, $reservaID ){
		$disponibility = ( new SessionRepo )->CallRaw("mysql", "AL_OFICINA_DISPONIBILIDAD", [ $fecha, $oficinaID, $reservaID ] ) ;
		return $disponibility;
	}
	public function getOficinaList( $param ){
		$oficinas = Oficina::where( "local_id", $param["local_id"] )->where( "modelo_id", $param["modelo_id"] )->where( "estado", "A" )->get();
		return $oficinas;
	}
	public function getCoworking( $local_id ){
		return Oficina::where( "local_id", $local_id )->where( "modelo_id", 4 )->where( "estado", "A" )->get(["id", "nombre_o", "disponibilidad", "empresa_id"]);
	}
	public function putCoworking( $oficina_id, $empresa_id ){
		$return   = [];
		$oficina  = Oficina::where( "id", $oficina_id );
		$ofiempr  = Oficina::where( "empresa_id", $empresa_id )->where( "estado", "A" )->count();
		$servicio = EmpresaServicio::where( "tipo", "P" )->where( "empresa_id", $empresa_id )->first();
		$ofi = $oficina->first();
		if( $empresa_id <= 0 ){
			$return = $oficina->update(
				array(
					"empresa_id" 		=> $empresa_id,
					"disponibilidad" 	=> ( $empresa_id > 0 ? "R" : "A" )
				)
			);
		}else{
			if( $ofi["empresa_id"] == 0 ){
				if( !empty($servicio) ){	
					if( $ofiempr < $servicio["servicio_extra_id"]  ){
						$return = $oficina->update(
							array(
								"empresa_id" 		=> $empresa_id,
								"disponibilidad" 	=> ( $empresa_id > 0 ? "R" : "A" )
							)
						);
					}else{
						$return = ["message" => "Ya alconzo el limite de asientos en su contrato (".$servicio["servicio_extra_id"].")"];
					}
				}else{
					$return = ["message" => "No tiene asientos asignados"];
				}
			}else{
				if( $ofi["empresa_id"] != $empresa_id ){
					$return = ["message" => "Ya asignado a otra persona"];
				}else{
					$return = ["message" => "Ya le fue asignado refresque el sistema por favor."];
				}
			}
		}
		if(is_array($return))
			throw new \Exception($return['message']);
	}
}
?>