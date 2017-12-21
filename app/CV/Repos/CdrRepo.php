<?php
namespace CVAdmin\CV\Repos;
//use CVAdmin\CV\Models\CorrespondenciaHistorial;
use CVAdmin\Common\Repos\SessionRepo;
use CVAdmin\Common\Repos\QueryRepo;

class CdrRepo{

    public function report( $anio, $mes, $getparams ){
        $getparams["anio"] = $anio;
        $getparams["mes"] = $mes;
        return ( new QueryRepo )->Q_cdrEmpresa( $getparams );
    }
}
?>