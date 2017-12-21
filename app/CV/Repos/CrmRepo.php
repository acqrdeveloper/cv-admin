<?php

namespace CVAdmin\CV\Repos;
use Carbon\Carbon;
use CVAdmin\CV\Models\Crm;
use CVAdmin\Http\Controllers\Controller;
use CVAdmin\Common\Repos\QueryRepo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDOException;

class CrmRepo extends Controller
{


    function getList($getparams)
    {
        return ( new QueryRepo )->Q_crm($getparams);
    }

    function getNotesByEnterprice($getparams)
    {
        return ( new QueryRepo )->Q_crmnota($getparams);
    }
    function changeCrmEstado($p){
        return Crm::where("id",$p["id"])->update( array( "terminado" => $p["terminado"] ) );
    }

    function postCreateNote($p)
    {
        try {
            //$model = new Crm();
            //$model->crm_tipo_id = $p['crm_tipo_id'];
            //$model->empresa_id = $p['empresa_id'];
            //$model->empleado = Auth::user()->nombre;
            //$model->nota = $p['nota'];
            //$model->fecha = Carbon::now()->format('Y-m-d');
            //$model->hora = Carbon::now()->format('H:i:s');
            //$model->fecha_creacion = Carbon::now()->format('Y-m-d H:i:s');
            //$model->usuario_id = Auth::user()->id;
            $save = Crm::create(
                array(
                    'empleado'          => \Auth::user()->nombre,
                    'crm_tipo_id'       => $p['crm_tipo_id'],
                    'empresa_id'        => $p['empresa_id'],
                    'nota'              => $p['nota'],
                    'fecha'             => isset( $p['fecha'] ) ? $p['fecha'] : date("Y-m-d"),
                    'hora'              => isset( $p['hora']  ) && isset( $p['minuto']  ) ? $p['hora'].":".$p['minuto'].":00"  : date("H:i:s"),
                    'fecha_creacion'    => date("Y-m-d H:i:s"),
                    'estado'            => 'A',
                    'archivado_por'     => '',
                    'fecha_archivado'   => '',
                    'visto'             => 'N',
                    'usuario_id'        => \Auth::user()->id,
                    'usuario_asignado_id'   => isset($p["usuario_asignado_id"]) ? $p["usuario_asignado_id"] : \Auth::user()->id
                )
            );
            if ( $save ) {//$model->save()
                $this->rpta = ['load' => true, 'data' => $save, 'msg' => 'nota creada correctamente'];
            }
        } catch (PDOException $e) {
            $this->returnCatch($e);
        }
        return $this->rpta;


    }
}