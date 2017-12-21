<?php namespace CVAdmin\CV\Models;
use Illuminate\Database\Eloquent\Model;
class Recado extends Model { 
	//protected $connection = 'centros';
	protected $table = 'recado';
	protected $fillable = [
		'id',				'empresa_id',		'para',					'contenido_paquete',
		'entregado_a',		'creado_por',		'fecha_creacion',		'estado',
		'fecha_entrega',	'entregado_por',	'local_id'
	];
	public    $timestamps = false;
	protected $hidden     = [];
}