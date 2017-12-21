<?php namespace CVAdmin\CV\Repos;

use Carbon\Carbon;
use DB;
use CVAdmin\CV\Models\Representante;
use CVAdmin\CV\Models\Empresa;

class RepresentanteRepo{

	public function create($params){
		
		$agente = new Representante();
		$agente->nombre = $params['nombre'];
		$agente->apellido = $params['apellido'];
		$agente->dni = $params['dni'];
		$agente->correo = $params['correo'];
		$agente->empresa_id = $params['empresa_id'];
		$agente->telefonos = $params['telefonos'];
		$agente->domicilio = $params['domicilio'];
		$agente->is_login = 'N';
		$agente->fecha = Carbon::now()->format('Y-m-d H:i:s');
		$agente->save();
	}

	public function delete($empresa_id, $repre_id){
		
		$agente = Representante::find($repre_id);

		if(is_null($agente))
			throw new \Exception("El representante buscado no existe");

		if($agente->empresa_id != $empresa_id)
			throw new \Exception("El representante no pertenece a esta empresa");

		$nombre = $agente->nombre;
		$agente->delete();
		return $nombre;
	}

	public function filter($params){
		return DB::select('SELECT r.id, r.empresa_id, CONCAT(r.nombre, " ", r.apellido) AS nombre, e.empresa_nombre FROM (SELECT * FROM representante WHERE estado = "A" AND (nombre LIKE ? OR apellido LIKE ?)) r JOIN empresa e ON e.id = r.empresa_id WHERE e.preferencia_estado = "A" LIMIT 10', [$params['value']."%", $params['value']."%"]);
	}

	public function update($params){
		
		$agente = Representante::find($params['id']);

		if(is_null($agente))
			throw new \Exception("El representante buscado no existe");

		if($agente->empresa_id != $params['empresa_id'])
			throw new \Exception("El representante no pertenece a esta empresa");

		$agente->nombre = $params['nombre'];
		$agente->apellido = $params['apellido'];
		$agente->dni = $params['dni'];
		$agente->correo = $params['correo'];
		$agente->telefonos = $params['telefonos'];
		$agente->domicilio = $params['domicilio'];

		$agente->save();
	}

	public function updateLogin($params){
		Representante::where('empresa_id', $params['empresa_id'])->update(['is_login'=>'N']);

		$agente = Representante::find($params['id']);

		if(is_null($agente))
			throw new \Exception("El representante buscado no existe");

		if($agente->empresa_id != $params['empresa_id'])
			throw new \Exception("El representante no pertenece a esta empresa");

		$agente->is_login = 'S';
		$agente->save();

		return $agente->correo;
	}
}