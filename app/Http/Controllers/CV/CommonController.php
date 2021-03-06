<?php namespace CVAdmin\Http\Controllers\CV;
use CVAdmin\Http\Controllers\Controller;
use Illuminate\Http\Request;
use CVAdmin\Common\Repos\ExportRepo;

class CommonController extends Controller {
    public function export( $modulo, Request $request ){
        try{
            $getparams = $request->all();
            $seaparams = isset($getparams["json"]) ? (array)json_decode( $getparams["json"], true ) : [];
            ( new ExportRepo )->createCSV( $modulo, $seaparams );
            exit();
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }
}