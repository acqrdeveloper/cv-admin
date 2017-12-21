<?php namespace CVAdmin\Http\Controllers\CV;
use CVAdmin\Http\Controllers\Controller;
use Illuminate\Http\Request;
use CVAdmin\CV\Repos\CdrRepo;

class cdrController extends Controller {
    public function report( $anio, $mes, Request $request ){
        try{
            $getparams = $request->all();
            return response()->json(( new CdrRepo )->report( $anio, $mes, $getparams ));
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }
}