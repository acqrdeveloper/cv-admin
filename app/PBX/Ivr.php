<?php namespace CVAdmin\PBX;

use Illuminate\Database\Eloquent\Model;

class Ivr extends Model {

	protected $connection = 'pbx';

	protected $table = 'ivr';
	public $timestamps = false;

	public function record(){
		return $this->hasOne('CVAdmin\PBX\Record','id','record_id');
	}

}
