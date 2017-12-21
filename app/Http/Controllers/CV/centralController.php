<?php namespace CVAdmin\Http\Controllers\CV;
use CVAdmin\Http\Controllers\Controller;
use Illuminate\Http\Request;
use CVAdmin\CV\Repos\EmpresaRepo;
use CVAdmin\CV\Repos\CentralRepo;
use CVAdmin\CV\Repos\SessionRepo;

class centralController extends Controller {

	public function addOption(){
		try {
			$opcion = ( new CentralRepo )->newOption( request()->all() );
			return response()->json(['message'=>'Opción creada', 'edit'=>false, 'opcion'=>$opcion]);
		} catch (\Exception $e) {
			return response($e->getMessage(), 412);
		}
	}

	/**
	 * Crea una nueva central telefonica para una empresa
	 * @return json
	 */
	public function create(){
		\DB::beginTransaction();
		try {
			$central = (new CentralRepo)->create(request()->all());
			\DB::commit();
			return response()->json(['message'=>'Central creada.', 'central'=>$central]);
		} catch (\Exception $e) {
			\DB::rollBack();
			return response()->json(['error'=>$e->getMessage(), 'code'=>$e->getCode()], 412);	
		}
	}

	public function delete($central_id){
		\DB::beginTransaction();
		try {
			$central = (new CentralRepo)->delete($central_id);
			\DB::commit();
			return response()->json(['message'=>'Central eliminada']);
		} catch (\Exception $e) {
			\DB::rollBack();
			return response()->json(['error'=>$e->getMessage(), 'code'=>$e->getCode()], 412);	
		}
	}

	public function deleteOption($central_id, $id){
		try {
			$params = ['central_id'=>$central_id, 'id'=>$id];
			( new CentralRepo )->deleteOption($params);
			return response()->json(['message'=>'Opción eliminada.']);
		} catch (\Exception $e) {
			return response($e->getMessage(), 412);
		}
	}

	public function getCDRs(){
		try {
			$value = request()->input('value');
			return ( new CentralRepo )->getCDRs($value);
		} catch (\Exception $e) {
			return response($e->getMessage(), 412);
		}
	}

	public function getCentrales($empresa_id){
		try {
			return response()->json( (new CentralRepo)->getCentrales($empresa_id) );
		} catch (\Exception $e) {
			return response($e->getMessage(), 412);
		}
	}

	/**
	 * Acutaliza los datos de una central
	 * @return json
	 */
	public function update(){
		\DB::beginTransaction();
		try {
			(new CentralRepo)->update(request()->all());
			\DB::commit();
			return response()->json(['message'=>'Central editada.']);
		} catch (Exception $e) {
			\DB::rollBack();
			return response()->json(['error'=>$e->getMessage(), 'code'=>$e->getCode()],412);
		}
	}

	public function getPbx($empresa_id){
		try {
			return response()->json( (new CentralRepo)->getByCompanyId($empresa_id) );
		} catch (\Exception $e) {
			return response($e->getMessage(), 412);
		}
	}

	public function updateConfig(){
		try {
			$response = ( new CentralRepo )->updateConfig(request()->all());
			$response['message'] = 'Central actualizada.';
			return response()->json($response);
		} catch (\Exception $e) {
			return response($e->getMessage(), 412);
		}
	}

	public function updateOption(){
		try {
			$opcion = ( new CentralRepo )->updateOption(request()->all());
			return response()->json(['edit'=>true,'message'=>'Opción actualizada.','opcion'=>$opcion]);
		} catch (\Exception $e) {
			return response($e->getTraceAsString(), 412);
		}
	}
}