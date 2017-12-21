<?php namespace CVAdmin\Http\Controllers\CV;

use Auth;
use DB;
use CVAdmin\Http\Controllers\Controller;

use CVAdmin\CV\Models\Plan;

class planController extends Controller {

	public function search(){
		$estado = request()->input('estado');
		try {
			return response()->json((new Plan)->getByState($estado));
		} catch (\Exception $e) {
			return response()->json(['message'=>$e->getMessage()],412);
		}
	}

	public function create(){
		$params = request()->all();
		try {
			$params['estado'] = 'A';
			$plan = Plan::create($params);
			return response()->json(['message'=>'Plan creado']);
		} catch (\Exception $e) {
			return response()->json(['message'=>$e->getMessage()],412);
		}
	}

	public function updateStatus($id){
		$estado = request()->input('estado');
		try {

			$plan = Plan::findOrFail($id);
			$plan->estado = ($estado=='A')?'E':'A';
			$plan->save();

			return response()->json(['message'=>'Estado actualizado']);
		} catch (\Exception $e) {
			return response()->json(['message'=>$e->getMessage()],412);
		}
	}

	public function update($id){
		$params = request()->all();
		try {
			$plan = Plan::findOrFail($id);
			$plan->fill( $params );
			$plan->save();
			return response()->json(['message'=>'Plan actualizado']);
		} catch (\Exception $e) {
			return response()->json(['message'=>$e->getMessage()],412);
		}
	}
}