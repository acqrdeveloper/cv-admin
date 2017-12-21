<?php namespace CVAdmin\CV\Models;
use Illuminate\Database\Eloquent\Model;
class ReservaDetalle extends Model { 
	//protected $connection = 'centros';
	protected $table = 'reserva_detalle';
	protected $fillable = [ 'reserva_id', 'concepto_id', 'precio', 'cantidad', 'created_at', 'factura_id' ];
	public    $timestamps = false;
	protected $hidden     = [];
}