<?php namespace CVAdmin\CV\Models;
use Illuminate\Database\Eloquent\Model;
class EmpresaExtras extends Model { 
	protected $table = 'empresa_extra';
	protected $fillable = ['empresa_id', 'extra_id', 'estado'];
	public $timestamps = false;
}