<?php namespace CVAdmin\Http\Controllers\CV;
use CVAdmin\Http\Controllers\Controller;
use Illuminate\Http\Request;
use CVAdmin\CV\Repos\FacturaRepo;

class facturaController extends Controller {


    public function getFacturacionTemporal($empresa_id){
        try {
            $params = request()->all();
            return response()->json( ( new FacturaRepo )->getFacturacionTemporal( $empresa_id ) );
        } catch (\Exception $e) {
            return response()->json(['message'=>$e->getMessage()." - ".$e->getLine()." - ".$e->getFile()], 412);
        }
    }
    public function setFacturacionTemporal($empresa_id){
        try {
            $params = request()->all();
            ( new FacturaRepo )->setFacturacionTemporal(  $params, $empresa_id );
            return response()->json(['message'=>'Item actualizado']);
        } catch (\Exception $e) {
            return response()->json(['error'=>$e->getMessage()." - ".$e->getLine()." - ".$e->getFile()], 412);
        }
    }
    public function deleteFacturacionTemporal( $id, $empresa_id ){
        try {
            (new FacturaRepo)->deleteFacturacionTemporal($id, $empresa_id);
            return response()->json(['message'=>'Item eliminado']);
        } catch (\Exception $e) {
            return response()->json(['error'=>$e->getMessage()." - ".$e->getLine()." - ".$e->getFile()], 412);
        }
    }
    public function facturadoFacturacionTemporal( $id, $empresa_id ){
        try {
            return response()->json( ( new FacturaRepo )->facturadoFacturacionTemporal(  $id, $empresa_id ) );
        } catch (\Exception $e) {
            return response()->json(['message'=>$e->getMessage()." - ".$e->getLine()." - ".$e->getFile()], 412);
        }
    }


    public function changeComprobante( $empresa_id, $facturaID, $comprobante ){
        try {
            return response()->json( ( new FacturaRepo )->changeComprobante( $empresa_id, $facturaID, $comprobante ) );
        } catch (\Exception $e) {
            \Log::error($e);
            return response()->json(['message'=>$e->getMessage()], 412);
        }
    }



    public function createNote($factura_id){
        try {
            $params = request()->all();
            return response()->json( ( new FacturaRepo )->createnota( $params['empresa_id'], $factura_id, $params ) );
        } catch (\Exception $e) {
            return response()->json(['message'=>$e->getMessage()." - ".$e->getLine()." - ".$e->getFile()], 412);
        }
    }

    public function garantiaList( $empresa_id ){
        try{
            return response()->json(( new FacturaRepo )->garantiaList( $empresa_id ));
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }

    public function search( Request $request )
    {
        try{
            $getparams = $request->all();
            return response()->json(( new FacturaRepo )->search( $getparams ));
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }
    
    public function getone($factura_id)
    {
        try{
            return response()->json(( new FacturaRepo )->getone( $factura_id ));
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }

    public function factura_item($factura_id)
    {
        try{
            return response()->json( ( new FacturaRepo )->factura_detalle( $factura_id ) );
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }

    public function factura_anula( $empresa_id, $factura_id )
    {
        try{
            return response()->json( ( new FacturaRepo )->factura_anula( $empresa_id, $factura_id ) );
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }

    public function factura_historial($factura_id)
    {
        try{
            return response()->json( ( new FacturaRepo )->factura_historial( $factura_id ) );
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }

    public function factura_create($empresa_id, Request $request)
    {
        try{
            $params = $request->all();
            return response()->json(( new FacturaRepo )->facturaCreate( $empresa_id, $params ));
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }

    public function factura_update( $empresa_id, $factura_id, Request $request)
    {
        try{
            $params = $request->all();
            return response()->json(( new FacturaRepo )->facturaUpdate( $empresa_id, $factura_id, $params ));
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }




    public function deletepay($pagoID){
        try{
            return response()->json(( new FacturaRepo )->delete_pago( $pagoID ));
        } catch(\Exception $ex) {
            return response()->json(["error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }

    /**
     * Paga una factura
     * @param int $factura_id ID de la factura 
     * @return void
     */
    public function pay($factura_id){
        try{
            $params = request()->all();
            $empresa_id = $params['empresa_id']; unset($params['empresa_id']);
            return response()->json(( new FacturaRepo )->createPago( $empresa_id, $factura_id, $params ));
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }

    /**
     * Paga una factura con la garantia
     * @param int $factura_id ID de la factura
     * @return void
     */
    public function payWithGuarantee($factura_id){
        try{
            $params = request()->all();
            $empresa_id = $params['empresa_id']; unset($params['empresa_id']);
            $return = array();
            foreach( $params["factura_item"] as $item  ){
                $params["factura_item_id"] = $item['id'];
                $params["tipo"] = "GARANTIA";
                $params["deposito_banco"] = "NINGUNO";
                $params["deposito_cuenta"] = "";
                $params["deposito_fecha"] = date("Y-m-d");
                $params["detalle"] = "";
                $params["monto"] = $item['precio'];
                $params["id_pos"] = 0;
                $params["dif_dep_pos"] = 0;
                $params["des_com_pos"] = 0;
                $params["detraccionD"] = 0;
                $params["detraccionE"] = 0;
                $ret = ( new FacturaRepo )->createPago( $empresa_id, $factura_id, $params );
                array_push( $return, $ret );
            }
            return response()->json($return);
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }        
    }

    public function report_pagos( $anio, $mes, Request $request )
    {
        try{
            $getparams = $request->all();
            return response()->json(( new FacturaRepo )->report_pagos( $anio, $mes, $getparams ));
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }

    public function report_facturacion( $anio, $mes, Request $request )
    {
        try{
            $getparams = $request->all();
            return response()->json(( new FacturaRepo )->report_facturacion( $anio, $mes, $getparams ));
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }

    public function facturacion_empresas( $anio, $mes, $ciclo )
    {
        try{
            return response()->json(( new FacturaRepo )->facturacion_empresas( $anio, $mes, $ciclo ));
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }

    public function send_facturame()
    {
        try{
            response()->json(( new FacturaRepo )->send_facturame());
            return \Redirect::to('factura');
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }

    public function comprobantepdf( $receptor_ruc, $documento, $serie, $numero )
    {
        $pdf = ( new FacturaRepo )->comprobantePDF( $receptor_ruc, $documento, $serie, $numero );
        if( $pdf["load"] ){
            $pdf = $pdf["data"]->setPaper('a4', 'portrait')->setWarnings(false)->stream();
        }
        return $pdf;
    }

    public function comprobantepdfdownload( $receptor_ruc, $documento, $serie, $numero )
    {

        $pdf = ( new FacturaRepo )->comprobantePDF( $receptor_ruc, $documento, $serie, $numero );
        if( $pdf["load"] ){
            $pdf = $pdf["data"]->setPaper('a4', 'portrait')->setWarnings(false)->download();
        }
        return $pdf;
    }

    public function payment_detail($factura_id)
    {
        try{
            return response()->json(( new FacturaRepo )->payment_detail( $factura_id ));
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }

    public function facturasend($factura_id)
    {
        try{
            return response()->json(( new FacturaRepo )->facturasend( $factura_id ));
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine()." - ".$ex->getFile() ], 412);
        }
    }

    public function nota_search(Request $request){
        try{
            $getparams = $request->all();
            return response()->json(( new FacturaRepo )->nota_search( $getparams ));
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }

    public function notasend($factura_id)
    {
        try{
            return response()->json(( new FacturaRepo )->notasend( $factura_id ));
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }

    public function emailfacturasend($factura_id)
    {
        try{
            (new FacturaRepo)->emailfacturasend( $factura_id, request()->input('cc') );
            return response()->json(['message'=>'Factura enviada']);
        } catch(\Exception $ex) {
            return response()->json(["error" => $ex->getMessage(), "detail" => $ex->getLine()."  ".$ex->getFile() ], 412);
        }
    }

    public function emailnotasend($factura_id)
    {
        try{
            $cc = request()->input('cc');
            return response()->json(( new FacturaRepo )->emailnotasend( $factura_id, $cc ));
        } catch(\Exception $e) {
            //return response()->json(["error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
            return response()->json(["detail" => $e->getLine()." - ".$e->getFile(), 'error'=>$e->getMessage()], 412);
        }
    }

    public function invoice_update_number($empresa_id, $factura_id){
        try {
            ( new FacturaRepo )->addInvoiceNumber($empresa_id, $factura_id, request()->input('numero') );
            return response()->json(['message'=>'Número de boleta actualizado']);
        } catch (\Exception $e) {
            return response()->json(['message'=>$e->getMessage()], 412 );
        }
    }
}