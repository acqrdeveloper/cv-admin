<?php namespace CVAdmin\PBX;

use Illuminate\Database\Eloquent\Model;

class Record extends Model {

	protected $connection = 'pbx';

	protected $table = 'record';

	public $timestamps = false;

}
