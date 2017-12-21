<?php
namespace CVAdmin\Http\Controllers\CV;
use CVAdmin\Http\Controllers\Controller;
use Illuminate\Http\Request;
use CVAdmin\CV\Repos\CrmRepo;
class crmController extends Controller
{
    function getListUsers()
    {
        $rpta = ( new CrmRepo )->getListUsers();
        if ($rpta['load']) {
            return response()->json($rpta, 200);
        } else {
            return response()->json($rpta, 412);
        }
    }

    function getListPlanes()
    {
        $rpta = ( new CrmRepo )->getListPlanes();
        if ($rpta['load']) {
            return response()->json($rpta, 200);
        } else {
            return response()->json($rpta, 412);
        }
    }

    function getListCrm()
    {

        $rpta = ( new CrmRepo )->getListCrm();
        if ($rpta['load']) {
            return response()->json($rpta, 200);
        } else {
            return response()->json($rpta, 412);
        }
    }

    function getList()
    {
        $getparams = request()->all();        
        $rpta = ( new CrmRepo )->getList($getparams);
        if ($rpta['load']) {
            return response()->json($rpta, 200);
        } else {
            return response()->json($rpta, 412);
        }
        /**/
    }

    function getNotesByEnterprice()
    {
        $getparams = request()->all();     
        $rpta = ( new CrmRepo )->getNotesByEnterprice($getparams);
        if($rpta['load']){
            return response()->json($rpta,200);
        }else{
            return response()->json($rpta,412);
        }
    }

    function changeCrmEstado(){
        $params = request()->all();
        try {
            return ( new CrmRepo )->changeCrmEstado($params);
        } catch (\Exception $e) {
            return response()->json(['message'=>$e->getMessage()],412);
        }

    }

    function postCreateNote()
    {   
        $getparams = request()->all();     
        $rpta = ( new CrmRepo )->postCreateNote($getparams);
        if($rpta['load']){
            return response()->json($rpta,200);
        }else{
            return response()->json($rpta,412);
        }
        
    }
}