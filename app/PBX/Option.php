<?php namespace CVAdmin\PBX;

use Illuminate\Database\Eloquent\Model;

class Option extends Model {

	protected $connection = 'pbx';

	protected $table = 'ivr_option';

	protected $fillable = ['ivr_id', 'option', 'destiny_type', 'destiny_id', 'customer_id'];

	public $timestamps = false;

}
