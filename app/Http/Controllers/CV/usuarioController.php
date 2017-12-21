<?php
/**
 * Created by PhpStorm.
 * User: aquispe
 * Date: 2017/05/25
 * Time: 11:47
 */

namespace CVAdmin\Http\Controllers\CV;

use CVAdmin\CV\Repos\UsuarioRepo;
use CVAdmin\Http\Controllers\Controller;
use Illuminate\Http\Request;

class usuarioController extends Controller
{
    public function __construct(Request $request, UsuarioRepo $usuarioRepo)
    {
        $this->request = $request;
        $this->repo = $usuarioRepo;
    }

    function changeEstado($id)
    {
        try {
            if ($this->repo->_changeEstado($id, $this->request->all())) {
                return response()->json(['message'=>'Datos guardados.']);
            }
        } catch (\Exception $e) {
            return response()->json(['error'=>$e->getMessage()], 412);
        }
    }

    function listAll()
    {
        $rpta = $this->repo->_listAll($this->request->all());
        if ($rpta['load']) {
            return response()->json($rpta, 200);
        } else {
            return response()->json($rpta, 412);
        }
    }

    function store()
    {
        try {
            if ($this->repo->_store($this->request->all())) {
                return response()->json(['message'=>'Usuario creado.']);
            }
        } catch (\Exception $e) {
            return response()->json(['error'=>$e->getMessage()], 412);
        }
    }

    function update($id)
    {
        try {
            if ($this->repo->_update($id, $this->request->all())) {
                return response()->json(['message'=>'Datos guardados.']);
            }
        } catch (\Exception $e) {
            return response()->json(['error'=>$e->getMessage()], 412);
        }
    }
}