<?php namespace CVAdmin\Http\Controllers\Pbx;

use CVAdmin\Http\Controllers\Controller;
use Illuminate\Http\Request;

use \Curl\Curl;
use CVAdmin\PBX\Pbx;

class PbxController extends Controller {

	public $repo = null;

	public function __construct(){
		$this->repo = new Pbx();
	}

	public function active(){
		try {
			return response()->json( $this->repo->activePbx(request()->all()) );
		} catch (\Exception $e) {
			return response()->json(['error'=>$e->getMessage()], 412);
		}
	}

	public function deletePbx(){
		try {
			return response()->json( $this->repo->deletePbx( request()->all() ) );
		} catch (\Exception $e) {
			return response()->json(['error'=>$e->getMessage()], 412);
		}
	}

	public function deleteOption(){
		try {
			return response()->json( $this->repo->deleteOption( request()->all() ) );
		} catch (\Exception $e) {
			return response()->json(['error'=>$e->getMessage()], 412);
		}
	}

	public function filter(){
		try {
			return response()->json( $this->repo->filter(request()->all()) ); 
		} catch (Exception $e) {
			return response()->json(['error'=>$e->getMessage(), 'module'=>'representante']);
		}
	}

	public function getFreeNumbers(){
		try {
			return response()->json($this->repo->getFreeNumbers());
		} catch (\Exception $e) {
			return response()->json([]);
		}
	}

	public function getNumbers($empresa_id){
		try {
			return response()->json( $this->repo->setCustomerId($empresa_id)->getNumbers() );			
		} catch (\Exception $e) {
			return response()->json(['error'=>$e->getMessage()],412);
		}
	}

	public function getRecord($customer_id, $id){
		return response( file_get_contents("http://noc.ngalax.com/record/".$customer_id."/".$id) )
				->header('Content-Type','audio/wav');
	}

	public function save(){
		try {

			$params = request()->all();

			$file = null;

			if (request()->hasFile('file')) {
				$file =	request()->file('file')->getRealPath();				
			}

			if( filter_var($params['create'], FILTER_VALIDATE_BOOLEAN) )
				$res = $this->repo->create($params, $file);
			else
				$res = $this->repo->update($params, $file);

			return response()->json($res);

		} catch (\Exception $e) {
			return response()->json(['error'=>$e->getMessage()],412);
		}
	}

	public function saveOption(){
		try {
			
			$params = request()->all();

			if(!filter_var($params['edit'], FILTER_VALIDATE_BOOLEAN)){
				$r = $this->repo->createOption($params);
			} else {
				$r = $this->repo->updateOption($params);
			}

			return response()->json($r);
			
		} catch (\Exception $e) {
			return response()->json(['error'=>$e->getMessage()],412);
		}
	}
}