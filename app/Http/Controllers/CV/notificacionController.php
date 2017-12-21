<?php namespace CVAdmin\Http\Controllers\CV;


use DB;
use CVAdmin\Http\Controllers\Controller;
use Illuminate\Http\Request;

class notificacionController extends Controller {

	public function get(){
		return DB::table('notificacion')->where('to','E')->orderBy('id','DESC')->take(10)->get();
	}

	public function read($id){
		DB::table('notificacion')->where('id',$id)->update(['read'=>1]);
		return response('',204);
	}
}