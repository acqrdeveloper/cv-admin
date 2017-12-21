<?php namespace CVAdmin\Http\Controllers\CV;
use CVAdmin\Http\Controllers\Controller;
use Illuminate\Http\Request;
use CVAdmin\CV\Repos\AbogadoRepo;

class abogadoController extends Controller
{
    function getAllData()
    {
        $getparams = request()->all();  
        $rpta = ( new AbogadoRepo )->getAllData( $getparams );
        if ($rpta['load']) {
            return response()->json($rpta, 200);
        } else {
            return response()->json($rpta, 412);
        }
    }
}