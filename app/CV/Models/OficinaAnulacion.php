<?php namespace CVAdmin\CV\Models;
use Illuminate\Database\Eloquent\Model;
class OficinaAnulacion extends Model { 
	//protected $connection = 'centros';
	protected $table = 'oficina_anulacion';
	protected $fillable = ['oficina_id', 'hini', 'hfin', 'dia', 'empresa_id'];
	public    $timestamps = false;
	protected $hidden     = [];
}