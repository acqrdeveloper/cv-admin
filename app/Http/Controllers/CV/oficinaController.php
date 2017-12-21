<?php namespace CVAdmin\Http\Controllers\CV;
use CVAdmin\Http\Controllers\Controller;
use Illuminate\Http\Request;
use CVAdmin\CV\Repos\OficinaRepo;
use CVAdmin\CV\Repos\ReservaRepo;
class oficinaController extends Controller {

    public function ofiAuditorioCoffeeBreak($empresa_id){
        try{
            return response()->json( ( new OficinaRepo )->ReservaAuditorio_CoffeeBreak( array( "empresa_id" => $empresa_id ) ) );
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }

    public function ofiAnulaList(){
        try{
            $params = request()->all();
            return response()->json( ( new OficinaRepo )->ofiAnulaList( $params ) );
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }
    
    public function ofiAnulaCreate(){
        try{
            $params = request()->all();
            return response()->json( ( new OficinaRepo )->ofiAnulaCreate( $params ) );
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }

    public function ofiAnulaDelete(){
        try{
            $params = request()->all();
            return response()->json( ( new OficinaRepo )->ofiAnulaDelete( $params ) );
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }

    public function disponibility(){
        try{
        	$fecha = request()->input('fecha');
            $oficinaID = request()->input('oficina_id');
            $reservaID = is_null( request()->input('reserva_id') ) ? 0 : request()->input('reserva_id');
            return response()->json(( new OficinaRepo )->disponibility( $fecha, $oficinaID, $reservaID ));
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }

    public function getSpaceByTime(){
        $code = 200;
        try {
            $params = request()->all();
            /**
             * local_id
             * modelo_id
             * fecha
             * hini
             * hfin
             **/
            //( new ReservaRepo )->auditorioprecio( $params['local_id'], $params['modelo_id'], $planID );
            $reservaID = isset($params['reserva_id']) ? $params['reserva_id'] : 0;
            $response = \DB::select('CALL AL_OFICINA_DISPONIBILIDAD_LISTA(?,?,?,?,?,?)', [$params['local_id'], $params['modelo_id'], $params['fecha'], $params['hini'], $params['hfin'], $reservaID ]);
        } catch (\Exception $e) {
            $response = ['message'=>$e->getMessage()];
            $code = 500;
        }

        return response()->json($response, $code);
    } 

    public function getOficinaList(Request $request){
        try{
            $getparams = $request->all();
            return response()->json(( new OficinaRepo )->getOficinaList( $getparams ));
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }

    public function getCoworking( $local_id ){
        try{
            return response()->json(( new OficinaRepo )->getCoworking( $local_id ));
        } catch(\Exception $ex) {
            return response()->json(["load" => false, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }

    public function putCoworking( $oficina_id, $empresa_id ){
        try{
            ( new OficinaRepo )->putCoworking( $oficina_id, $empresa_id );
            return response()->json(['load'=>true]);
        } catch(\Exception $ex) {
            return response()->json(["load" => false, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }
    
}