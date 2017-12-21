<?php namespace CVAdmin\Http\Controllers\CV;
use CVAdmin\Http\Controllers\Controller;
use Illuminate\Http\Request;
use CVAdmin\Common\Repos\QueryRepo;
use CVAdmin\CV\Repos\ReporteRepo;
class reporteController extends Controller
{
    public function reporte( $tipo )
    {
        $params = request()->all();
        $data = call_user_func_array( array( ( new QueryRepo ) ,"Q_reporte_".$tipo ), array( $params ) );
        return $data;
    }

}