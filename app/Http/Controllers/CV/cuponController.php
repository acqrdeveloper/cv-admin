<?php namespace CVAdmin\Http\Controllers\CV;
use CVAdmin\Http\Controllers\Controller;
use Illuminate\Http\Request;
use CVAdmin\CV\Repos\CuponRepo;

class cuponController extends Controller {
	public function valid( $codigo ){
        try{
            return response()->json(( new CuponRepo )->valid( $codigo ));
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
	}
	
    public function search( Request $request ){
        try{
            $getparams = $request->all();
            return response()->json(( new CuponRepo )->search( $getparams ));
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }
    public function insert( Request $request ){
        try{
            $getparams = $request->all();
            $cupon = ( new CuponRepo )->insert( $getparams );
            return response()->json(['message'=>'Cupón creado.', 'cupon'=>$cupon]);
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }
    public function update( Request $request ){
        try{
            $getparams = $request->all();
            return response()->json(( new CuponRepo )->update( $getparams ));
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }
    public function delete($cupon_id){
        try{
            ( new CuponRepo )->delete( $cupon_id );
            return response()->json(['message'=>'Cupón eliminado']);
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }
}