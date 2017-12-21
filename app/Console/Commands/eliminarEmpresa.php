<?php
namespace CVAdmin\Console\Commands;
use Illuminate\Console\Command;
class eliminarEmpresa extends Command{

    protected $signature = 'eliminar:Empresa';
    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(){
        $query = "UPDATE empresa SET preferencia_estado = 'E' WHERE fecha_eliminacion <> '0000-00-00' AND fecha_eliminacion IS NOT NULL AND fecha_eliminacion <= DATE( NOW() ) AND preferencia_estado <> 'E'";
        \DB::update($query);
    }
}
