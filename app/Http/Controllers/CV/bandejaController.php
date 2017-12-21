<?php
/**
 * User: Gonzalo A. Del Portal
 * Date: 30/05/17
 * Time: 10:34
**/
namespace CVAdmin\Http\Controllers\CV;
use DB;
use CVAdmin\CV\Repos\BandejaRepo;
use Illuminate\Http\Request;
use CVAdmin\CV\Repos\SessionRepo;
class bandejaController{
    function putMessageAction( $empresa_id, $message_id, $response_id)
    {
        try{
            return response()->json( ( new BandejaRepo )->putMessageAction( $empresa_id, $message_id, $response_id ) );
        } catch(\Exception $ex) {
            return response()->json( [ "load" => true, "error" => $ex->getMessage(), "detail" => $ex->getFile()." - ".$ex->getLine() ], 412 );
        }
    }

    function getMyMessages( $tipo_usuario, $id )
    {
        try{
            $params = request()->all();
            return response()->json( ( new BandejaRepo )->getMyMessages( $tipo_usuario, $id, $params ) );
        } catch(\Exception $ex) {
            return response()->json( [ "load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412 );
        }
    }

    function getMyReceivedMessages( $tipo_usuario, $id )
    {
        try{
            $params = request()->all();
            return response()->json( ( new BandejaRepo )->getMyReceivedMessages( $tipo_usuario, $id, $params ) );
        } catch(\Exception $ex) {
            return response()->json( [ "load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412 );
        }
    }

    function getMySendMessages( $tipo_usuario, $id )
    {
        try{
            $params = request()->all();
            return response()->json( ( new BandejaRepo )->getMySendMessages( $tipo_usuario, $id, $params ) );
        } catch(\Exception $ex) {
            return response()->json( [ "load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412 );
        }
    }
    


    function getAllMessages()
    {
        try{
            $params = request()->all();
            return response()->json( ( new BandejaRepo )->getAllMessages( $params ) );
        } catch(\Exception $ex) {
            return response()->json( [ "load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412 );
        }
    }

    function getMessageDetail( $message_id )
    {
        try{
            return response()->json( ( new BandejaRepo )->getMessageDetail( $message_id ) );
        } catch(\Exception $ex) {
            return response()->json( [ "load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412 );
        }
    }

    function postNewMessages()
    {   
        DB::beginTransaction();
        try{
            $params = request()->all();
            $response = ( new BandejaRepo )->postNewMessages( $params );
            DB::commit();
            $response['message'] = 'Mensaje Enviado';
            return response()->json( $response );
        } catch(\Exception $ex) {
            DB::rollBack();
            return response()->json( ["message" => $ex->getMessage(), "detail" => $ex->getLine() ], 412 );
        }
    }

    function putReadMessages( $message_id )
    {
        try{
            return response()->json( ( new BandejaRepo )->putReadMessages( $message_id ) );
        } catch(\Exception $ex) {
            return response()->json( [ "load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412 );
        }
    }
}