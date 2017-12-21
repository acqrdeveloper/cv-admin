<?php namespace CVAdmin\CV\Models;
use Illuminate\Database\Eloquent\Model;
class RecursoPeriodoHistorial extends Model {
	protected $table = 'recurso_periodo_historial';
	protected $fillable = [
		'empresa_id', 'anio', 'mes', 'reserva_id', 
		'fecha', 'horas_reunion', 
		'horas_privada', 'horas_capacitacion', 'estado', 'usuario', 
		'cantidad_copias', 'cantidad_impresiones'
	];
	public    $timestamps = false;
	protected $hidden     = [];
}


