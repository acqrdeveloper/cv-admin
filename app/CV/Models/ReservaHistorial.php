<?php namespace CVAdmin\CV\Models;
use Illuminate\Database\Eloquent\Model;
class ReservaHistorial extends Model { 
	//protected $connection = 'centros';
	protected $table = 'reserva_historial';
	protected $fillable = [ 'reserva_id', 'fecha', 'usuario', 'texto' ];
	public    $timestamps = false;
	protected $hidden     = [];
} 