<?php
namespace CVAdmin\CV\Repos;
use CVAdmin\CV\Models\Configuracion;
use CVAdmin\Common\Repos\SessionRepo;
use CVAdmin\Common\Repos\QueryRepo;
class ConfiguracionRepo{
	public function GetValor( $nombre ){
		return Configuracion::where( "nombre", $nombre )->select("valor")->first();
	}
    public function ComprobanteSgteNumero( $nombre ){
        $config = Configuracion::where( "nombre", $nombre )->first();
        $numero = $config->valor + 1;
        return Configuracion::where( "nombre", $nombre )->update(
            array(
                "valor" => $numero
            )
        );

    }
    public function toggleFacturaMasiva( $flag = true ){
        $status = $flag ? 'S' : 'N';
        Configuracion::where( "nombre", "MASIVE_INVOICE" )->update(
            array(
                "valor" => $status
            )
        );
    }   
}
?>