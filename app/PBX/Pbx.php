<?php

namespace CVAdmin\PBX;

// Models
use DB;
use \Curl\Curl;
use CVAdmin\PBX\Customer;
use CVAdmin\PBX\Extension;
use CVAdmin\PBX\Ivr;
use CVAdmin\PBX\Number;
use CVAdmin\PBX\Option;
use CVAdmin\PBX\Record;

class Pbx {
	
	private $customer_id = null;

	public function activePbx($params){
		Number::where('id', $params['number_id'])->where('customer_id', $params['customer_id'])->update(['state'=>'A']);

		return ['message'=>'Central activada.'];
	}

	public function create($params, $file = null){

		// Check if customer is created
		$c = Customer::find($params['customer_id']);

		if(is_null($c)){
			$c = new Customer();
			$c->id = $params['customer_id'];
			$c->server_name = 'cv_'.str_random(8);
			$c->save();
		}

		$this->setCustomerId($params['customer_id']);


		// Create audio file
		$record_id = null;

		if(!is_null($file)){

			$response = $this->createRecord($file, $params['id'], $params['customer_id'], null);

			$record_id = $response->record_id;
		}

		// Create IVR
		$ivr = $this->createIvr($params, $record_id);


		// Update number info
		try {
			$n = Number::where('id',$params['id'])->whereNull('customer_id')->firstOrFail();

			$n->customer_id = $this->customer_id;
			$n->cid_name = $params['cid_name'];
			$n->destiny_type = 'IVR';
			$n->destiny_id = $ivr->id;

			$n->save();

			return ['message'=>'Central creada', 'ivr_id'=>$ivr->id, 'number'=>$n->number, 'record_id' => $record_id];

		} catch (\Exception $e) {
			throw new \Exception("El número ingresado no existe, o ya fue tomado por otra empresa");			
		}
	}

	public function createIvr($params, $record_id = null){
		$ivr = new Ivr();
		$ivr->label = "IVR";
		$ivr->customer_id = $this->customer_id;

		if(isset($params['description']))
			$ivr->description = $params['description'];

		if(isset($params['song']))
			$ivr->song = $params['song'];


		if(!is_null($record_id)){
			$ivr->record_id = $record_id;
		}

		$ivr->save();

		return $ivr;
	}

	public function createOption($params){

		$destiny_id = -1;

		if($params['destiny_type'] == 'EXTENSION'){
			$customer = Customer::findOrFail($params['customer_id']);

			$anexo = Extension::where('number_id', $params['number_id'])->where('label', $params['label'])->where('customer_id', $params['customer_id'])->first();

			if(is_null($anexo)){
				$anexo = new Extension();
				$anexo->customer_id = $params['customer_id'];
				$anexo->number_id = $params['number_id'];
				$anexo->label = $params['label'];
				$anexo->label_name = $params['label_name'];
				$anexo->accountcode = $customer->server_name . "_" . $params['number_id'] . "_" . $params['label'];
				$anexo->redirect_to = $params['redirect_to'];
				$anexo->redirect_to_ringtime = 60;
				
			} else {
				throw new \Exception("El número de anexo ingresado ya existe");
				/*$anexo->label_name = $params['label_name'];
				$anexo->redirect_to = $params['redirect_to'];*/
			}

			$anexo->save();

			$destiny_id = $anexo->id;
		}

		// Crear opcion
		$opcion = Option::where('ivr_id', $params['ivr_id'])->where('option', $params['option'])->first();

		if(is_null($opcion)){

			Option::create([
				'ivr_id' => $params['ivr_id'],
				'option' => $params['option'],
				'destiny_type' => $params['destiny_type'],
				'destiny_id' => $destiny_id,
				'customer_id' => $params['customer_id']
			]);
		}

		return ['message'=>'Opcion creada'];
	}

	public function deletePbx($params){
		Number::where('id', $params['number_id'])->where('customer_id', $params['customer_id'])->update(['state'=>'I']);

		return ['message'=>'Central eliminada'];
	}

	public function deleteOption($params){
		Option::where('customer_id', $params['customer_id'])->where('ivr_id', $params['ivr_id'])->where('option', $params['option'])->delete();

		return ['message' => 'Opción eliminada'];
	}

	public function filter($params){
		return \DB::connection('pbx')->select("SELECT customer_id AS empresa_id, cid_name as empresa_nombre, `number` AS nombre FROM `number` WHERE `number` LIKE ? OR `cid_name` LIKE ? LIMIT 10", ['%'.$params['value']."%", '%'.$params['value']."%"]);
	}

	public function getFreeNumbers(){
		return Number::whereNull('customer_id')->get(['id','number']);
	}

	public function getNumbers(){
		$numbers = Number::getByCustomer($this->customer_id);

		$results = [];

		foreach($numbers as $number){

			$item = [
				'id' => $number->id,
				'number' => $number->number,
				'cid_name' => $number->cid_name,
				'state' => $number->state,
				'destiny_type' => $number->destiny_type,
				'destiny_id' => $number->destiny_id,
			];

			$destiny = call_user_func( '\CVAdmin\PBX\\' . ucfirst(strtolower($number->destiny_type)) . '::find', $number->destiny_id);

			if(!is_null($destiny)){

				if($destiny instanceof Ivr){
					$item['destiny'] = [
						'id' => $destiny->id,
						'label' => $destiny->label,
						'description' => $destiny->description,
						'song' => $destiny->song,
						'record_id' => $destiny->record_id
					];

					$item['destiny']['options'] = \DB::connection('pbx')->select("SELECT i.ivr_id,i.option,i.destiny_type, e.id AS 'extension_id', e.label, e.label_name, e.redirect_to FROM (SELECT * FROM ivr_option WHERE customer_id = ? AND ivr_id = ?) i LEFT JOIN (SELECT * FROM extension WHERE customer_id = ?) e ON e.id = i.destiny_id", [$this->customer_id, $destiny->id, $this->customer_id]);

				}

			}

			array_push($results, $item);
		}

		return $results;
	}

	public function setCustomerId($id){
		$this->customer_id = $id;
		return $this;
	}

	public function update($params, $file = null){

		$n = null;

		try {
			$n = Number::findOrFail($params['id']);
		} catch (\Exception $e) {
			throw new \Exception("El número no existe");
		}

		$n->cid_name = $params['cid_name'];
		$n->save();

		// Get Ivr
		$i = Ivr::find($n->destiny_id);

		if(isset($params['description']))
			$i->description = $params['description'];

		if(isset($params['song']))
			$i->song = $params['song'];

		$i->save();

		// Save record
		if(!is_null($file)){

			if($params['record_id'] == 'null' || $params['record_id'] == -1){
				$params['record_id'] = null;
			}

			$response = $this->createRecord($file, $params['id'], $params['customer_id'], $params['record_id']);

			// If record_id is null or empty, must create a record in database
			if(is_null($i->record_id) || empty($i->record_id)){
				$i->record_id = $response->record_id;
				$i->save();
			}
		}

		return ['message'=>'Datos actualizados', 'record_id'=>$i->record_id];

	}

	public function createRecord($file, $number_id, $customer_id, $record_id = null){

		$curl = new Curl();

		$url = 'http://noc.ngalax.com/record.' . (is_null($record_id)?'create':'update');

		$curl->post($url, array(
		    'number_id' => $number_id,
		    'customer_id' => $customer_id,
		    'record_id' => $record_id,
		    'content' => base64_encode(file_get_contents($file))
		));

		if($curl->error){
			throw new \Exception("No se pudo cargar el audio: " . $curl->response);
		}

		return $curl->response;
	}

	public function saveRecord($number_id, $customer_id){
		$record = new Record();
		$record->label = "IVR";
		$record->location = '/var/lib/asterisk/sounds/ivr/ogm/' . $customer_id . '/ivr';
		$record->customer_id = $customer_id;
		$record->save();
		return $record->id;
	}

	public function updateOption($params){
		
		$destiny_id = -1;

		// Si es anexo
		if($params['destiny_type'] != 'OPERATOR'){

			$customer = Customer::findOrFail($params['customer_id']);

			$anexo = Extension::where('number_id', $params['number_id'])->where('label', $params['label'])->where('customer_id', $params['customer_id'])->first();

			if(is_null($anexo)){
				$anexo = new Extension();
				$anexo->customer_id = $params['customer_id'];
				$anexo->number_id = $params['number_id'];
				$anexo->label = $params['label'];
				$anexo->label_name = $params['label_name'];
				$anexo->accountcode = $customer->server_name . "_" . $params['number_id'] . "_" . $params['label'];
				$anexo->redirect_to = $params['redirect_to'];
				$anexo->redirect_to_ringtime = 60;
				
			} else {
				$anexo->label_name = $params['label_name'];
				$anexo->redirect_to = $params['redirect_to'];
			}

			$anexo->save();

			$destiny_id = $anexo->id;
		}

		Option::where('ivr_id', $params['ivr_id'])->where('option', $params['option'])->where('customer_id', $params['customer_id'])->update([
				'destiny_type' => $params['destiny_type'],
				'destiny_id' => $destiny_id,
			]);

		return ['message'=>'Opcion actualizada', 'opcion'=>Option::where('ivr_id', $params['ivr_id'])->where('option', $params['option'])->where('customer_id', $params['customer_id'])->first()];
	}
}