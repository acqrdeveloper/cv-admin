<?php namespace CVAdmin\CV\Models;
use Illuminate\Database\Eloquent\Model;
class Cochera extends Model { 
	protected $table = 'cochera';
	protected $fillable = ['id', 'nombre', 'estado', 'lugar'];
	public    $timestamps = false;
	protected $hidden     = [];
}