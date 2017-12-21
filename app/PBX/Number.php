<?php namespace CVAdmin\PBX;

use Illuminate\Database\Eloquent\Model;

class Number extends Model {

	protected $connection = 'pbx';

	protected $table = 'number';

	public $timestamps = false;

	public static function getByCustomer($id){
		return self::where('customer_id', $id)->get();
	}
}
