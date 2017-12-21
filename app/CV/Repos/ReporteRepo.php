<?php
/**
 * Created by PhpStorm.
 * User: QuispeRoque
 * Date: 10/04/17
 * Time: 09:57
 */

namespace CVAdmin\CV\Repos;


use CVAdmin\Common\Repos\QueryRepo;
use CVAdmin\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PDOException;

class ReporteRepo extends Controller
{

    protected $rpta = array();

    function filterSearchCorrespondencias($getparams)
    {
        try {
            return (new QueryRepo)->Q_correspondenciaEmpresa($getparams);
        } catch (PDOException $e) {
            return $this->returnCatch($e);
        }
    }

}