<?php namespace CVAdmin\Http\Controllers\CV;
use CVAdmin\Http\Controllers\Controller;
use Illuminate\Http\Request;
use CVAdmin\Common\Repos\QueryRepo;

class dashboardController extends Controller {
    public function initial( Request $request ){
        try{
            $getparams = $request->all();
            $empresa   = ( new QueryRepo )->Q_dashboard_empresasregistradas( $getparams );
            $historial = ( new QueryRepo )->Q_dashboard_empresahistorial( $getparams );
            return response()->json(["load" => true, "data" => [ "empresa" => $empresa, "historial" => $historial ] ]);
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }

    public function empresa( Request $request ){
        try{
            $getparams = $request->all();
            return response()->json(( new QueryRepo )->Q_dashboard_empresasregistradas( $getparams ));
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }

    public function empresaestado( Request $request ){
        try{
            $getparams = $request->all();
            return response()->json(( new QueryRepo )->Q_dashboard_empresahistorial( $getparams ));
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }
}