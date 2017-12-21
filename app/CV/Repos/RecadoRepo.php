<?php
namespace CVAdmin\CV\Repos;
use CVAdmin\CV\Models\Recado;
use CVAdmin\CV\Models\RecadoHistorial;
use CVAdmin\Common\Repos\SessionRepo;
use CVAdmin\Common\Repos\QueryRepo;
use CVAdmin\CV\Repos\EmpresaRepo;

class RecadoRepo{
	public function search( $anio, $mes, $estado, $getparams )
    {
		$getparams["anio"] = $anio;
		$getparams["mes"] = $mes;
		$getparams["estado"] = $estado;
		return ( new QueryRepo )->Q_recado( $getparams );
	}

    public function getById( $id )
    {
        return Recado::where("id",$id)->first();
    }

    public function addnew( $values )
    {
        $rpta = Recado::create(
            array(
                'empresa_id'           => $values['empresa_id'],
                'fecha_creacion'       => date("Y-m-d H:i:s"),
                'para'                 => $values['para'],
                'contenido_paquete'    => $values['contenido_paquete'],
                'creado_por'           => \Auth::user()->nombre,
                'local_id'                => $values["local_id"],
                'estado'               => 'P',
                'entregado_a'          => '',
                'fecha_entrega'        => '',
                'entregado_por'        => ''
            )
        );
        $empresa = ( new EmpresaRepo )->getById( $values['empresa_id'] );
        $values["id"] = $rpta->id;
        $values["empresa_nombre"] = $empresa["empresa_nombre"];
        ( new SessionRepo )->Notification( $values ,'agregó un nuevo recado para ', \Auth::user()->nombre, 'Recado' );
        return ["load" => true, "data" => $rpta->id, "empresa" => $empresa["empresa_nombre"] ];
    }

    public function update($id, $values)
    {
        $p = $this->getById( $id );
        $empresa = ( new EmpresaRepo )->getById( $p['empresa_id'] );
        $values["empresa_nombre"] = $empresa["empresa_nombre"];
		( new SessionRepo )->CallRaw("mysql", "AL_RECADO_UPDATE", [$id, $values["para"], $values["contenido_paquete"], $values["local_id"], $values['entregado_a'], \Auth::user()->nombre], true );
        ( new SessionRepo )->Notification( $values ,'editó un recado para ', \Auth::user()->nombre, 'Recado' );
    }

    public function delete($id, $obs)
    {
		$delete = ( new SessionRepo )->CallRaw("mysql", "AL_RECADO_DELETE", [$id, $obs, \Auth::user()->nombre], true ) ;
    	return $delete;
    }
    
    public function history( $recadoID ){
    	$history = RecadoHistorial::where( "recado_id", $recadoID )->get();
    	return $history;	
    }

    public function deliver( $id, $values )
    {
        return Recado::where( "id", $id )->update( 
            array(  
                "entregado_a"     => $values["entregado_a"],
                "estado"          => 'E',
                "fecha_entrega"   => date("Y-m-d H:i:s"),
                "entregado_por"   => \Auth::user()->nombre
            ) 
        );
    }
    /*
    public function enviarMensajePlantilla(  $mensaje, $data, $newmensaje, $values, $pie = true ) {
        return ( new SessionRepo )->send( $mensaje, $data, $newmensaje, $values, $pie );
    }*/
}
?>