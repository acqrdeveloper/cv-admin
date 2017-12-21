<?php
namespace CVAdmin\Console\Commands;
use Illuminate\Console\Command;
use CVAdmin\Common\Repos\QueryRepo;
class convenioMensual extends Command
{
    protected $signature = 'convenio:Mensual';
    protected $description = 'Command description';
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $empresas = ( new QueryRepo )->Q_renovar_servicio_convenio();
        foreach( $empresas as $emp ){
            echo "<pre>";
            print_r( $emp );
            echo "</pre>";
        }
        $query = "UPDATE empresa SET preferencia_estado = 'E' WHERE convenio = 'S' AND preferencia_estado = 'A' AND TIMESTAMPDIFF( MONTH, DATE( CONCAT( YEAR( fecha_creacion ), '-', MONTH( fecha_creacion ), '-01' ) ), DATE( NOW() ) ) > convenio_duration";
        \DB::update($query);
    }
}