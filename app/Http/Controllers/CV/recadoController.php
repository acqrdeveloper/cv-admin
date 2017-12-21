<?php namespace CVAdmin\Http\Controllers\CV;
use CVAdmin\Http\Controllers\Controller;
use Illuminate\Http\Request;
use CVAdmin\CV\Repos\RecadoRepo;
/*
use CVAdmin\CV\Repos\UsuarioRepo;
*/
class recadoController extends Controller {
    public function search( $anio, $mes, $estado, Request $request ){
        try{
            $getparams = $request->all();
            return response()->json(( new RecadoRepo )->search( $anio, $mes, $estado, $getparams ));
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }
    public function create( Request $request ){
        try{
            $getparams = $request->all();
            return ["load" => true, "data" => ( new RecadoRepo )->addnew( $getparams )];
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }
    public function update($id){
        try{
            \DB::transaction(function() use ($id){
                $params = request()->all();
                ( new RecadoRepo )->update($id, $params);
            });
            return ["load" => true];
        } catch(\Exception $ex) {
            return response()->json(["error" => $ex->getMessage(), "detail" => $ex->getFile() . " " . $ex->getLine()], 412);
        }
    }
    public function delete($id){
        try{
            $obs = request()->input('observacion');
            return ["load" => true, "data" => ( new RecadoRepo )->delete($id, $obs) ];
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getFile()." ".$ex->getLine() ], 412);
        }
    }
    public function getHistory( $id ){
        try{
            return ( new RecadoRepo )->history( $id );
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getFile()." ".$ex->getLine() ], 412);
        }
    }
    public function deliver($id){
        try{
            $getparams = request()->all();
            return ["load" => true, "data" => ( new RecadoRepo )->deliver($id, $getparams)];
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getFile()." ".$ex->getLine() ], 412);
        }
    }/*
    public function report( $anio, $mes, Request $request ){
        try{
            $getparams = $request->all();
            return response()->json(( new RecadoRepo )->report( $anio, $mes, $getparams ));
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }*/
}