<?php namespace CVAdmin\Http\Controllers\CV;
use CVAdmin\Http\Controllers\Controller;
use Illuminate\Http\Request;
use CVAdmin\CV\Repos\CocheraRepo;
class cocheraController extends Controller {
    public function disponibility( $reservaID, $fReserva, $localID, $vhini, $vhfin ){
        try{
            return ( new CocheraRepo )->disponibility( $reservaID, $fReserva, $localID, $vhini, $vhfin );
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }
    
}