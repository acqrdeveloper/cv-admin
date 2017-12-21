<?php
namespace CVAdmin\CV\Repos;

use CVAdmin\CV\Models\CLocal;
use CVAdmin\CV\Models\Correspondencia;
use CVAdmin\CV\Models\CorrespondenciaHistorial;
use CVAdmin\Common\Repos\SessionRepo;
use CVAdmin\Common\Repos\QueryRepo;

use CVAdmin\Traits\MailTrait;

class CorrespondenciaRepo{

    use MailTrait;

	public function search( $anio, $mes, $estado, $getparams ){
		$getparams["anio"] = $anio;
		$getparams["mes"] = $mes;
		$getparams["estado"] = $estado;
		return ( new QueryRepo )->Q_correspondencia( $getparams );
	}

    public function report( $anio, $mes, $getparams ){
        $getparams["anio"] = $anio;
        $getparams["mes"] = $mes;
        return ( new QueryRepo )->Q_correspondenciaEmpresa( $getparams );
    }

    public function create( $values ) {

        $values['addcc'] = !empty($values['addcc']) ? $values['addcc'] : null;

        $clocal = CLocal::where( "id", $values['local_id'] )->first();

        if( !empty( $clocal ) ){
        	$clocal = $clocal->toArray();
        	$rpta = [];
	        $rpta = Correspondencia::create(
	        	array(
	        		'empresa_id' 		=> $values['empresa_id'], 
	        		'fecha_creacion' 	=> date("Y-m-d H:i:s"), 
	        		'remitente' 		=> $values['remitente'], 
	        		'asunto' 			=> $values['asunto'], 
	        		'creado_por' 		=> \Auth::user()->nombre,
	        		'cc' 				=> $values['addcc'], 
	        		'lugar' 			=> $clocal["nombre"], 
	        		'local_id' 			=> $values['local_id']
	         	)
	        );

	        $emailRpta = false;

            $d = (array)(( new SessionRepo )->CallRaw( "mysql", "USP_DATOS_EMPRESA_MENSAJE" ,array( $values['empresa_id'], 'R' ) )[0]);

            try {
                // Envia correspondencia
                $this->sendMail(['email'=>$d['correo'], 'fullname'=>$d['nombre']], 'NUEVA_CORRESPONDENCIA', ['fullname'=>$d['nombre'], 'remitente'=> $values['remitente']] );
                $emailRpta = true;
            } catch (\Exception $e) {
            }

        	return array( "newreg" => $rpta, "emailsend" => $emailRpta, "local" => $clocal["nombre"]);

        } else {

        	return array( "newreg" => [],  "emailsend" => false, "local" => "" );

        }
    }

    public function enviarMensajePlantilla(  $remitente, $destinatario, $newmensaje, $values, $pie = true ) {
    	return ( new SessionRepo )->send( $remitente, $destinatario, $newmensaje, $values, $pie );
    }

    public function update($id, $values){
		$update = ( new SessionRepo )->CallRaw("mysql", "AL_CORRESPONDENCIA_UPDATE", array($id, $values["remitente"], $values["asunto"], $values["local_id"], $values['entregado_a'], \Auth::user()->nombre), true);
    	return $update;
    }

    public function delete($id, $obs)
    {
		$delete = ( new SessionRepo )->CallRaw("mysql", "AL_CORRESPONDENCIA_DELETE", [$id, $obs, \Auth::user()->nombre], true ) ;
    	return $delete;
    }

    public function history( $corresID ){
    	$history = CorrespondenciaHistorial::where( "correspondencia_id", $corresID )->get();
    	return $history;	
    }

    public function deliver( $values ){
        $ids = "";
        $values["corresIDs"] = explode(",", $values["corresIDs"]);
    	foreach( $values["corresIDs"] as $cor ){
            $ids .= $cor."-";
        }
        $result = [];
        $tobase64 = base64_encode( date("YmdHis")."|".$ids );
        $i = 0;
    	foreach( $values["corresIDs"] as $cor ){
    		$result[$i] = ( new SessionRepo )->CallRaw( "mysql", "AL_CORRESPONDENCIA_DESTINATARIO" ,array( $values["entregado_a"], $cor, \Auth::user()->nombre, $tobase64 ), true ) ;
    		$i = $i + 1;
    	}
    	return $result;
    }
}
?>