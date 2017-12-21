<?php
namespace CVAdmin\CV\Repos;
use CVAdmin\CV\Models\Cochera;
use CVAdmin\Common\Repos\SessionRepo;
use CVAdmin\Common\Repos\QueryRepo;
class CocheraRepo{
	public function disponibility( $reservaID, $fReserva, $localID, $vhini, $vhfin ){
		$disponibility = ( new SessionRepo )->CallRaw("mysql", "AL_COCHERA_DISPONIBILIDAD", [ $reservaID, $fReserva, $localID, $vhini, $vhfin ] ) ;
		return $disponibility;
	}
}
?>