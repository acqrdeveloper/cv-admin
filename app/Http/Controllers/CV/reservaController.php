<?php namespace CVAdmin\Http\Controllers\CV;

use DB;
use CVAdmin\Http\Controllers\Controller;
use Illuminate\Http\Request;
use CVAdmin\CV\Repos\ReservaRepo;
class reservaController extends Controller {

    public $repo = null;

    /**
     * Construct method
     * @return void
     */
    public function __construct(){
        $this->repo = new ReservaRepo();
    }

    public function auditorioprecio( $localID, $modeloID, $planID ){
        try{
            $numout = 0;
            return response()->json($this->repo->auditorioprecio( $localID, $modeloID, $planID ));
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }


    /**
     * Establece un estado para una reserva
     * @param int $id Reserva ID
     * @param string $estado estado
     * @return json
     */
    public function changestate($id, $estado){
        try{
            return ["load" => true, "data" => $this->repo->changestate($id, $estado)];
        } catch(\Exception $ex) {
            return response()->json(["load" => true, "error" => $ex->getMessage(), "detail" => $ex->getFile() . " " . $ex->getLine()], 412);
        }        
    }

    /**
     * Crea una reserva
     * @param Illuminate\Http\Request $request Request HTTP
     * @return json
     */
    public function create( Request $request ){
        DB::beginTransaction();
        try{
            $data = $this->repo->create( $request->all() );
            if(!($data['id']>0)){
                throw new \Exception( $data['mensaje'] );
            }
            DB::commit();
            return response()->json($data, 200);
        } catch(\Exception $ex) {
            DB::rollBack();
            return response()->json(["message" => $ex->getMessage()], 412);
        }
    }

    /**
     * Elimina una reservas
     * @param int $id ID de la reserva
     * @return json
     */
    public function delete($id){
        try {

            $this->repo->delete($id);
            return response('', 204);

        } catch (\Exception $e) {
            return response($e->getMessage(), 412);
        }
    }

    /**
     * Obtiene informacion adicional de la reserva (como coffeebreak)
     * @param int $id Id de la reserva
     * @return json
     */
    public function getDetalle( $id ){
        try{
            return response()->json($this->repo->getDetalle($id));
        } catch(\Exception $ex) {
            return response()->json(["message" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }

    /**
     * Obtiene el historial de cambios de la reserva
     * @param int $id Id de la reserva
     * @return json
     */
    public function getHistory($id){
        try {
            return $this->repo->getHistory($id);
        } catch (\Exception $e) {
            return response($e->getMessage(), 412);
        }
    }

    /**
     * Obtiene un listado de reservas
     * @return json
     */
    public function search($fecha = null){
        try{
            $getparams = request()->all();
            return response()->json($this->repo->search($fecha, $getparams));
        } catch(\Exception $ex) {
            return response()->json(["message" => $ex->getMessage(), "detail" => $ex->getLine() ], 412);
        }
    }

    /**
     * Ingresa una observacion a la reserva
     * @param int $id Id de la reserva
     * @return json
     */
    public function updateObs($id){
        try {
            return $this->repo->updateObs($id, request()->input('observacion'));
        } catch (\Exception $e) {
            return response($e->getMessage(), 412);
        }
    }

    /**
     * Actualiza la informacion de la reserva
     * @param int $id Id de la reserva
     * @return json
     */
    public function update($id){
        try{
            $data = $this->repo->update($id,  request()->all());
            if(!($data['id']>0)){
                throw new \Exception( $data['mensaje'] );
            }
            return response()->json($data, 200);
        } catch(\Exception $ex) {
            return response()->json(["message" => $ex->getMessage()], 412);
        }
    }
}