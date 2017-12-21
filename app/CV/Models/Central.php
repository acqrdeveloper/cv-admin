<?php namespace CVAdmin\CV\Models;
use Illuminate\Database\Eloquent\Model;
class Central extends Model { 

	protected $table = 'central';

	public $timestamps = false;

	public function opciones(){
		return $this->hasMany('CVAdmin\CV\Models\CentralOpcion', 'central_id', 'id');
	}

	public function empresa(){
		return $this->belongsToMany('CVAdmin\CV\Models\Empresa','empresa_central', 'central_id','empresa_id');
	}
}