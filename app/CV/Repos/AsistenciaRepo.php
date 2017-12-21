<?php
namespace CVAdmin\CV\Repos;
use CVAdmin\CV\Models\Reserva;
use CVAdmin\CV\Models\ReservaInvitado;
use CVAdmin\Common\Repos\QueryRepo;
class AsistenciaRepo{
	public function search( $tipo, $params ){
		$return = [];
		if( $tipo == 'I' ){
			$return = ( new QueryRepo )->Q_invitado_eventos( $params );
		}else{
			$return = ( new QueryRepo )->Q_eventos( $params );
		}
		return $return;
	}

	public function massive( $reserva_id, $estructura ){
		$return  = [];
		$reserva = Reserva::where( "id", $reserva_id )->first();#existe reserva
		if( !empty( $reserva ) ){
			if( strtotime( $reserva["fecha_reserva"]." ".$reserva["hora_inicio"] ) > strtotime( date( "Y-m-d H:i:s" ) ) ){#permite subir otra lista mientras el evento aun no inicie
				foreach( $estructura as $params ){
					$subreturn = $this->create( $reserva_id, $params );
					array_push( $return, $subreturn );
				}
			}
		}
		return $return;
	}

	public function create( $reserva_id, $params, $nuevo = 0 ){
		$return = [];		
		if( isset( $params["dni"] ) && $params["dni"] != "" && strlen( $params["dni"] ) == 8 ){
			$return = ReservaInvitado::create(
				array(
					'reserva_id' 	=> $reserva_id,
					'dni' 			=> $params["dni"],
					'nomape' 		=> isset( $params["nomape"] ) ? $params["nomape"] : "",
					'email' 		=> isset( $params["email"] )  ? $params["email"]  : "",
					'movil' 		=> isset( $params["movil"] )  ? $params["movil"]  : "",
					'created_at' 	=> date("Y-m-d H:i:s"),
					'updated_at' 	=> date("Y-m-d H:i:s"),
					'estado' 		=> 'A',
					'asistencia' 	=> $nuevo,
					'nuevo' 		=> $nuevo
				)
			);
		}
		return $return;
	}

	public function asistencia( $reserva_id, $dni ){
		$return = ReservaInvitado::where( 'reserva_id', $reserva_id )->where( 'dni', $dni )->update(
			array(
				'updated_at' => date("Y-m-d H:i:s"),
				'asistencia' => 1
			)
		);
		return $return;
	}

	public function delete( $reserva_id, $dni ){
		$return = ReservaInvitado::where( 'reserva_id', $reserva_id )->where( 'dni', $dni )->delete();
		return $return;
	}
}
?>