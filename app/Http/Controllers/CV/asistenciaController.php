<?php
namespace CVAdmin\Http\Controllers\CV;
use DB;
use CVAdmin\CV\Repos\AsistenciaRepo;
use Illuminate\Http\Request;
use CVAdmin\CV\Repos\SessionRepo;
class asistenciaController{

    public function search( $tipo ){
        try{
            $params = request()->all();
            return response()->json( ( new AsistenciaRepo )->search( $tipo, $params ) );
        }catch(\Exception $ex){
            return response()->json( [ "load" => true, "error" => $ex->getMessage(), "detail" => $ex->getFile()." - ".$ex->getLine() ], 412 );            
        }
    }

    public function massive( $reserva_id ){
        try{
            $params = request()->all();
            $estructura = $params["estructura"];
            return response()->json( ( new AsistenciaRepo )->massive( $reserva_id, $estructura ) );
        }catch(\Exception $ex){
            return response()->json( [ "load" => true, "error" => $ex->getMessage(), "detail" => $ex->getFile()." - ".$ex->getLine() ], 412 );            
        }
    }

    public function create( $reserva_id, $nuevo ){
        try{
            $params = request()->all();
            return response()->json( ( new AsistenciaRepo )->create( $reserva_id, $params, $nuevo ) );
        }catch(\Exception $ex){
            return response()->json( [ "load" => true, "error" => $ex->getMessage(), "detail" => $ex->getFile()." - ".$ex->getLine() ], 412 );            
        }
    }

    public function asistencia( $reserva_id, $dni ){
        try{
            return response()->json( ( new AsistenciaRepo )->asistencia( $reserva_id, $dni ) );
        }catch(\Exception $ex){
            return response()->json( [ "load" => true, "error" => $ex->getMessage(), "detail" => $ex->getFile()." - ".$ex->getLine() ], 412 );            
        }
    }

    public function delete( $reserva_id, $dni ){
        try{
            return response()->json( ( new AsistenciaRepo )->delete( $reserva_id, $dni ) );
        }catch(\Exception $ex){
            return response()->json( [ "load" => true, "error" => $ex->getMessage(), "detail" => $ex->getFile()." - ".$ex->getLine() ], 412 );            
        }
    }
}