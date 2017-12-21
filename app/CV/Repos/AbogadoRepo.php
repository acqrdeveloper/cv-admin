<?php namespace CVAdmin\CV\Repos;
use CVAdmin\Http\Controllers\Controller;
use CVAdmin\Common\Repos\QueryRepo;
class AbogadoRepo extends Controller
{
    function getAllData($getparams)
    {
        return ( new QueryRepo )->Q_abogados_casos($getparams);
    }
}