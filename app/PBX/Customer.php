<?php namespace CVAdmin\PBX;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model {

	protected $connection = 'pbx';

	protected $table = 'customer';

	public $timestamps = false;

}
