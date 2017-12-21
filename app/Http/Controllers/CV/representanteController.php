<?php namespace CVAdmin\Http\Controllers\CV;

use Auth;
use DB;
use CVAdmin\Http\Controllers\Controller;
use CVAdmin\CV\Repos\EmpresaRepo;
use CVAdmin\CV\Repos\RepresentanteRepo;
use CVAdmin\Common\Repos\SessionRepo;

class representanteController extends Controller {

	public $repo = null;

	public function __construct(){
		$this->repo = new RepresentanteRepo;
	}

	public function create($id){
		$params = request()->only(['nombre','apellido','correo','dni','domicilio','telefonos']);
		$params['empresa_id'] = $id;
		try {
			DB::beginTransaction();
			// Obtener empresa
			$empresa = (new EmpresaRepo)->getById($id);

			// Crear empleado
			$this->repo->create($params);

			// Enviar notificacion con Pusher
            ( new SessionRepo )->Notification(['empresa_id'=>$id,'empresa_nombre'=>$empresa->empresa_nombre] ,'agregó un nuevo representante ( ' . $params['nombre'] . ' ) para', Auth::user()->nombre, 'Representante');
            DB::commit();
            // Retornar
            return response()->json(['message'=>'Representante creado.', 'agents'=>$empresa->representantes]);
		} catch (\Exception $e) {
			return response()->json(['message'=>$e->getMessage()], 412);
		}
	}

	public function delete($id, $repr_id){
		try {
			DB::beginTransaction();
			// Obtener empresa
			$empresa = (new EmpresaRepo)->getById($id);

			// Borrar representante
			$nombre = $this->repo->delete($id,$repr_id);

			// Enviar notificacion con Pusher
            ( new SessionRepo )->Notification(['empresa_id'=>$id,'empresa_nombre'=>$empresa->empresa_nombre] ,'eliminó al representante ('.$nombre.') de', Auth::user()->nombre, 'Representante');
            DB::commit();
            // Retornar
            return response()->json(['message'=>'Representante eliminado.', 'agents'=>$empresa->representantes]);
		} catch (\Exception $e) {
			return response()->json(['message'=>$e->getMessage()], 412);
		}
	}

	public function filter(){
		try {
			return response()->json( $this->repo->filter(request()->all()) ); 
		} catch (Exception $e) {
			return response()->json(['error'=>$e->getMessage(), 'module'=>'representante']);
		}
	}

	public function setLogin(){
		$params = request()->only(['empresa_id','id']);
		try {
			DB::beginTransaction();
			// Obtener empresa
			$empresa = (new EmpresaRepo)->getById($params['empresa_id']);
			// Actualizar representantes
			$correo = $this->repo->updateLogin($params);
			// Actualizar login en empresa
			$empresa->preferencia_login = $correo;
			$empresa->save();
			DB::commit();
			// Retorno
            return response()->json(['message'=>'Logeo Actualizado.', 'agents'=>$empresa->representantes]);
		} catch (\Exception $e) {
			return response()->json(['message'=>$e->getMessage()], 412);
		}
	}

	public function update($id, $repr_id){
		$params = request()->only(['nombre','apellido','correo','dni','domicilio','telefonos']);
		$params['empresa_id'] = $id;
		$params['id'] = $repr_id;
		try {
			DB::beginTransaction();
			// Obtener empresa
			$empresa = (new EmpresaRepo)->getById($id);

			// Crear empleado
			$this->repo->update($params);

			// Actualizar preferencia_login de empresa
			$empresa->preferencia_login = $params['correo'];
			$empresa->save();

			// Enviar notificacion con Pusher
            ( new SessionRepo )->Notification(['empresa_id'=>$id,'empresa_nombre'=>$empresa->empresa_nombre] ,'editó el representante ( ' . $params['nombre'] . ' ) de', Auth::user()->nombre, 'Representante');
            DB::commit();
            // Retornar
            return response()->json(['message'=>'Datos del Representante actualizado.', 'agents'=>$empresa->representantes]);
		} catch (\Exception $e) {
			return response()->json(['message'=>$e->getMessage()], 412);
		}
	}

}