<?php
namespace CVAdmin\Http\Controllers\CV;
use CVAdmin\Http\Controllers\Controller;
use CVAdmin\CV\Repos\ServicioRepo;
use Illuminate\Http\Request;
class servicioController extends Controller
{
    public function getRecursoPeriodo( $empresa_id, $anio, $mes )
    {
        try {
            return response()->json( ( new ServicioRepo )->getRecursoPeriodo( $empresa_id, $anio, $mes ) , 200);
        } catch (\Exception $e) {
            return response()->json(['message'=>$e->getMessage()], 412);
        }
    } 

    public function setRecursoHoras( $empresa_id, $anio, $mes, Request $request )
    {
        try {
            $params = $request->all();
            return response()->json( ( new ServicioRepo )->setRecursoHoras( $empresa_id, $anio, $mes, $params ) , 200);
        } catch (\Exception $e) {
            return response()->json(['error'=>$e->getMessage()], 412);
        }
    }

    public function getEmpresaServicio( $empresa_id )
    {
        try{
            return response()->json(( new ServicioRepo )->getEmpresaServicio( $empresa_id ));
        }catch(\Exception $e){
            return response()->json(['message'=>$e->getMessage()], 412);
        }
    }

    public function setEmpresaServicio( $empresa_id, Request $request )
    {
        try{
            $params = $request->all();
            return response()->json(( new ServicioRepo )->setEmpresaServicio( $empresa_id, $params ));
        }catch(\Exception $e){
            return response()->json(['message'=>$e->getMessage()], 412);
        }
    }

    public function deleteEmpresaServicio( $empresa_id, $id )
    {
        try{
            return response()->json(( new ServicioRepo )->deleteEmpresaServicio( $id, $empresa_id ));
        }catch(\Exception $e){
            return response()->json(['message'=>$e->getMessage()], 412);
        }
    }
}