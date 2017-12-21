<?php namespace CVAdmin\Http\Controllers\CV;
use CVAdmin\Http\Controllers\Controller;
use Illuminate\Http\Request;
use CVAdmin\CV\Repos\CorrespondenciaRepo;
/*
use CVAdmin\CV\Repos\UsuarioRepo;
*/
class correspondenciaController extends Controller {
    public function search( $anio, $mes, $estado, Request $request ){
        try{
            $getparams = $request->all();
            return response()->json(( new CorrespondenciaRepo )->search( $anio, $mes, $estado, $getparams ));
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }
    public function report( $anio, $mes, Request $request ){
        try{
            $getparams = $request->all();
            return response()->json(( new CorrespondenciaRepo )->report( $anio, $mes, $getparams ));
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }
    public function create( Request $request ){
        try{
            $getparams = $request->all();
            return ["load" => true, "data" => ( new CorrespondenciaRepo )->create( $getparams )];
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }
    public function update($id){
        try{
            $params = request()->all();
            return ["load" => true, "data" => ( new CorrespondenciaRepo )->update($id, $params)];
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getFile() . " " . $ex->getLine()], 412);
        }
    }
    public function delete($id){
        try{
            $obs = request()->input('observacion');
            return ["load" => true, "data" => ( new CorrespondenciaRepo )->delete($id, $obs) ];
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getFile()." ".$ex->getLine() ], 412);
        }
    }
    public function getHistory( $id ){
        try{
            return ( new CorrespondenciaRepo )->history( $id );
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getFile()." ".$ex->getLine() ], 412);
        }
    }
    public function deliver(){
        try{
            $getparams = request()->all();
            return ["load" => true, "data" => ( new CorrespondenciaRepo )->deliver($getparams)];
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getFile()." ".$ex->getLine() ], 412);
        }
    }
}