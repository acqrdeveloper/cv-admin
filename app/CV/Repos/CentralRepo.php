<?php
/**
 * @author Kevin W. Baylon Huerta <kbaylonh@hotmail.com>
 */

namespace CVAdmin\CV\Repos;

use Auth;
use Carbon\Carbon;
use DB;
use CVAdmin\CV\Models\Central;
use CVAdmin\CV\Models\CentralOpcion;
use CVAdmin\CV\Models\Empresa;
use CVAdmin\CV\Models\EmpresaHistorial;
use CVAdmin\CV\Repos\EmpresaRepo;
use CVAdmin\CV\Models\EmpresaCentral;
use CVAdmin\Common\Repos\SessionRepo;

class CentralRepo{

	public function getCDRs($value){
		return \DB::select('SELECT DISTINCT userfield FROM cdr WHERE userfield LIKE ? LIMIT 8', ['%' . $value .'%']);
	}

	public function getCentrales($empresa_id){

		$sql = "SELECT a.*, c.*, CONCAT('[',GROUP_CONCAT( CONCAT('{\"id\":\"',IFNULL(o.id,''),'\",\"central_id\":\"',IFNULL(o.central_id,''),'\",\"opcion_numero\":\"',IFNULL(o.opcion_numero,''),'\",\"opcion_nombre\":\"',IFNULL(o.opcion_nombre,''),'\",\"anexo_numero\":\"',IFNULL(o.anexo_numero,''),'\",\"anexo_nombre\":\"',IFNULL(o.anexo_nombre,''),'\",\"redireccion\":\"',IFNULL(o.redireccion,''),'\"}')),']') AS 'opciones'
				FROM (
					SELECT central_id, cdr, estado, created_at, created_by FROM empresa_central WHERE empresa_id = ? AND estado = 'A'
				) a LEFT JOIN central c ON c.id = a.central_id
				LEFT JOIN central_opcion o ON o.`central_id` = a.central_id
				GROUP BY a.central_id";

		return DB::select($sql, [$empresa_id]);
	}

	public function addCentrales( $empresa_id, $p ){
		return EmpresaCentral::create(
			array(
				'empresa_id'	=> $empresa_id,
				'central_id'	=> $p['central_id'],
				'cdr'			=> $p['cdr'],
				'created_at'	=> date("Y-m-d H:i:s"),
				'updated_at'	=> date("Y-m-d H:i:s"),
				'created_by'	=> Auth::user()->nombre,
				'updated_by'	=> Auth::user()->nombre
			)
		);
	}

	public function updateCentrales( $empresa_id, $p ){
		return EmpresaCentral::where( "empresa_id", $empresa_id )->where( "central_id", $p["central_id"] )->update(
			array(
				'cdr' 		 => $p['cdr'],
				'updated_at' => date("Y-m-d H:i:s"),
				'updated_by' => Auth::user()->nombre
			)
		);
	}

	public function delete( $central_id ){
		// Quitar numero de la central
		$central = Central::find($central_id);
		$central->numero = "";
		$central->save();

		(new EmpresaCentral)->findByCentral($central_id)->update(['cdr'=>'','estado'=>'I','updated_by'=>Auth::user()->nombre]);
	}



	/**
	 * Crea una nueva central.
	 * @param array $params Datos para crear la central.
	 * @return \CVAdmin\CV\Models\Central Instancia del modelo Central.
	 */
	public function create($params){
		// crea un registro de central
		$central = new Central;
		$central->numero = $params['numero'];
		$central->cancion = $params['cancion'];
		$central->texto = $params['texto'];
		$central->save();

		// crea la relacion empresa-central
		EmpresaCentral::create(
			array(
				'empresa_id'	=> $params['empresa_id'],
				'central_id'	=> $central->id,
				'cdr'			=> $params['cdr'],
				'created_at'	=> date("Y-m-d H:i:s"),
				'updated_at'	=> date("Y-m-d H:i:s"),
				'created_by'	=> Auth::user()->nombre,
				'updated_by'	=> Auth::user()->nombre
			)
		);

		// Se crea un historial para la empresa empresa
		EmpresaHistorial::create([
			'empresa_id' => $params['empresa_id'],
			'estado' => 'N',
			'observacion' => 'CENTRAL CREADA: ' . $params['numero'] . ' -|- ' . $params['cancion'] . ' -|- ' . $params['texto'] . ' -|- ' . $params['cdr'],
			'empleado' => Auth::user()->nombre,
			'fecha' => Carbon::now()->format('Y-m-d H:i:s')
		]);

		return $central;
	}

	/**
	 * Borra una opcion de la central.
	 * @param array $params Datos de la opcion
	 * @return void
	 */
	public function deleteOption($params){
		DB::beginTransaction();
		$opcion = CentralOpcion::where('central_id', $params['central_id'])->where('id', $params['id'])->first();
		$opc_num = $opcion->opcion_numero;
		$ane_num = $opcion->anexo_numero;
		$opcion->delete();
		$empresa= Empresa::where('central_id', $params['central_id'])->first();
		(new SessionRepo)->Notification(['empresa_id'=>$empresa->id, 'empresa_nombre'=>$empresa->empresa_nombre], 'eliminó la opción ' . $opc_num . ' con anexo ' . $ane_num . ' de', Auth::user()->nombre, 'Central');
		DB::commit();
	}

	/**
	 * Obtiene la informacion de la central usando el ID de empresa vinculado a este.
	 * @param int $empresa_id ID de la empresa.
	 * @return \CVAdmin\CV\Models\Central instancia del modelo Central.
	 */
	public function getByCompanyId($empresa_id){
		$empresa = Empresa::find($empresa_id);

		if(is_null($empresa)){
			throw new \Exception("La empresa no existe");
		}

		$central = $empresa->pbx;

		if(is_null($central) || $central->id == 440){
			return ['have'=>'N', 'cdr'=>$empresa->preferencia_cdr];
		}

		return [
			'cancion' => $central->cancion,
			'cdr' => $empresa->preferencia_cdr,
			'have' => $empresa->central,
			'id' => $central->id,
			'numero' => $central->numero,
			'opciones' => $central->opciones,
			'texto' => $central->texto
		];
	}

	/**
	 * Obtiene la informacion de una central usando su ID.
	 * @param id $id 
	 * @return \CVAdmin\CV\Models\Central instancia del modelo Central.
	 * @throws \Exception Dispara una excepcion cuando la central buscada no existe.
	 */
	public function getById($id){
		$central = Central::find($id);

		if(is_null($central))
			throw new \Exception("La empresa no tiene central.");

		return $central;
	}

	public function getCentral($id){

		$central = $this->getById($id);

		return [
			'numero' => $central->numero,
			'cancion' => $central->cancion,
			'texto' => $central->texto,
			'opciones' => $central->opciones
		];
	}

	/**
	 * Crea una nueva opcion en una central.
	 * @param array $params Datos de la nueva opcion.
	 * @return (int|\CVAdmin\CV\Models\Empresa)[] retorna en un arreglo el id de la nueva opcion y la instancia de la empresa.
	 * @throws \Exception Dispara error si el numero de la nueva opcion ya existe.
	 */
	public function newOption($params){
		// Se inicia una transaccion en la BD
		DB::beginTransaction();

		// Se busca la central
		$central = $this->getById($params['central_id']);

		// Buscar opcion
		$existe = $central->opciones()->where('central_id', $params['central_id'])->where('opcion_numero', $params['opcion_numero'])->first();

		if(!is_null($existe)){
			throw new \Exception("El número de opción ya está ingresado.");
		}

		// Se crea la opcion
		$opcion = $central->opciones()->create([
			'opcion_numero' => $params['opcion_numero'],
			'opcion_nombre' => $params['opcion_nombre'],
			'anexo_numero'  => $params['anexo_numero'],
			'anexo_nombre'  => $params['anexo_nombre'],
			'redireccion'   => $params['redireccion']
		]);

		// Creamos un nuevo empleado
		$empresa = $central->empresa()->first();

		if(isset($params['empleado']) && $params['empleado'] == 'on'){
			$empresa->empleados()->create([
				'nombre' => $params['anexo_nombre'],
				'opcion_central_id' => $opcion->id
			]);
		}

		// Se manda un pusher
		(new SessionRepo)->Notification(['empresa_id'=>$empresa->id, 'empresa_nombre'=>$empresa->empresa_nombre], 'agregó opción ' . $params['opcion_numero'] . ' con anexo ' . $params['anexo_numero'] . ' para', Auth::user()->nombre, 'Central');

		// Se guardan los cambios en la BD
		DB::commit();

		return [
			'id' => $opcion->id,
			'central_id'    => $params['central_id'], 
			'opcion_numero' => $params['opcion_numero'],
			'opcion_nombre' => $params['opcion_nombre'],
			'anexo_numero'  => $params['anexo_numero'],
			'anexo_nombre'  => $params['anexo_nombre'],
			'redireccion'   => $params['redireccion']
		];
	}

	/**
	 * Actualiza la informacion de la central
	 * @param array $params Datos nuevos de la central 
	 * @return CVAdmin\CV\Models\Central Instancia del modelo central
	 */
	public function update($params){
		// Se actualiza la info de la central
		$central = Central::find($params['central_id']);
		$central->numero = $params['numero'];
		$central->cancion = $params['cancion'];
		$central->texto = $params['texto'];
		$central->save();

		// Actualizar EmpresaCentral
		$empresaCentral = (new EmpresaCentral)->findByCentral($params['central_id']);
		$empresaCentral->update(['cdr'=> $params['cdr']]);
		$empresa_id = $empresaCentral->first()->empresa_id;

		// Se crea un historial en empresa
		EmpresaHistorial::create([
			'empresa_id' => $empresa_id,
			'estado' => 'N',
			'observacion' => 'CENTRAL EDITADO: ' . $params['numero'] . ' -|- ' . $params['cancion'] . ' -|- ' . $params['texto'] . ' -|- ' . $params['cdr'],
			'empleado' => Auth::user()->nombre,
			'fecha' => Carbon::now()->format('Y-m-d H:i:s')
		]);
	}

	/**
	 * Actualiza la configuracion de una central
	 * @param array $params datos nuevos de la central
	 * @return (int|boolean)[] Devuelve el id de la central y si fue editado o no
	 */
	public function updateConfig($params){
		DB::beginTransaction();
		// Si existe el id de la central, se modificara
		if(isset($params['id']) && $params['id'] > 0){
			$central = $this->update($params);
		// Caso contrario, se creara la central
		} else {
			$central = $this->create($params);
		}
		DB::commit();
		return ['id'=>$central->id, 'edit' => (isset($params['id']) && $params['id'] > 0)];
	}

	/**
	 * Actualiza la informacion de una opcion de una central
	 * @param array $params datos de la opcion
	 * @return void
	 */
	public function updateOption($params){
		// Iniciar transaccion
		DB::beginTransaction();

		// obtenemos la central
		$central = $this->getById($params['central_id']);

		// Obtenemos la opcion
		CentralOpcion::where('id', $params['id'])->update([
			'opcion_numero' => $params['opcion_numero'],
			'opcion_nombre' => $params['opcion_nombre'],
			'anexo_numero' => $params['anexo_numero'],
			'anexo_nombre' => $params['anexo_nombre'],
			'redireccion' => $params['redireccion']
		]);

		// Creamos un historial en empresa
		$empresa = $central->empresa()->first();

		$empresa->historial()->create([
			'estado' => 'N',
			'observacion' => 'CENTRAL OPCIÓN (EDITADO): '.$params['opcion_numero'].' -|- '.$params['opcion_nombre'].' -|- '.$params['anexo_numero'].' -|- '.$params['anexo_nombre'].' -|- '. $params['redireccion'],
			'empleado' => Auth::user()->nombre,
			'fecha' => Carbon::now()->format('Y-m-d H:i:s')
		]);

		// Guardar cambios en DB
		(new SessionRepo)->Notification(['empresa_id'=>$empresa->id, 'empresa_nombre'=>$empresa->empresa_nombre], 'editó la opción ' . $params['opcion_numero'] . ' con anexo ' . $params['anexo_numero'] . ' de', Auth::user()->nombre, 'Central');
		DB::commit();

		return [
			'id' => $params['id'],
			'central_id' => $params['central_id'],
			'opcion_numero' => $params['opcion_numero'],
			'opcion_nombre' => $params['opcion_nombre'],
			'anexo_numero' => $params['anexo_numero'],
			'anexo_nombre' => $params['anexo_nombre'],
			'redireccion' => $params['redireccion']
		];
	}
}
?>