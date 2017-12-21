<?php namespace CVAdmin\PBX;

use Illuminate\Database\Eloquent\Model;

class Extension extends Model {

	protected $connection = 'pbx';

	protected $table = 'extension';

	public $timestamps = false;
}
