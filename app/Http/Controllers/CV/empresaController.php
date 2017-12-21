<?php namespace CVAdmin\Http\Controllers\CV;

use DB;
use CVAdmin\Http\Controllers\Controller;
use CVAdmin\CV\Repos\EmpresaRepo;
use CVAdmin\Common\Repos\PDFRepo;
use Illuminate\Http\Request;

class empresaController extends Controller {

    public function contratoPDF($empresa_id){
        try {
        	return (new PDFRepo)->render($empresa_id);
        } catch (\Exception $e) {
            return response()->json(['message'=>$e->getMessage()." - ".$e->getLine()." - ".$e->getFile()], 412);
        }        
    }

    public function activarAhoraProrateo($empresa_id){
		try {
			return response()->json( ( new EmpresaRepo )->activarAhoraProrateo( $empresa_id ) );
		} catch (\Exception $e) {
			return response()->json(['message'=>$e->getMessage()], 412);
		}    	
    }

	public function getExtras( $empresa_id ){
		try {
			return response()->json( ( new EmpresaRepo )->getExtras( $empresa_id ) );
		} catch (\Exception $e) {
			return response()->json(['message'=>$e->getMessage()], 412);
		} 
	}

	public function postExtras( $empresa_id ){
		try {
			return response()->json( ( new EmpresaRepo )->postExtras( $empresa_id, request()->all() ) );
		} catch (\Exception $e) {
			return response()->json(['message'=>$e->getMessage()], 412);
		} 
	}
	
	public function create(){
    	DB::beginTransaction();
		try {
			$r = (new EmpresaRepo)->create( request()->all() );
			DB::commit();
            return response()->json($r);
		} catch (\Exception $e) {
		    DB::rollBack();
			return response()->json(['error'=>$e->getMessage()." ".$e->getLine()." ".$e->getFile()], 412);
		} 
	}

	public function testProrrateo( $empresa_id ){
		try {
			return response()->json( (new EmpresaRepo)->testProrrateo( $empresa_id ) );
		} catch (\Exception $e) {
			return response()->json(['message'=>$e->getMessage()], 412);
		} 
	}


	public function getDeuda($empresa_id, $estado ){
		try {
			return response()->json( (new EmpresaRepo)->getDeuda( $empresa_id, $estado ) );
		} catch (\Exception $e) {
			return response()->json(['message'=>$e->getMessage()], 412);
		} 
	}

	public function changeCiclo($empresa_id){
		try {
			$params = request()->all();
			(new EmpresaRepo)->changeCiclo($empresa_id, $params);
			return response()->json(['message'=>'Ciclo de facturación actualizado']);
		} catch (\Exception $e) {
			return response()->json(['message'=>$e->getMessage()], 412);
		}
	}

   	public function changeComprobante($empresa_id){
		try {
			$params = request()->all();
			(new EmpresaRepo)->changeComprobante($empresa_id, $params);
			return response()->json(['message'=>'Comprobante actualizado']);
		} catch (\Exception $e) {
			return response()->json(['message'=>$e->getMessage()], 412);
		}
	}

	public function getFacturaItemGarantia( $id ){
		try {
			return (new EmpresaRepo)->getFacturaItemGarantia( $id );
		} catch (\Exception $e) {
			return response()->json(['message' => $e->getMessage()], 412);
		}
	}

	public function getEmpresaRecursoPeriodoHoras( $empresa_id, $ciclo ){
		try {
			return (new EmpresaRepo)->getEmpresaRecursoPeriodoHoras( $empresa_id, $ciclo );
		} catch (\Exception $e) {
			return response()->json(['message' => $e->getMessage()], 412);
		}
	}

	public function getEmpresaServicio( $empresa_id ){
		try {
			return (new EmpresaRepo)->getEmpresaServicio( $empresa_id );
		} catch (\Exception $e) {
			return response()->json(['message' => $e->getMessage()], 412);
		}
	}
	public function postEmpresaServicio( $empresa_id, $params ){
		try {
			return (new EmpresaRepo)->postEmpresaServicio( $empresa_id, $params );
		} catch (\Exception $e) {
			return response()->json(['message' => $e->getMessage()], 412);
		}
	}

	/**
	 * Edita el contrato de una empresa, puede llevar a cambiar el plan, modificar la fecha del contrato o cambiar el estado a PENDIENTE
	 * @param int $empresa_id ID de la empresa
	 * @return void
	 */
	public function editContract($empresa_id){
		try {
			$params = request()->all();
			$plan_src = $params['plan_src'];
			$plan_dst = $params['plan_dst'];
			unset($params['plan_src'], $params['plan_dst']);
			return (new EmpresaRepo)->putEmpresaPlanCambio($empresa_id, $plan_src, $plan_dst, $params);
		} catch (\Exception $e) {
			return response()->json(['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 412);
		}
	}	



	public function renovarEmpresaContrato($empresa_id){
		try {
			$params = request()->all();
			return (new EmpresaRepo)->renovarEmpresaContrato($empresa_id, $params);
		} catch (\Exception $e) {
			return response()->json(['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 412);
		}
	}	




	public function putEmpresaServicio( $empresa_id, $id, $params ){
		try {
			return (new EmpresaRepo)->putEmpresaServicio( $empresa_id, $id, $params );
		} catch (\Exception $e) { 
			return response()->json(['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 412);
		}
	}
	public function deleteEmpresaServicio( $empresa_id, $id ){
		try {
			return (new EmpresaRepo)->deleteEmpresaServicio( $empresa_id, $id );
		} catch (\Exception $e) {
			return response()->json(['message' => $e->getMessage()], 412);
		}
	}

	public function basic($id){
		try {
			return (new EmpresaRepo)->basic($id);
		} catch (\Exception $e) {
			return response()->json(['message' => $e->getMessage()], 412);
		}
	}

	public function updateplan( $empresa_id, Request $request ){
		try {
		$params = request()->all();
			return (new EmpresaRepo)->updatePlan( $empresa_id, $params );
		} catch (\Exception $e) {
			return response()->json(['message' => 'Error al actualizar Plan de la empresa', 'details' => $e->getMessage()]);
		}
	}
	public function search(){
		$params = request()->all();
		try {
			return (new EmpresaRepo)->get($params);
		} catch (\Exception $e) {
			return response()->json(['message' => 'Error al obtener las empresas', 'details' => $e->getMessage()]);
		}
	}

	public function getCalls($id){
		$params = request()->all();
		try {
			return (new EmpresaRepo)->getCalls( $id, $params );
		} catch (\Exception $e) {
			return response()->json(['message' => $e->getMessage()], 412);
		}
	}

	public function getCentral($id){
		try {
			return (new EmpresaRepo)->getCentral($id);
		} catch (\Exception $e) {
			return response($e->getMessage(), 412);
		}
	}
	
	public function updateEmpresa($id){
		try {
			$params = request()->all();
			return (new EmpresaRepo)->updateEmpresa($id, $params);
		} catch (Exception $e) {
			return response()->json(['message'=>'Error al actualizar', 'details'=>$e->getMessage()], 412);
		}

	}

	public function sendCredentials($empresa_id){
		try {

			$emp = (new EmpresaRepo)->getById($empresa_id);

			if(is_null($emp)){
				throw new \Exception("La empresa no existe");
			}

			$cc = request()->input('cc');

			\Mail::send( new \CVAdmin\Mail\Credentials($emp, $cc) );

			return response()->json(['message'=>'Credenciales enviadas']);

		} catch (\Exception $e) {
			return response()->json(['error'=>$e->getMessage()], 412);
		}
	}

	public function scheduleEliminacion($empresa_id){
		try {
			$params = request()->all();
			(new EmpresaRepo)->scheduleEliminacion($empresa_id, $params);
			$m = 'Fecha de eliminación programada';
			if(isset($params['delete']) && $params['delete'] == 1)
				$m = 'Programación cancelada';

			return response()->json(['message'=>$m]);
		} catch (\Exception $e) {
			return response()->json(['message'=>$e->getMessage()], 412);
		}
	}

	public function updateInterview($id){
		try {
			$params = request()->all();
			(new EmpresaRepo)->updateInterview($id, $params);
			if($params['estado'] == 'P')
				return response()->json(['message'=>'Postergado para el ' . $params['fecha']]);
			else
				return response()->json(['message'=>'Actualizado']);

		} catch (Exception $e) {
			return response()->json(['message'=>'Error al actualizar', 'details'=>$e->getMessage()], 412);
		}
	}

	public function updateState($id){
		try {
			$params = request()->all();
			(new EmpresaRepo)->updateState($id, $params);
			return response()->json(['message'=>'Estado actualizado']);
		} catch (\Exception $e) {
			return response()->json(['message'=>$e->getMessage()], 412);
		}
	}

	public function updateCRMState($id){
		try {
			$params = request()->all();
			$crmest = (new EmpresaRepo)->updateCRMState($id, $params);
			return response()->json(['message'=>'Estado CRM actualizado']);
		} catch (\Exception $e) {
			return response()->json(['message'=>$e->getMessage()], 412);
		}
	}

    public function getListTipoHistorial(){
        $rpta = (new EmpresaRepo)->_getListTipoHistorial(request()->all());
        if ($rpta['load']) {
            return response()->json($rpta, 200);
        } else {
            return response()->json($rpta, 412);
        }
    }
    
		
}