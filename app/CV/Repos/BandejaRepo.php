<?php
namespace CVAdmin\CV\Repos;

use CVAdmin\CV\Models\Bandeja;
use CVAdmin\CV\Models\BandejaMensaje;
use CVAdmin\CV\Models\Empresa;
use CVAdmin\CV\Models\FacturaTemporal;

use CVAdmin\CV\Repos\EmpresaRepo;
use CVAdmin\CV\Repos\FacturaRepo;
use CVAdmin\CV\Repos\ServicioRepo;

use CVAdmin\Common\Repos\SessionRepo;
use CVAdmin\Common\Repos\QueryRepo;
class BandejaRepo{

	use \CVAdmin\Traits\MailTrait, \CVAdmin\Traits\NotificationTrait;

	public function getMyMessages( $tipo, $id, $params )
	{
		$params['de_tipo'] 	= $tipo;
		$params['de'] 		= $id;
		$params['a_tipo'] 	= $tipo;
		$params['a'] 		= $id;
		return $data = ( new QueryRepo )->Q_bandeja( $params );
	}

	public function getMyReceivedMessages( $tipo, $id, $params )
	{
		$params['a_tipo'] 	= $tipo;
		if( $id > 0 ){
			$params['a'] 	= $id;
		}
		return $data = ( new QueryRepo )->Q_bandeja( $params );
	}

	public function getMySendMessages( $tipo, $id, $params )
	{
		$params['de_tipo'] 	= $tipo;
		$params['de'] 		= $id;
		return $data = ( new QueryRepo )->Q_bandeja( $params );
	}

	public function getAllMessages( $params )
	{
		return $data = ( new QueryRepo )->Q_bandeja( $params );
	}

	public function getMessageDetail( $message_id )
	{
		$params = [ "padre_id" => $message_id ];//"respuesta_id" => $message_id, 
		return $data = ( new QueryRepo )->Q_bandeja( $params );
	}

	public function postNewMessages( $params )
	{
		$bandeja = Bandeja::create(
			array(
				'de_tipo'		=> $params["de_tipo"],
				'de'			=> $params["de"],
				'a_tipo'		=> $params["a_tipo"],
				'a'				=> $params["a"],
				'empresa_id'	=> $params["empresa_id"],
				'asunto'		=> $params["asunto"],
				'leido'			=> 0,
				'padre_id'		=> isset( $params["padre_id"] ) 	? $params["padre_id"]     : 0,
				'respuesta_id'	=> isset( $params["respuesta_id"] ) ? $params["respuesta_id"] : '0',
				'created_at'	=> date("Y-m-d H:i:s"),
				'updated_at'	=> date("Y-m-d H:i:s"),
				'usuario'		=> \Auth::user()->nombre
			)
		);
		$bandejamensaje = BandejaMensaje::create( array( "id" => $bandeja["id"], "mensaje" => $params["mensaje"] ) );

		$empresa = Empresa::find($params['empresa_id']);

		$return = [];

		// Send Notification
		$return['notification'] = $this->sendWSMessage($params["a_tipo"], ['message'=>$params["mensaje"], 'from'=>'Administrador', 'sid'=>$empresa->websocket]);

		// Send Push
		$response = $this->sendNotification($params["mensaje"], [$empresa->pushwoosh]);

		if($response->isOk()){
			$return['push'] ='Push sent';
		} else {
			$return['push'] = 'Oups, the sent failed :-( Status code : ' . $response->getStatusCode() . ' Status message : ' . $response->getStatusMessage();
		}

		return $return;
	}

	public function putReadMessages( $message_id )
	{
		return $bandeja = Bandeja::where( "id", $message_id )->update( 
			array( 
				"leido" 	=> 1,
				"quienleyo" => \Auth::user()->nombre
			) 
		);
	}

	public function putMessageAction( $empresa_id, $message_id, $response_id )
	{
		$return     = [ "load" => false, "message" => "" ];
		$bandejareg = Bandeja::where( "empresa_id", $empresa_id )->where( "id", $message_id );
		$bandeja    = $bandejareg->first();
		if( $bandeja["respuesta_id"] == '0' ){
			
			if( $response_id == '1' ){
				$bandejamensaje = BandejaMensaje::where( "id", $message_id )->first();
				if( $bandeja["asunto"] == 'H' ){
					$mensaje = json_decode( $bandejamensaje["mensaje"], true );
					$params = array(
						"reunion" 	=> $mensaje["tipo"] == "R" ? $mensaje["horas"] : 0,
						"privada" 	=> $mensaje["tipo"] == "P" ? $mensaje["horas"] : 0,
						"tipo"   	=> 1,
						"monto" 	=> $mensaje["horas"],
						"recurso" 	=> $mensaje["tipo"] == "R" ? "horas_reunion" : "horas_privada",
						"next" 		=> $mensaje["pago"] == "F" ? "on": "",
						"facturar" 	=> $mensaje["pago"] == "F" ? ""  : "on",
					);
					( new ServicioRepo )->setRecursoHoras( $empresa_id, $mensaje["anio"], $mensaje["mes"], $params );
					$return["message"] = "Horas Otorgadas";


				}else if( $bandeja["asunto"] == 'A' ){

				}
			}else if( $response_id == '2' ){
				$return["message"] = "Haz Rechazado la solicitud";
			}
			$bandejareg->update( array( "respuesta_id" => $response_id ) );
			$return["load"] = true;
		}else{
			$return["message"] = "Respuesta ya Dada";
		}
		return $return;
	}
}
?>