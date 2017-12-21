<?php namespace CVAdmin\CV\Models;
use Illuminate\Database\Eloquent\Model;
class EmpresaCentral extends Model { 
	protected $table = 'empresa_central';
	protected $fillable = ['empresa_id', 'central_id', 'cdr', 'created_at', 'updated_at', 'created_by', 'updated_by'];
	public $timestamps = false;

	public function findByCentral($central_id){
		return $this->where('central_id', $central_id);
	}
}