<?php
namespace CVAdmin\CV\Repos;
use DB;
use CVAdmin\CV\Models\Empleado;
use CVAdmin\CV\Models\Empresa;

class EmpleadoRepo{

	public function create($params){
		
		$empleado = new Empleado();
		$empleado->nombre = $params['nombre'];
		$empleado->apellido = $params['apellido'];
		$empleado->dni = $params['dni'];
		$empleado->correo = $params['correo'];
		$empleado->empresa_id = $params['empresa_id'];
		$empleado->save();
	}

	public function delete($empresa_id, $empleado_id){
		
		$empleado = Empleado::find($empleado_id);

		if(is_null($empleado))
			throw new \Exception("El empleado buscado no existe");

		if($empleado->empresa_id != $empresa_id)
			throw new \Exception("El empleado no pertenece a esta empresa");

		$empleado->delete();
	}

	public function filter($params){
		return DB::select('SELECT r.id, r.empresa_id, CONCAT(r.nombre, " ", r.apellido) AS nombre, e.empresa_nombre FROM (SELECT * FROM empresa_empleados WHERE estado = "A" AND (nombre LIKE ? OR apellido LIKE ?)) r JOIN empresa e ON e.id = r.empresa_id LIMIT 10', [$params['value']."%", $params['value']."%"]);
	}

	public function update($params){
		
		$empleado = Empleado::find($params['id']);

		if(is_null($empleado))
			throw new \Exception("El empleado buscado no existe");

		if($empleado->empresa_id != $params['empresa_id'])
			throw new \Exception("El empleado no pertenece a esta empresa");

		$empleado->nombre = $params['nombre'];
		$empleado->apellido = $params['apellido'];
		$empleado->dni = $params['dni'];
		$empleado->correo = $params['correo'];

		$empleado->save();
	}
}